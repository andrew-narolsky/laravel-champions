<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountrySaveRequest;
use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CountryController extends Controller
{
    public function index(): View
    {
        $countries = Country::paginate(Country::PAGINATION_LIMIT);
        return view('admin.pages.countries.index', compact('countries'));
    }

    public function create(): View
    {
        return view('admin.pages.countries.create');
    }

    public function store(CountrySaveRequest $request): JsonResponse
    {
        $country = new Country($request->all());
        $country->save();
        return response()->json(['message' => 'Country successfully created!', 'redirect' => true]);
    }

    public function edit(Country $country): View
    {
        return view('admin.pages.countries.update', compact('country'));
    }

    public function update(CountrySaveRequest $request, Country $country): JsonResponse
    {
        $country->fill($request->all());
        $country->save();
        return response()->json(['message' => 'Country successfully updated!']);
    }

    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();
        return redirect()->route('countries.index');
    }
}
