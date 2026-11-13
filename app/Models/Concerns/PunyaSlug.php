<?php

namespace App\Models\Concerns;

use App\Support\SlugKonten;

/**
 * Konten dengan slug otomatis. Model wajib mendefinisikan kolomJudulSlug().
 * Slug dibuat saat konten dibuat; perubahan judul pada draf ditangani controller.
 */
trait PunyaSlug
{
    abstract public function kolomJudulSlug(): string;

    public static function bootPunyaSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = SlugKonten::buat($model->getTable(), (string) $model->{$model->kolomJudulSlug()});
            }
        });
    }

    /** Kunci untuk URL: slug, atau id bila slug belum ada. */
    public function getKunciUrlAttribute(): string
    {
        return $this->slug ?: (string) $this->getKey();
    }
}
