from __future__ import annotations

import io
import os
from typing import Any
from pathlib import Path

import numpy as np
import torch
import torch.nn as nn
from flask import Flask, jsonify, request
from PIL import Image

# =========================
# GLOBALS
# =========================
MODEL = None
CLASS_LABELS = ['Glioma', 'Meningioma', 'No Tumor', 'Pituitary']
IMAGE_SIZE = 224
BASE_DIR = Path(__file__).resolve().parent


# =========================
# ✅ EXACT TumorClassifier (FROM KAGGLE)
# =========================
class TumorClassifier(nn.Module):
    def __init__(self, num_classes):
        super(TumorClassifier, self).__init__()

        self.features = nn.Sequential(
            nn.Conv2d(3, 16, kernel_size=3, padding=1),
            nn.ReLU(inplace=True),
            nn.MaxPool2d(kernel_size=2, stride=2),

            nn.Conv2d(16, 32, kernel_size=3, padding=1),
            nn.ReLU(inplace=True),
            nn.MaxPool2d(kernel_size=2, stride=2)
        )

        self.classifier = nn.Sequential(
            nn.Linear(32 * 56 * 56, 128),
            nn.ReLU(inplace=True),
            nn.Linear(128, num_classes)
        )

    def forward(self, x):
        x = self.features(x)
        x = x.view(x.size(0), -1)
        x = self.classifier(x)
        return x


# =========================
# UTILS
# =========================
def softmax(x: np.ndarray) -> np.ndarray:
    exp_x = np.exp(x - np.max(x))
    return exp_x / exp_x.sum(axis=-1, keepdims=True)


def prepare_image(image_bytes: bytes, size: int) -> np.ndarray:
    image = Image.open(io.BytesIO(image_bytes)).convert('RGB').resize((size, size))

    image_array = np.asarray(image, dtype=np.float32) / 255.0

    # ✅ SAME normalization as Kaggle
    mean = np.array([0.485, 0.456, 0.406], dtype=np.float32)
    std = np.array([0.229, 0.224, 0.225], dtype=np.float32)
    image_array = ((image_array - mean) / std).astype(np.float32, copy=False)

    chw = np.transpose(image_array, (2, 0, 1))
    return np.expand_dims(chw, axis=0)


# =========================
# LOAD MODEL
# =========================
def load_model(model_path: str):
    resolved_model_path = Path(model_path)

    if not resolved_model_path.is_absolute():
        resolved_model_path = BASE_DIR / resolved_model_path

    if not resolved_model_path.exists():
        raise FileNotFoundError(f'Model not found: {resolved_model_path}')

    device = torch.device('cpu')

    try:
        # TorchScript
        model = torch.jit.load(str(resolved_model_path), map_location=device)
        model.eval()
        print("✅ Loaded TorchScript model")
        return model
    except Exception:
        # state_dict
        state_dict = torch.load(str(resolved_model_path), map_location=device)

        model = TumorClassifier(num_classes=4)
        model.load_state_dict(state_dict)
        model.eval()

        print("✅ Loaded state_dict model")
        return model


# =========================
# PREDICT
# =========================
def predict(image_bytes: bytes) -> dict[str, Any]:
    image_batch = prepare_image(image_bytes, IMAGE_SIZE)

    with torch.no_grad():
        tensor = torch.from_numpy(image_batch).float()
        outputs = MODEL(tensor)

        if isinstance(outputs, (list, tuple)):
            outputs = outputs[0]

        logits = outputs.detach().cpu().numpy()[0]

    probabilities = softmax(logits)
    predicted_index = int(np.argmax(probabilities))

    predicted_label = CLASS_LABELS[predicted_index]

    scores = {
        CLASS_LABELS[i]: float(probabilities[i])
        for i in range(len(CLASS_LABELS))
    }

    return {
        'predicted_label': predicted_label,
        'confidence': float(probabilities[predicted_index]),
        'scores': scores,
    }


# =========================
# FLASK APP
# =========================
def create_app() -> Flask:
    app = Flask(__name__)

    @app.get('/health')
    def health():
        return jsonify({'status': 'ok'})

    @app.post('/predict')
    def predict_endpoint():
        if 'scan' not in request.files:
            return jsonify({'error': 'Missing file field: scan'}), 400

        image_bytes = request.files['scan'].read()

        if not image_bytes:
            return jsonify({'error': 'Empty image'}), 400

        try:
            result = predict(image_bytes)
            return jsonify(result)
        except Exception as e:
            return jsonify({'error': str(e)}), 500

    return app


# =========================
# BOOTSTRAP
# =========================
def bootstrap():
    global MODEL

    model_path = os.getenv('MODEL_PATH', 'best_model.pth')
    MODEL = load_model(model_path)


def main():
    bootstrap()
    app = create_app()
    host = os.getenv('BRAIN_TUMOR_SERVICE_HOST', '127.0.0.1')
    port = int(os.getenv('BRAIN_TUMOR_SERVICE_PORT', '5001'))
    app.run(host=host, port=port)


if __name__ == '__main__':
    main()
