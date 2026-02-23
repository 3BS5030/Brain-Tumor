<?php

return [
    'service_url' => env('BRAIN_TUMOR_SERVICE_URL', 'http://127.0.0.1:5001'),
    'service_timeout_seconds' => (int) env('BRAIN_TUMOR_SERVICE_TIMEOUT_SECONDS', 120),
    'python_binary' => env('BRAIN_TUMOR_PYTHON', 'python'),
    'model_path' => env('BRAIN_TUMOR_MODEL_PATH', base_path('app/Infrastructure/Prediction/Python/best_model.pth')),
    'image_size' => (int) env('BRAIN_TUMOR_IMAGE_SIZE', 224),
    'class_labels' => array_map('trim', explode(',', env('BRAIN_TUMOR_CLASS_LABELS', 'glioma,meningioma,notumor,pituitary'))),
];
