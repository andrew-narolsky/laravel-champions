@extends('layouts.front')

@php
    $metaTitle = "{$competition->name} — Results, Winners & History";
    $metaDescription = "Complete history of {$competition->name}: season-by-season results, champions, runners-up, finals and all-time statistics. Explore clubs, records and past winners.";
    $ogImage = $competition->attachment?->getFileUrl() ?? asset('build/images/champions.webp');
@endphp

@section('meta')
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}" />

    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('competition.show', $competition) }}" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDescription }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta name="twitter:title" content="{{ $metaTitle }}" />
    <meta name="twitter:description" content="{{ $metaDescription }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />

    @include('front.partials.breadcrumb-jsonld', ['items' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => $competition->country->name, 'url' => route('country.show', $competition->country)],
        ['name' => $competition->name, 'url' => route('competition.show', $competition)],
    ]])
@endsection

@section('content')

@include("front.competitions.{$style}")

@endsection
