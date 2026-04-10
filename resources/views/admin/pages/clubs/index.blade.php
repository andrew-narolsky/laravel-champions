@extends('layouts.admin')

@section('title', 'Clubs')
@section('wrapper', 'clubs-page')

@section('content')

    <div class="page-header mb-3">
        <div class="title-wrapper mb-2">
            <div class="col-auto d-block">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-account menu-icon"></i>
                    </span> Clubs
                </h3>
            </div>

            <div class="col-auto ms-auto text-end mt-n1">
                <a href="{{ route('clubs.create') }}" class="btn btn-primary">New club</a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Clubs</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    @if($clubs->isEmpty())
                        <span>No clubs found.</span>
                    @else
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 70px">#</th>
                                <th style="width: 200px">Thumbnail</th>
                                <th style="width: 200px">Name</th>
                                <th>Description</th>
                                <th style="width: 200px; text-align: right">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clubs as $club)
                                <tr>
                                    <td class="py-1">{{ $loop->index + 1 }}.</td>
                                    <td>
                                        @isset($club->attachment)
                                            <img src="{{ $club->attachment?->getFileUrl() }}" alt="{{ $club->name }}" loading="lazy">
                                        @else
                                            <img src="{{ asset('/build/images/shield.svg') }}" alt="{{ $club->name }}" loading="lazy">
                                        @endisset
                                    </td>
                                    <td>
                                        {{ $club->name }}
                                    </td>
                                    <td>
                                        {{ $club->description }}
                                    </td>

                                    <td class="d-flex flex-row justify-content-end">
                                        <a href="{{ route('clubs.edit', $club) }}" type="button" class="btn btn-inverse-info btn-icon">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>

                                        <button type="submit" form="delete-country-{{ $club->id }}" class="btn btn-inverse-danger btn-icon">
                                            <i class="mdi mdi-delete"></i>
                                        </button>

                                        <form method="POST" id="delete-country-{{ $club->id }}" action="{{ route('clubs.destroy', $club) }}">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $clubs->appends(request()->except('page'))->links('admin.partials.pagination') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
