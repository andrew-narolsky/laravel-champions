<?php

namespace App\Traits;

use App\Exceptions\SlugRedirectException;
use App\Models\SlugRedirect;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasSlugRedirects
{
    protected static function bootHasSlugRedirects(): void
    {
        static::updating(function (self $model) {
            $originalSlug = $model->getOriginal('slug');

            if ($model->isDirty('slug') && $originalSlug) {
                SlugRedirect::updateOrCreate(
                    ['redirectable_type' => static::class, 'old_slug' => $originalSlug],
                    ['redirectable_id' => $model->getKey()]
                );
            }
        });
    }

    public function resolveRouteBinding($value, $field = null): self
    {
        $model = static::where($field ?? $this->getRouteKeyName(), $value)->first();

        if ($model) {
            return $model;
        }

        $redirect = SlugRedirect::query()
            ->where('redirectable_type', static::class)
            ->where('old_slug', $value)
            ->first();

        if ($redirect?->redirectable) {
            throw new SlugRedirectException($redirect->redirectable, static::slugRedirectRouteName());
        }

        throw (new ModelNotFoundException())->setModel(static::class, [$value]);
    }

    abstract public static function slugRedirectRouteName(): string;
}
