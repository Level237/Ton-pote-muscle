<?php

namespace App\Models;

use App\Support\ExerciseCatalog;
use Database\Factories\ExerciseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'description',
    'image_path',
    'gif_path',
    'video_url',
    'body_parts',
    'target_muscles',
    'secondary_muscles',
    'equipments',
    'instructions',
])]
class Exercise extends Model
{
    /** @use HasFactory<ExerciseFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'body_parts' => 'array',
            'target_muscles' => 'array',
            'secondary_muscles' => 'array',
            'equipments' => 'array',
            'instructions' => 'array',
        ];
    }

    public function imageUrl(): ?string
    {
        return $this->publicUrl($this->image_path);
    }

    public function gifUrl(): ?string
    {
        return $this->publicUrl($this->gif_path);
    }

    public function demoUrl(): ?string
    {
        $url = trim((string) $this->video_url);

        return $url !== '' ? $url : null;
    }

    /**
     * @return list<string>
     */
    public function bodyPartLabels(): array
    {
        return ExerciseCatalog::labels($this->body_parts, ExerciseCatalog::BODY_PARTS);
    }

    /**
     * @return list<string>
     */
    public function equipmentLabels(): array
    {
        return ExerciseCatalog::labels($this->equipments, ExerciseCatalog::EQUIPMENTS);
    }

    private function publicUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
