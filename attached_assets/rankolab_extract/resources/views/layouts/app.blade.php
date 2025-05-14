<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rankolab Admin - @yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-light">

    <div id="app" class="d-flex">
        @include('partials.sidebar')

        <div class="flex-grow-1">
            @include('partials.topnav')

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
