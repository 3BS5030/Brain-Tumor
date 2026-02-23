import argparse
import json
import os
import sys

import numpy as np
from PIL import Image


def _softmax(x):
    e_x = np.exp(x - np.max(x))
    return e_x / e_x.sum(axis=-1, keepdims=True)


def _prepare_image(image_path, size):
    image = Image.open(image_path).convert('RGB').resize((size, size))
    image_array = np.asarray(image, dtype=np.float32) / 255.0
    # CHW shape for PyTorch models
    chw = np.transpose(image_array, (2, 0, 1))
    return np.expand_dims(chw, axis=0)


def _predict_with_torchscript(model_path, image_batch):
    import torch

    device = torch.device('cpu')
    model = torch.jit.load(model_path, map_location=device)
    model.eval()

    with torch.no_grad():
        tensor = torch.from_numpy(image_batch)
        outputs = model(tensor)
        if isinstance(outputs, (list, tuple)):
            outputs = outputs[0]
        logits = outputs.detach().cpu().numpy()[0]

    return logits


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--image', required=True)
    parser.add_argument('--model', required=True)
    parser.add_argument('--size', required=True, type=int)
    parser.add_argument('--labels', required=True)
    args = parser.parse_args()

    if not os.path.exists(args.image):
        raise FileNotFoundError(f'Image not found: {args.image}')

    if not os.path.exists(args.model):
        raise FileNotFoundError(f'Model not found: {args.model}')

    labels = json.loads(args.labels)
    image_batch = _prepare_image(args.image, args.size)

    try:
        logits = _predict_with_torchscript(args.model, image_batch)
    except Exception as exc:
        raise RuntimeError(
            'Unable to load .pth as TorchScript. Export/load a TorchScript model for inference.'
        ) from exc

    probabilities = _softmax(logits)
    predicted_index = int(np.argmax(probabilities))

    predicted_label = labels[predicted_index] if predicted_index < len(labels) else str(predicted_index)

    scores = {}
    for idx, score in enumerate(probabilities):
        label = labels[idx] if idx < len(labels) else str(idx)
        scores[label] = float(score)

    result = {
        'predicted_label': predicted_label,
        'confidence': float(probabilities[predicted_index]),
        'scores': scores,
    }

    sys.stdout.write(json.dumps(result))


if __name__ == '__main__':
    main()
