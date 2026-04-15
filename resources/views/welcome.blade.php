@extends('layouts.front')

@section('content')

    <header class="border-bottom mb-4">
        <div class="container py-3">
            @if (Route::has('login'))
                <div class="d-flex justify-content-end align-items-center gap-2">

                    @auth
                        <a href="{{ route('admin') }}" class="btn btn-outline-primary">
                            Dashboard
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            Log in
                        </a>
                    @endauth

                </div>
            @endif
        </div>
    </header>

@endsection
