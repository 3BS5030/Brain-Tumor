import argparse
import json
import os
import sys

import numpy as np
from PIL import Image


def softmax(x):
    e_x = np.exp(x - np.max(x))
    return e_x / e_x.sum(axis=-1, keepdims=True)


def prepare_image(image_path, size):
    image = Image.open(image_path).convert('RGB').resize((size, size))

    image_array = np.asarray(image, dtype=np.float32) / 255.0

    # ✅ SAME normalization
    mean = np.array([0.485, 0.456, 0.406], dtype=np.float32)
    std = np.array([0.229, 0.224, 0.225], dtype=np.float32)
    image_array = ((image_array - mean) / std).astype(np.float32, copy=False)

    chw = np.transpose(image_array, (2, 0, 1))
    return np.expand_dims(chw, axis=0)


def predict(model_path, image_batch):
    import torch

    device = torch.device('cpu')

    try:
        model = torch.jit.load(model_path, map_location=device)
        model.eval()
    except Exception:
        raise RuntimeError("Model must be TorchScript for this script")

    with torch.no_grad():
        tensor = torch.from_numpy(image_batch).float()
        outputs = model(tensor)

        if isinstance(outputs, (list, tuple)):
            outputs = outputs[0]

        logits = outputs.detach().cpu().numpy()[0]

    return logits


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--image', required=True)
    parser.add_argument('--model', required=True)
    args = parser.parse_args()

    if not os.path.exists(args.image):
        raise FileNotFoundError(f'Image not found: {args.image}')

    if not os.path.exists(args.model):
        raise FileNotFoundError(f'Model not found: {args.model}')

    image_batch = prepare_image(args.image, 224)

    logits = predict(args.model, image_batch)

    probabilities = softmax(logits)
    predicted_index = int(np.argmax(probabilities))

    labels = ['Glioma', 'Meningioma', 'No Tumor', 'Pituitary']

    result = {
        'predicted_label': labels[predicted_index],
        'confidence': float(probabilities[predicted_index]),
        'scores': {
            labels[i]: float(probabilities[i])
            for i in range(len(labels))
        }
    }

    sys.stdout.write(json.dumps(result))


if __name__ == '__main__':
    main()
