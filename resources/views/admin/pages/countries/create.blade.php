@extends('layouts.admin')

@section('title', 'Create new country')
@section('wrapper', 'countries-page')

@section('content')

    <div class="page-header mb-3">
        <div class="title-wrapper mb-2">
            <div class="col-auto d-block">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-account menu-icon"></i>
                    </span> Create new country
                </h3>
            </div>
            <div class="col-auto ms-auto text-end mt-n1">
                <button type="submit" form="countries-form" class="btn btn-primary" id="update">Save</button>
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
                <li class="breadcrumb-item active" aria-current="page">Create new country</li>
            </ol>
        </nav>
    </div>

    @include('admin.pages.countries._form', ['method' => 'POST', 'action' => route('countries.store') ])

@endsection
