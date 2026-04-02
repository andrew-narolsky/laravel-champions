@extends('layouts.admin')

@section('title', 'Update country')
@section('wrapper', 'countries-page')

@section('content')

    <div class="page-header mb-3">
        <div class="title-wrapper mb-2">
            <div class="col-auto d-block">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-account menu-icon"></i>
                    </span> Update "{{ $country->name }}"
                </h3>
            </div>
            <div class="col-auto ms-auto text-end mt-n1">
                <button type="submit" form="countries-form" class="btn btn-primary" id="update">Update</button>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('countries.index') }}">Countries</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Update</li>
            </ol>
        </nav>
    </div>

    @include('admin.pages.countries._form', ['country' => $country, 'method' => 'PUT', 'action' => route('countries.update', $country) ])

@endsection
