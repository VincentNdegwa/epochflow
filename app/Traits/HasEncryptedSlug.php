<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait HasEncryptedSlug
{
    public function getSlugAttribute(): string
    {
        return Crypt::encrypt($this->id);
    }

    public static function findBySlug(string $slug): ?static
    {
        try {
            $id = Crypt::decrypt($slug);

            return static::find($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}
