<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home(Request $request)
    {
        $concerts = $this->concertQuery($request)->limit(3)->get();
        $upcomingConcerts = Concert::query()
            ->where('status', 'aktif')
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->limit(3)
            ->get();
        $featuredConcert = Concert::where('status', 'aktif')->where('is_featured', true)->first() ?? $concerts->first();

        if ($featuredConcert && ! $concerts->contains('id', $featuredConcert->id)) {
            $concerts = collect([$featuredConcert])->merge($concerts)->take(3);
        }

        $bannerItems = $this->homeBannerItems($concerts, $featuredConcert);

        return view('public.home', compact('concerts', 'featuredConcert', 'upcomingConcerts', 'bannerItems'));
    }

    public function concerts(Request $request)
    {
        $concerts = $this->concertQuery($request)->paginate(9)->withQueryString();
        return view('public.concerts', compact('concerts'));
    }

    public function show(Concert $concert)
    {
        $concert->load('ticketZones');
        return view('public.concert-show', compact('concert'));
    }

    private function concertQuery(Request $request)
    {
        return Concert::query()
            ->where('status', 'aktif')
            ->when($request->q, fn ($query, $q) => $query->where(fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('artist', 'like', "%{$q}%")))
            ->when($request->venue, fn ($query, $venue) => $query->where('venue', 'like', "%{$venue}%"))
            ->when($request->date, fn ($query, $date) => $query->whereDate('date', $date))
            ->orderByDesc('is_featured')
            ->latest('date');
    }

    private function homeBannerItems($concerts, ?Concert $featuredConcert)
    {
        $settings = SiteSetting::homeBannerSettings();

        if ($settings['source'] === 'custom' && $settings['image_url']) {
            return collect([[
                'type' => 'custom',
                'title' => $settings['title'] ?: 'Festify',
                'image_url' => $settings['image_url'],
                'url' => $settings['link'] ?: route('concerts.index'),
                'is_promo' => false,
            ]]);
        }

        if ($settings['source'] === 'concert' && $settings['concert_id']) {
            $concert = Concert::where('status', 'aktif')->find($settings['concert_id']);

            if ($concert) {
                return collect([[
                    'type' => 'concert',
                    'title' => $concert->name,
                    'concert' => $concert,
                    'url' => route('concerts.show', $concert),
                    'is_promo' => $concert->is_promo,
                ]]);
            }
        }

        $bannerConcerts = $concerts->take(4);
        if ($featuredConcert && ! $bannerConcerts->contains('id', $featuredConcert->id)) {
            $bannerConcerts = collect([$featuredConcert])->merge($bannerConcerts)->take(4);
        }

        return $bannerConcerts->map(fn (Concert $concert) => [
            'type' => 'concert',
            'title' => $concert->name,
            'concert' => $concert,
            'url' => route('concerts.show', $concert),
            'is_promo' => $concert->is_promo,
        ]);
    }
}
