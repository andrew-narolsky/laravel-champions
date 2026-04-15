@extends('layouts.front')

@section('meta')
    <title>{{ $competition->name }} — Results, Winners & History</title>
    <meta name="description" content="Complete history of {{ $competition->name }}: season-by-season results, champions, runners-up, finals and all-time statistics. Explore clubs, records and past winners." />
@endsection

@section('content')

@include("front.competitions.{$style}")

@endsection
