<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubSaveRequest;
use App\Models\Club;
use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ClubController extends Controller
{
    public function index(): View
    {
        $clubs = Club::paginate(Club::PAGINATION_LIMIT);
        return view('admin.pages.clubs.index', compact('clubs'));
    }

    public function create(): View
    {
        $countries = Country::pluck('name', 'id');
        return view('admin.pages.clubs.create', compact('countries'));
    }

    public function store(ClubSaveRequest $request): JsonResponse
    {
        $club = Club::create($request->validated());

        if ($request->has('names')) {
            foreach ($request->names as $item) {
                $club->names()->create($item);
            }
        }

        $club->save();
        return response()->json(['message' => 'Club successfully created!', 'redirect' => true]);
    }

    public function edit(Club $club): View
    {
        $countries = Country::pluck('name', 'id');
        return view('admin.pages.clubs.update', compact('club', 'countries'));
    }

    public function update(ClubSaveRequest $request, Club $club): JsonResponse
    {
        $club->update($request->validated());
        $club->names()->delete();

        if ($request->has('names')) {
            foreach ($request->names as $item) {
                $club->names()->create($item);
            }
        }

        $club->save();
        return response()->json(['message' => 'Club successfully updated!']);
    }

    public function destroy(Club $club): RedirectResponse
    {
        $club->delete();
        return redirect()->route('clubs.index');
    }
}
