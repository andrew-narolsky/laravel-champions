<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Football Champions</title>

    @vite([
        'resources/assets/vendors/mdi/css/materialdesignicons.min.css',
        'resources/assets/css/styles.css'
    ])

    <link rel="icon" type="image/png" href="/favicon.ico"/>
</head>
<body>
<div class="container-scroller">

    @yield('content')

</div>
@vite(['resources/assets/js/main.js'])
</body>
</html>
