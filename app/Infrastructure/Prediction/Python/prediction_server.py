from __future__ import annotations

import io
import json
import os
from typing import Any

import numpy as np
import torch
import torch.nn as nn
from flask import Flask, jsonify, request
from PIL import Image

MODEL = None
CLASS_LABELS: list[str] = []
IMAGE_SIZE = 150


class BrainTumorCNN(nn.Module):
    def __init__(self) -> None:
        super().__init__()
        self.features = nn.Sequential(
            nn.Conv2d(3, 16, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.MaxPool2d(2, 2),
            nn.Conv2d(16, 32, kernel_size=3, padding=1),
            nn.ReLU(),
            nn.MaxPool2d(2, 2),
        )
        self.classifier = nn.Sequential(
            nn.Linear(32 * 56 * 56, 128),
            nn.ReLU(),
            nn.Linear(128, 4),
        )

    def forward(self, x: torch.Tensor) -> torch.Tensor:
        x = self.features(x)
        x = torch.flatten(x, 1)
        return self.classifier(x)


def softmax(x: np.ndarray) -> np.ndarray:
    exp_x = np.exp(x - np.max(x))
    return exp_x / exp_x.sum(axis=-1, keepdims=True)


def prepare_image(image_bytes: bytes, size: int) -> np.ndarray:
    image = Image.open(io.BytesIO(image_bytes)).convert('RGB').resize((size, size))
    image_array = np.asarray(image, dtype=np.float32) / 255.0
    chw = np.transpose(image_array, (2, 0, 1))
    return np.expand_dims(chw, axis=0)


def load_model(model_path: str) -> Any:
    if not os.path.exists(model_path):
        raise FileNotFoundError(f'Model not found: {model_path}')

    device = torch.device('cpu')
    try:
        model = torch.jit.load(model_path, map_location=device)
        model.eval()
        return model
    except Exception:
        state_dict = torch.load(model_path, map_location=device)
        if not isinstance(state_dict, dict):
            raise RuntimeError('Unsupported .pth format: expected TorchScript or state_dict.')

        model = BrainTumorCNN()
        model.load_state_dict(state_dict, strict=True)
        model.eval()
        return model


def predict(image_bytes: bytes) -> dict[str, Any]:
    image_batch = prepare_image(image_bytes, IMAGE_SIZE)

    with torch.no_grad():
        tensor = torch.from_numpy(image_batch)
        outputs = MODEL(tensor)
        if isinstance(outputs, (list, tuple)):
            outputs = outputs[0]
        logits = outputs.detach().cpu().numpy()[0]

    probabilities = softmax(logits)
    predicted_index = int(np.argmax(probabilities))

    predicted_label = CLASS_LABELS[predicted_index] if predicted_index < len(CLASS_LABELS) else str(predicted_index)

    scores: dict[str, float] = {}
    for idx, score in enumerate(probabilities):
        label = CLASS_LABELS[idx] if idx < len(CLASS_LABELS) else str(idx)
        scores[label] = float(score)

    return {
        'predicted_label': predicted_label,
        'confidence': float(probabilities[predicted_index]),
        'scores': scores,
    }


def create_app() -> Flask:
    app = Flask(__name__)

    @app.get('/health')
    def health() -> Any:
        return jsonify({'status': 'ok'})

    @app.post('/predict')
    def predict_endpoint() -> Any:
        if 'scan' not in request.files:
            return jsonify({'error': 'Missing file field: scan'}), 400

        scan = request.files['scan']
        image_bytes = scan.read()
        if not image_bytes:
            return jsonify({'error': 'Empty image file'}), 400

        try:
            result = predict(image_bytes)
            return jsonify(result)
        except Exception as exc:
            return jsonify({'error': str(exc)}), 500

    return app


def bootstrap() -> None:
    global MODEL, CLASS_LABELS, IMAGE_SIZE

    model_path = os.getenv('BRAIN_TUMOR_MODEL_PATH', os.path.join(os.path.dirname(__file__), 'best_model.pth'))
    IMAGE_SIZE = int(os.getenv('BRAIN_TUMOR_IMAGE_SIZE', '224'))
    CLASS_LABELS = [x.strip() for x in os.getenv('BRAIN_TUMOR_CLASS_LABELS', 'glioma,meningioma,notumor,pituitary').split(',') if x.strip()]

    MODEL = load_model(model_path)


def main() -> None:
    bootstrap()
    app = create_app()
    host = os.getenv('BRAIN_TUMOR_SERVICE_HOST', '127.0.0.1')
    port = int(os.getenv('BRAIN_TUMOR_SERVICE_PORT', '5001'))
    app.run(host=host, port=port)


if __name__ == '__main__':
    main()
