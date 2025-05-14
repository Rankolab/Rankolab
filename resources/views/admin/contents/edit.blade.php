@extends('admin.layouts.app')

@section('title', 'Edit Content')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Content</h1>
        <div>
            <a href="{{ route('admin.contents.view', $content) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-eye fa-sm text-white-50"></i> View Content
            </a>
            <a href="{{ route('admin.contents.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Content List
            </a>
        </div>
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

    <!-- Content Form -->
    <div class="row">
        <div class="col-lg-9">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Content Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contents.update', $content) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="website_id">Website <span class="text-danger">*</span></label>
                            <select class="form-control @error('website_id') is-invalid @enderror" id="website_id" name="website_id" required>
                                <option value="">Select Website</option>
                                @foreach($websites as $website)
                                    <option value="{{ $website->id }}" {{ old('website_id', $content->website_id) == $website->id ? 'selected' : '' }}>
                                        {{ $website->name }} ({{ $website->url }})
                                    </option>
                                @endforeach
                            </select>
                            @error('website_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="title">Content Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $content->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="target_keywords">Target Keywords <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('target_keywords') is-invalid @enderror" id="target_keywords" name="target_keywords" value="{{ old('target_keywords', $content->target_keywords) }}" required>
                            @error('target_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ old('status', $content->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="generated" {{ old('status', $content->status) == 'generated' ? 'selected' : '' }}>Generated</option>
                                <option value="published" {{ old('status', $content->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="failed" {{ old('status', $content->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="content_text">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content_text" name="content" rows="20" required>{{ old('content', $content->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Content</button>
                            <a href="{{ route('admin.contents.regenerate', $content) }}" class="btn btn-warning" onclick="return confirm('Are you sure you want to regenerate this content? This will overwrite the current content.');">
                                <i class="fas fa-sync-alt mr-1"></i> Regenerate Content
                            </a>
                            @if($content->status == 'generated')
                                <a href="{{ route('admin.contents.publish', $content) }}" class="btn btn-success">
                                    <i class="fas fa-upload mr-1"></i> Publish Content
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Content Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-muted">Word Count:</span>
                        <span class="font-weight-bold ml-2">{{ $content->word_count ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-muted">Created:</span>
                        <span class="font-weight-bold ml-2">{{ $content->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    
                    @if($content->generated_at)
                    <div class="mb-3">
                        <span class="text-muted">Generated:</span>
                        <span class="font-weight-bold ml-2">{{ $content->generated_at->format('M d, Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($content->published_at)
                    <div class="mb-3">
                        <span class="text-muted">Published:</span>
                        <span class="font-weight-bold ml-2">{{ $content->published_at->format('M d, Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($content->readability_score)
                    <div class="mb-3">
                        <span class="text-muted">Readability:</span>
                        <span class="font-weight-bold ml-2 {{ $content->readability_score >= 70 ? 'text-success' : 'text-warning' }}">
                            {{ $content->readability_score }}/100
                        </span>
                    </div>
                    @endif
                    
                    @if($content->plagiarism_score !== null)
                    <div class="mb-3">
                        <span class="text-muted">Plagiarism:</span>
                        <span class="font-weight-bold ml-2 {{ $content->plagiarism_score <= 10 ? 'text-success' : 'text-danger' }}">
                            {{ $content->plagiarism_score }}%
                        </span>
                    </div>
                    @endif
                    
                    <hr>
                    
                    <h6 class="font-weight-bold small mt-4">Quality Analysis</h6>
                    
                    <div class="mb-3">
                        <a href="#" class="btn btn-sm btn-outline-primary btn-block analyze-content-btn">
                            <i class="fas fa-search mr-1"></i> Analyze Content
                        </a>
                    </div>
                    
                    <div id="contentAnalysisResults"></div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contents.destroy', $content) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this content? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash mr-1"></i> Delete Content
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Analysis Modal -->
    <div class="modal fade" id="analysisModal" tabindex="-1" role="dialog" aria-labelledby="analysisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="analysisModalLabel">Content Analysis</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-5" id="analysisLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3">Analyzing your content...</p>
                    </div>
                    <div id="analysisResults" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    $(document).ready(function() {
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content_text',
            height: 500,
            menubar: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
        
        // Handle content analysis
        $('.analyze-content-btn').on('click', function(e) {
            e.preventDefault();
            
            // Get content from TinyMCE
            let content = tinymce.get('content_text').getContent();
            const keywords = $('#target_keywords').val();
            
            if (!content) {
                alert('Please enter some content to analyze.');
                return;
            }
            
            // Show analysis modal with loading
            $('#analysisModal').modal('show');
            $('#analysisLoading').show();
            $('#analysisResults').hide();
            
            // Send content for analysis
            $.ajax({
                url: '/admin/bot/analyze-content',
                type: 'POST',
                data: {
                    content: content,
                    keywords: keywords,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Hide loading and show results
                        $('#analysisLoading').hide();
                        
                        const analysis = response.analysis;
                        let html = '';
                        
                        // Word count section
                        html += '<div class="mb-4">';
                        html += '<h6 class="font-weight-bold">Word Count</h6>';
                        html += '<p>' + analysis.word_count + ' words</p>';
                        html += '</div>';
                        
                        // Keyword density section
                        html += '<div class="mb-4">';
                        html += '<h6 class="font-weight-bold">Keyword Density</h6>';
                        html += '<ul class="list-group">';
                        
                        for (const [keyword, density] of Object.entries(analysis.keyword_density)) {
                            let densityClass = 'text-success';
                            if (density < 0.5) densityClass = 'text-warning';
                            if (density > 3) densityClass = 'text-danger';
                            
                            html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            html += keyword;
                            html += '<span class="badge badge-pill ' + densityClass + '">' + density + '%</span>';
                            html += '</li>';
                        }
                        
                        html += '</ul>';
                        html += '</div>';
                        
                        // Readability section
                        html += '<div class="mb-4">';
                        html += '<h6 class="font-weight-bold">Readability Score</h6>';
                        
                        let readabilityClass = 'text-success';
                        if (analysis.readability_score < 70) readabilityClass = 'text-warning';
                        if (analysis.readability_score < 60) readabilityClass = 'text-danger';
                        
                        html += '<div class="progress mb-2">';
                        html += '<div class="progress-bar bg-' + (readabilityClass.replace('text-', '')) + '" role="progressbar" style="width: ' + analysis.readability_score + '%" aria-valuenow="' + analysis.readability_score + '" aria-valuemin="0" aria-valuemax="100"></div>';
                        html += '</div>';
                        html += '<p class="' + readabilityClass + '">' + analysis.readability_score + '/100</p>';
                        html += '</div>';
                        
                        // Structure section
                        html += '<div class="mb-4">';
                        html += '<h6 class="font-weight-bold">Structure</h6>';
                        html += '<p>Headings: ' + analysis.headings_count + '</p>';
                        html += '</div>';
                        
                        // Issues section
                        if (analysis.issues.length > 0) {
                            html += '<div class="mb-4">';
                            html += '<h6 class="font-weight-bold">Issues</h6>';
                            html += '<ul class="text-danger">';
                            
                            analysis.issues.forEach(function(issue) {
                                html += '<li>' + issue + '</li>';
                            });
                            
                            html += '</ul>';
                            html += '</div>';
                        }
                        
                        // Recommendations section
                        if (analysis.recommendations.length > 0) {
                            html += '<div class="mb-4">';
                            html += '<h6 class="font-weight-bold">Recommendations</h6>';
                            html += '<ul class="text-info">';
                            
                            analysis.recommendations.forEach(function(recommendation) {
                                html += '<li>' + recommendation + '</li>';
                            });
                            
                            html += '</ul>';
                            html += '</div>';
                        }
                        
                        // Update the modal with results
                        $('#analysisResults').html(html).show();
                        
                        // Update the sidebar with summary
                        let summaryHtml = '<div class="alert alert-' + (analysis.readability_score >= 70 ? 'success' : 'warning') + ' small mb-3">';
                        summaryHtml += '<strong>Readability:</strong> ' + analysis.readability_score + '/100';
                        summaryHtml += '</div>';
                        
                        if (analysis.issues.length > 0) {
                            summaryHtml += '<div class="alert alert-danger small mb-3">';
                            summaryHtml += '<strong>Issues:</strong> ' + analysis.issues.length + ' found';
                            summaryHtml += '</div>';
                        } else {
                            summaryHtml += '<div class="alert alert-success small mb-3">';
                            summaryHtml += '<strong>Issues:</strong> None found';
                            summaryHtml += '</div>';
                        }
                        
                        $('#contentAnalysisResults').html(summaryHtml);
                    } else {
                        $('#analysisLoading').hide();
                        $('#analysisResults').html('<div class="alert alert-danger">Analysis failed: ' + response.error + '</div>').show();
                    }
                },
                error: function() {
                    $('#analysisLoading').hide();
                    $('#analysisResults').html('<div class="alert alert-danger">An error occurred during analysis. Please try again.</div>').show();
                }
            });
        });
    });
</script>
@endsection
