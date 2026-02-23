<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'predicted_class',
        'confidence',
        'raw_scores',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'float',
            'raw_scores' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
