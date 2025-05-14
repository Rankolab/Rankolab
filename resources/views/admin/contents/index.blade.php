@extends('admin.layouts.app')

@section('title', 'Content Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Content Management</h1>
        <a href="{{ route('admin.contents.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Generate New Content
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Content</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.contents.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-2">
                    <label for="website_id" class="sr-only">Website</label>
                    <select class="form-control" id="website_id" name="website_id">
                        <option value="">All Websites</option>
                        @foreach(App\Models\Website::orderBy('name')->get() as $websiteOption)
                            <option value="{{ $websiteOption->id }}" {{ request('website_id') == $websiteOption->id ? 'selected' : '' }}>
                                {{ $websiteOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-2 mr-2">
                    <label for="status" class="sr-only">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="generated" {{ request('status') == 'generated' ? 'selected' : '' }}>Generated</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                
                <div class="form-group mb-2 mr-2">
                    <label for="search" class="sr-only">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search titles or keywords" value="{{ request('search') }}">
                </div>
                
                <button type="submit" class="btn btn-primary mb-2">Apply Filters</button>
                <a href="{{ route('admin.contents.index') }}" class="btn btn-secondary mb-2 ml-2">Reset</a>
            </form>
        </div>
    </div>

    <!-- Content Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Content List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="contentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Website</th>
                            <th>Keywords</th>
                            <th>Status</th>
                            <th>Words</th>
                            <th>Quality</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contents as $content)
                            <tr>
                                <td><a href="{{ route('admin.contents.view', $content) }}">{{ $content->title }}</a></td>
                                <td>
                                    <a href="{{ route('admin.websites.edit', $content->website) }}">
                                        {{ $content->website->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                        {{ $content->target_keywords }}
                                    </span>
                                </td>
                                <td>
                                    @if($content->status == 'published')
                                        <span class="badge badge-success">Published</span>
                                    @elseif($content->status == 'generated')
                                        <span class="badge badge-info">Generated</span>
                                    @elseif($content->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-danger">Failed</span>
                                    @endif
                                </td>
                                <td>{{ $content->word_count ?? 'N/A' }}</td>
                                <td>
                                    @if($content->readability_score)
                                        <div data-toggle="tooltip" title="Readability Score">
                                            <i class="fas fa-book-reader text-info"></i> 
                                            {{ $content->readability_score }}
                                        </div>
                                    @endif
                                    
                                    @if($content->plagiarism_score)
                                        <div data-toggle="tooltip" title="Plagiarism Score (lower is better)">
                                            <i class="fas fa-copy {{ $content->plagiarism_score < 10 ? 'text-success' : 'text-danger' }}"></i> 
                                            {{ $content->plagiarism_score }}%
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $content->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.contents.view', $content) }}" class="btn btn-sm btn-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.contents.edit', $content) }}" class="btn btn-sm btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($content->status == 'generated')
                                            <form action="{{ route('admin.contents.publish', $content) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Publish">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.contents.destroy', $content) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this content?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No content found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-end">
                {{ $contents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#contentsTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false,
            "columnDefs": [
                { "orderable": false, "targets": [2, 5, 7] }
            ]
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
