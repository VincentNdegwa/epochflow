<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasEncryptedSlug
{
    public function getSlugAttribute(): string
    {
        return Str::lower(base64_encode($this->id));
    }

    public static function findBySlug(string $slug): ?static
    {
        try {
            $id = base64_decode($slug);
            return static::find($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}
