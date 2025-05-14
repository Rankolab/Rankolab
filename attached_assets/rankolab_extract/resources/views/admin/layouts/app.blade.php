<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankolab Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="{{ url('/admin/dashboard') }}">Rankolab Admin</a>
    </nav>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group">
                    <a href="{{ url('/admin/users') }}" class="list-group-item">Users</a>
                    <a href="{{ url('/admin/licenses') }}" class="list-group-item">Licenses</a>
                    <a href="{{ url('/admin/websites') }}" class="list-group-item">Websites</a>
                    <a href="{{ url('/admin/contents') }}" class="list-group-item">Content</a>
                    <a href="{{ url('/admin/performance') }}" class="list-group-item">Performance</a>
                </div>
            </div>
            <div class="col-md-10">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>