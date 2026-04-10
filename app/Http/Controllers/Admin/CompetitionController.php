<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CompetitionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompetitionSaveRequest;
use App\Models\Competition;
use App\Models\Country;
use App\Models\Season;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CompetitionController extends Controller
{
    public function index(): View
    {
        $competitions = Competition::paginate(Competition::PAGINATION_LIMIT);
        return view('admin.pages.competitions.index', compact('competitions'));
    }

    public function create(): View
    {
        $countries = Country::pluck('name', 'id');
        $types = collect(CompetitionType::cases())
            ->mapWithKeys(fn($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))]);
        return view('admin.pages.competitions.create', compact('countries', 'types'));
    }

    public function store(CompetitionSaveRequest $request): JsonResponse
    {
        $competition = new Competition($request->all());
        $competition->save();
        return response()->json(['message' => 'Competition successfully created!', 'redirect' => true]);
    }

    public function edit(Competition $competition): View
    {
        $countries = Country::pluck('name', 'id');
        $types = collect(CompetitionType::cases())
            ->mapWithKeys(fn($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))]);
        $seasons = $competition->seasons()->paginate(Season::PAGINATION_LIMIT);
        return view('admin.pages.competitions.update', compact('competition', 'countries', 'types', 'seasons'));
    }

    public function update(CompetitionSaveRequest $request, Competition $competition): JsonResponse
    {
        $competition->fill($request->all());
        $competition->save();
        return response()->json(['message' => 'Competition successfully updated!']);
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();
        return redirect()->route('competitions.index');
    }
}
