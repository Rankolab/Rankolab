<div class="bg-dark text-white vh-100 p-3" style="width: 250px;">
    <h4 class="mb-4 text-center">Rankolab Admin</h4>
    <ul class="nav flex-column">
        <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link text-white"><i class="fas fa-home me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a href="{{ route('admin.users.index') }}" class="nav-link text-white"><i class="fas fa-users me-2"></i>Users</a></li>
        <li class="nav-item"><a href="{{ route('admin.websites.index') }}" class="nav-link text-white"><i class="fas fa-globe me-2"></i>Websites</a></li>
        <li class="nav-item"><a href="{{ route('admin.articles.index') }}" class="nav-link text-white"><i class="fas fa-file-alt me-2"></i>Articles</a></li>
        <li class="nav-item"><a href="{{ route('admin.licenses.index') }}" class="nav-link text-white"><i class="fas fa-key me-2"></i>Licenses</a></li>
        <li class="nav-item"><a href="#" class="nav-link text-white"><i class="fas fa-robot me-2"></i>Rankolab Bot</a></li>
    </ul>
</div>
