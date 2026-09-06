<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const int LIMIT_PER_TYPE = 5;

    public function index(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';

        $clubs = Club::query()
            ->where('name', 'like', $like)
            ->with(['attachment', 'primaryCountry:id,name', 'countries:id,name'])
            ->orderBy('name')
            ->limit(self::LIMIT_PER_TYPE)
            ->get()
            ->map(fn (Club $club) => [
                'type' => 'club',
                'label' => 'Club',
                'name' => $club->name,
                'subtitle' => $club->country?->name,
                'image' => $club->attachment?->getFileUrl(),
                'url' => route('club.show', $club->slug),
            ]);

        $countries = Country::query()
            ->where('name', 'like', $like)
            ->with('attachment')
            ->orderBy('name')
            ->limit(self::LIMIT_PER_TYPE)
            ->get()
            ->map(fn (Country $country) => [
                'type' => 'country',
                'label' => 'Country',
                'name' => $country->name,
                'subtitle' => null,
                'image' => $country->attachment?->getFileUrl(),
                'url' => route('country.show', $country->slug),
            ]);

        $competitions = Competition::query()
            ->where('name', 'like', $like)
            ->with('attachment', 'country:id,name')
            ->orderBy('name')
            ->limit(self::LIMIT_PER_TYPE)
            ->get()
            ->map(fn (Competition $competition) => [
                'type' => 'competition',
                'label' => 'Tournament',
                'name' => $competition->name,
                'subtitle' => $competition->country?->name,
                'image' => $competition->attachment?->getFileUrl(),
                'url' => route('competition.show', $competition->slug),
            ]);

        $results = $clubs
            ->concat($countries)
            ->concat($competitions)
            ->sortBy('name')
            ->values();

        return response()->json(['results' => $results]);
    }
}
