@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    <div class="page-header mb-3">
        <div class="title-wrapper">
            <div class="col-auto d-block">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-home"></i>
                    </span> Dashboard
                </h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card" style="border:none;border-left:4px solid #e65c5c;box-shadow:0 2px 12px rgba(0,0,0,0.07);">
                <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                    <div style="width:52px;height:52px;border-radius:10px;background:#ffe5e5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="mdi mdi-earth" style="font-size:28px;color:#e65c5c;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.8rem;color:#9a9a9a;text-transform:uppercase;letter-spacing:0.07em;">Countries</div>
                        <div style="font-size:2.2rem;font-weight:700;color:#2d2d2d;line-height:1.1;">{{ $countriesCount }}</div>
                        <div style="font-size:0.78rem;color:#b0b0b0;margin-top:2px;">Registered in the system</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card" style="border:none;border-left:4px solid #3b9ede;box-shadow:0 2px 12px rgba(0,0,0,0.07);">
                <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                    <div style="width:52px;height:52px;border-radius:10px;background:#dff0fb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="mdi mdi-shield" style="font-size:28px;color:#3b9ede;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.8rem;color:#9a9a9a;text-transform:uppercase;letter-spacing:0.07em;">Clubs</div>
                        <div style="font-size:2.2rem;font-weight:700;color:#2d2d2d;line-height:1.1;">{{ $clubsCount }}</div>
                        <div style="font-size:0.78rem;color:#b0b0b0;margin-top:2px;">Across all competitions</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card" style="border:none;border-left:4px solid #27ae60;box-shadow:0 2px 12px rgba(0,0,0,0.07);">
                <div class="card-body d-flex align-items-center gap-4 py-4 px-4">
                    <div style="width:52px;height:52px;border-radius:10px;background:#d5f5e3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="mdi mdi-trophy" style="font-size:28px;color:#27ae60;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.8rem;color:#9a9a9a;text-transform:uppercase;letter-spacing:0.07em;">Competitions</div>
                        <div style="font-size:2.2rem;font-weight:700;color:#2d2d2d;line-height:1.1;">{{ $competitionsCount }}</div>
                        <div style="font-size:0.78rem;color:#b0b0b0;margin-top:2px;">Active tournaments tracked</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection