<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function valueFor(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function putValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function putMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::putValue($key, $value);
        }
    }

    public static function homeBannerSettings(): array
    {
        return ['slots' => static::homeBannerSlots()];
    }

    public static function homeBannerSlots(): array
    {
        $slots = [];

        for ($position = 1; $position <= 3; $position++) {
            $source = static::valueFor("home_banner_{$position}_source");
            $image = static::valueFor("home_banner_{$position}_image");
            $concertId = static::valueFor("home_banner_{$position}_concert_id");
            $title = static::valueFor("home_banner_{$position}_title");
            $link = static::valueFor("home_banner_{$position}_link");

            if ($position === 1 && blank($source)) {
                $source = static::valueFor('home_banner_source', 'auto');
                $image = static::valueFor('home_banner_image');
                $concertId = static::valueFor('home_banner_concert_id');
                $title = static::valueFor('home_banner_title');
                $link = static::valueFor('home_banner_link');
            }

            $slots[$position] = [
                'position' => $position,
                'source' => in_array($source, ['auto', 'custom', 'concert'], true) ? $source : 'auto',
                'image' => $image,
                'image_url' => $image && Storage::disk('public')->exists($image) ? asset('storage/'.$image) : null,
                'concert_id' => $concertId ? (int) $concertId : null,
                'title' => $title,
                'link' => $link,
            ];
        }

        return $slots;
    }
}
