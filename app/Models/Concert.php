<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $fillable = ['admin_id', 'name', 'artist', 'description', 'venue', 'date', 'time', 'poster', 'price', 'stock', 'seat_zone', 'status', 'is_featured', 'is_promo'];

    protected function casts(): array
    {
        return ['date' => 'date', 'price' => 'integer', 'stock' => 'integer', 'is_featured' => 'boolean', 'is_promo' => 'boolean'];
    }

    public function getPosterUrlAttribute(): ?string
    {
        if (! $this->poster) {
            return null;
        }

        $publicPoster = 'posters/'.basename($this->poster);

        if (is_file(public_path($publicPoster))) {
            return asset($publicPoster);
        }

        if (Storage::disk('public')->exists($this->poster)) {
            return asset('storage/'.$this->poster);
        }

        return null;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ticketZones()
    {
        return $this->hasMany(TicketZone::class)->orderBy('position');
    }
}
