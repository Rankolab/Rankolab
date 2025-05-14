@extends('admin.layouts.app')

@section('title', 'Add Website')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Website</h1>
        <a href="{{ route('admin.websites.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Websites
        </a>
    </div>

    <!-- Website Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Website Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.websites.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="user_id">User <span class="text-danger">*</span></label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="url">Website URL <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">https://</span>
                                </div>
                                <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" placeholder="example.com" required>
                                @error('url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Enter the domain without the protocol (http:// or https://)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Website Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="primary_keyword">Primary Keyword</label>
                            <input type="text" class="form-control @error('primary_keyword') is-invalid @enderror" id="primary_keyword" name="primary_keyword" value="{{ old('primary_keyword') }}">
                            @error('primary_keyword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">This will be used as the main keyword for content generation.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="run_analysis" name="run_analysis" value="1" {{ old('run_analysis', '1') == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="run_analysis">Run domain analysis after creation</label>
                            <small class="form-text text-muted">This will analyze the website for SEO metrics and provide recommendations.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Add Website</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Domain Analysis</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        When adding a new website, Rankolab can automatically run a domain analysis to gather important SEO metrics such as:
                    </p>
                    <ul class="text-muted">
                        <li>Website authority score</li>
                        <li>Overall SEO score</li>
                        <li>Backlink profile</li>
                        <li>Page speed analysis</li>
                        <li>Common SEO issues</li>
                    </ul>
                    <p class="text-muted">
                        This information helps our AI generate more relevant and effective content for your website.
                    </p>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Domain analysis may take a few moments to complete. You'll be notified when it's finished.
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User License Status</h6>
                </div>
                <div class="card-body">
                    <div id="licenseInfo">
                        <p class="text-center text-muted">Select a user to view license information</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-fill website name based on URL
        $('#url').on('blur', function() {
            const url = $(this).val().trim();
            if (url && !$('#name').val()) {
                let name = url.replace(/^www\./, '');
                name = name.replace(/\.(com|org|net|io|co|us|uk)$/, '');
                name = name.split('.')[0]; // Get first part of domain
                name = name.charAt(0).toUpperCase() + name.slice(1); // Capitalize
                $('#name').val(name);
            }
        });
        
        // Load user license info when user changes
        $('#user_id').on('change', function() {
            const userId = $(this).val();
            if (!userId) {
                $('#licenseInfo').html('<p class="text-center text-muted">Select a user to view license information</p>');
                return;
            }
            
            $('#licenseInfo').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</p>');
            
            $.ajax({
                url: '/admin/users/' + userId + '/license-info',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const license = response.license;
                        let html = '';
                        
                        if (license) {
                            const statusClass = {
                                'active': 'text-success',
                                'pending': 'text-warning',
                                'expired': 'text-danger',
                                'cancelled': 'text-muted'
                            }[license.status] || 'text-muted';
                            
                            html = `
                                <p><strong>Plan:</strong> <span class="text-primary">${license.plan.charAt(0).toUpperCase() + license.plan.slice(1)}</span></p>
                                <p><strong>Status:</strong> <span class="${statusClass}">${license.status.charAt(0).toUpperCase() + license.status.slice(1)}</span></p>
                                <p><strong>Websites:</strong> ${response.websiteCount} / ${license.max_websites}</p>
                                <p><strong>Monthly Content:</strong> ${response.contentCount} / ${license.max_content_per_month}</p>
                            `;
                            
                            if (response.canAddWebsite) {
                                html += '<div class="alert alert-success small">User can add more websites</div>';
                            } else {
                                html += '<div class="alert alert-danger small">User has reached website limit</div>';
                            }
                        } else {
                            html = `
                                <div class="alert alert-warning">
                                    <p class="mb-2">No active license found for this user.</p>
                                    <a href="/admin/licenses/create?user_id=${userId}" class="btn btn-sm btn-outline-primary">Create License</a>
                                </div>
                            `;
                        }
                        
                        $('#licenseInfo').html(html);
                    } else {
                        $('#licenseInfo').html('<div class="alert alert-danger">Error loading license information</div>');
                    }
                },
                error: function() {
                    $('#licenseInfo').html('<div class="alert alert-danger">Error loading license information</div>');
                }
            });
        });
    });
</script>
@endsection
