@extends('layouts.front')

@section('meta')
    <title>Page Not Found — Champions Review</title>
    <meta name="robots" content="noindex, follow" />
@endsection

@section('content')

    <section class="hero-section hero-section--inner hero-section--inner-competition">
        <div class="hero-bg" style="background-image: url({{ asset('build/images/default-banner.webp') }});"></div>
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <div class="container">
                <p class="hero-title">404 — <span>Page Not Found</span></p>
                <p class="hero-slogan">The page you're looking for doesn't exist or has moved.</p>
            </div>
        </div>
    </section>

    <section class="seo-section">
        <div class="container" style="text-align: center;">
            <p>
                Try searching for a club, country or tournament using the search bar above,
                or head back to the <a href="{{ url('/') }}">homepage</a>.
            </p>
        </div>
    </section>

@endsection
