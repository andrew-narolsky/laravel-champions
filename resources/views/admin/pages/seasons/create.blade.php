@extends('layouts.admin')

@section('title', 'Create new season')
@section('wrapper', 'seasons-page')

@section('content')
    <div class="page-header mb-3">
        <div class="title-wrapper d-flex justify-content-between mb-2">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-calendar-plus"></i>
                </span> Create new season for "{{ $competition->name }}"
            </h3>
            <button type="submit" form="seasons-form" class="btn btn-primary" id="update">Save</button>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('competitions.index') }}">Competitions</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('competitions.edit', $competition) }}">{{ $competition->name }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
    </div>

    @include('admin.pages.seasons._form', ['method' => 'POST', 'action' => route('competitions.seasons.store', $competition)])

@endsection
