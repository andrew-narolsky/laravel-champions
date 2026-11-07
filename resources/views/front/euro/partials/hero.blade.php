@php
    // per-competition banner: resources/assets/images/<slug>-banner.webp, otherwise the default one
    $banner ??= isset($competition) && file_exists(public_path("build/images/{$competition->slug}-banner.webp"))
        ? "{$competition->slug}-banner.webp"
        : 'default-banner-1.webp';
@endphp
<section class="hero-section hero-section--inner hero-section--inner-competition">
    <div class="hero-bg" style="background-image: url({{ asset('build/images/' . $banner) }});"></div>
    <div class="hero-overlay"></div>

    <nav class="country-breadcrumb">
        <div class="container">
            <a href="/">Home</a>
            @foreach($crumbs as $crumb)
                <span class="breadcrumb-sep">›</span>
                @if($loop->last)
                    <span>{{ $crumb['name'] }}</span>
                @else
                    <a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a>
                @endif
            @endforeach
        </div>
    </nav>

    <div class="hero-content">
        <div class="container">
            {{-- $title: string, or array of lines --}}
            <p class="hero-title">
                @foreach((array) $title as $line)
                    <span>{{ $line }}</span>
                @endforeach
            </p>
            @if(!empty($slogan))
                <p class="hero-slogan">{{ $slogan }}</p>
            @endif
        </div>
    </div>
</section>
