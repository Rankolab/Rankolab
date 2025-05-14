@extends('admin.layouts.app')

@section('title', 'Generate Content')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Generate Content</h1>
        <a href="{{ route('admin.contents.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Content List
        </a>
    </div>

    <!-- Content Generation Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Content Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contents.store') }}" method="POST" id="contentForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="website_id">Website <span class="text-danger">*</span></label>
                            <select class="form-control @error('website_id') is-invalid @enderror" id="website_id" name="website_id" required>
                                <option value="">Select Website</option>
                                @foreach(App\Models\Website::orderBy('name')->get() as $website)
                                    <option value="{{ $website->id }}" {{ old('website_id', request('website_id')) == $website->id ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="target_keywords">Target Keywords <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('target_keywords') is-invalid @enderror" id="target_keywords" name="target_keywords" value="{{ old('target_keywords') }}" required>
                            @error('target_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Enter comma-separated keywords to target in this content.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="min_words">Word Count <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('min_words') is-invalid @enderror" id="min_words" name="min_words" value="{{ old('min_words', 1000) }}" min="1000" max="2500" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">words</span>
                                </div>
                            </div>
                            @error('min_words')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Choose between 1,000 and 2,500 words.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="instructions">Additional Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="4">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Add specific instructions for content generation (optional).</small>
                        </div>
                        
                        <!-- Word Count Range Slider -->
                        <div class="form-group">
                            <label>Word Count Range</label>
                            <input type="range" class="custom-range" id="wordCountSlider" min="1000" max="2500" step="100" value="{{ old('min_words', 1000) }}">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">1,000</small>
                                <small class="text-muted">1,500</small>
                                <small class="text-muted">2,000</small>
                                <small class="text-muted">2,500</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i>
                            Content generation may take up to 2 minutes depending on length and complexity.
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="generateButton">
                            <i class="fas fa-robot mr-1"></i> Generate Content
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Content Types</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Blog Post</h6>
                        <p class="text-muted small">In-depth content with comprehensive information on a specific topic.</p>
                        <button class="btn btn-sm btn-outline-primary mb-2 content-type-btn" data-words="1500" data-title="Comprehensive Guide to" data-format="blog">
                            Use Template
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">How-To Guide</h6>
                        <p class="text-muted small">Step-by-step instructions for accomplishing a specific task.</p>
                        <button class="btn btn-sm btn-outline-primary mb-2 content-type-btn" data-words="1200" data-title="How to" data-format="how-to">
                            Use Template
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Listicle</h6>
                        <p class="text-muted small">Engaging numbered list of tips, ideas, or products.</p>
                        <button class="btn btn-sm btn-outline-primary mb-2 content-type-btn" data-words="1000" data-title="Top 10" data-format="list">
                            Use Template
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Product Review</h6>
                        <p class="text-muted small">Detailed evaluation of a product or service.</p>
                        <button class="btn btn-sm btn-outline-primary mb-2 content-type-btn" data-words="1800" data-title="Review:" data-format="review">
                            Use Template
                        </button>
                    </div>
                    
                    <div>
                        <h6 class="font-weight-bold">Comparison Article</h6>
                        <p class="text-muted small">Side-by-side comparison of two or more items.</p>
                        <button class="btn btn-sm btn-outline-primary mb-2 content-type-btn" data-words="2000" data-title="vs" data-format="comparison">
                            Use Template
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Need Inspiration?</h6>
                </div>
                <div class="card-body">
                    <div id="keywordSuggestions">
                        <p class="text-center text-muted small mb-2">Select a website to see keyword suggestions based on its primary keyword.</p>
                        <button id="getKeywordSuggestions" class="btn btn-outline-secondary btn-sm btn-block" disabled>
                            <i class="fas fa-lightbulb mr-1"></i> Generate Ideas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <h5 class="modal-title mb-3">Generating Your Content</h5>
                    <p class="text-muted">This may take a minute or two depending on the length and complexity of the content.</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Keyword Suggestions Modal -->
    <div class="modal fade" id="keywordSuggestionsModal" tabindex="-1" role="dialog" aria-labelledby="keywordSuggestionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="keywordSuggestionsModalLabel">Keyword Suggestions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="keyword-suggestions-content">
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-3">Generating keyword suggestions...</p>
                        </div>
                    </div>
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
<script>
    $(document).ready(function() {
        // Handle word count slider
        $('#wordCountSlider').on('input', function() {
            $('#min_words').val($(this).val());
        });
        
        $('#min_words').on('input', function() {
            $('#wordCountSlider').val($(this).val());
        });
        
        // Handle content type template buttons
        $('.content-type-btn').on('click', function() {
            const words = $(this).data('words');
            const titlePrefix = $(this).data('title');
            const format = $(this).data('format');
            
            $('#min_words').val(words);
            $('#wordCountSlider').val(words);
            
            // Update title with prefix if it's empty or confirm override
            if (!$('#title').val() || confirm('Replace current title with template?')) {
                if (format === 'list') {
                    $('#title').val(titlePrefix + ' Ways to Improve [Your Topic]');
                } else if (format === 'how-to') {
                    $('#title').val(titlePrefix + ' [Accomplish Task] in [Year]');
                } else if (format === 'comparison') {
                    $('#title').val('[Product A] ' + titlePrefix + ' [Product B]: Which Is Better?');
                } else if (format === 'review') {
                    $('#title').val(titlePrefix + ' [Product/Service] - Is It Worth It?');
                } else {
                    $('#title').val(titlePrefix + ' [Your Topic]');
                }
            }
            
            // Add format-specific instructions
            let instructions = '';
            
            if (format === 'list') {
                instructions = "Format this as a numbered list article with a brief introduction, " +
                               "at least 10 detailed points, and a conclusion. Include subheadings " +
                               "for each point and actionable advice.";
            } else if (format === 'how-to') {
                instructions = "Create a step-by-step guide with clear, actionable instructions. " +
                               "Include an introduction explaining the benefits, numbered steps with " +
                               "detailed explanations, tips for success, common mistakes to avoid, " +
                               "and a conclusion.";
            } else if (format === 'comparison') {
                instructions = "Structure this as a detailed comparison with an introduction, " +
                               "side-by-side feature analysis, pros and cons of each option, " +
                               "pricing comparison, and a conclusion with clear recommendations " +
                               "for different user types.";
            } else if (format === 'review') {
                instructions = "Create a comprehensive review with an introduction to the product/service, " +
                               "detailed features analysis, benefits, drawbacks, pricing information, " +
                               "comparison to alternatives, and a final verdict with rating out of 10.";
            }
            
            if (instructions && (!$('#instructions').val() || confirm('Replace current instructions with template?'))) {
                $('#instructions').val(instructions);
            }
        });
        
        // Show loading modal on form submit
        $('#contentForm').on('submit', function() {
            $('#loadingModal').modal('show');
        });
        
        // Enable keyword suggestions when website is selected
        $('#website_id').on('change', function() {
            if ($(this).val()) {
                $('#getKeywordSuggestions').prop('disabled', false);
            } else {
                $('#getKeywordSuggestions').prop('disabled', true);
            }
        });
        
        // Handle keyword suggestions button
        $('#getKeywordSuggestions').on('click', function() {
            const websiteId = $('#website_id').val();
            
            if (!websiteId) {
                return;
            }
            
            // Show the modal
            $('#keywordSuggestionsModal').modal('show');
            
            // Fetch keyword suggestions
            $.ajax({
                url: '/admin/websites/' + websiteId + '/keyword-suggestions',
                type: 'GET',
                success: function(response) {
                    let html = '';
                    
                    if (response.success && response.data.suggestions.length > 0) {
                        html += '<h6 class="font-weight-bold">Based on: ' + response.data.seed_keyword + '</h6>';
                        html += '<div class="table-responsive">';
                        html += '<table class="table table-sm table-bordered">';
                        html += '<thead><tr><th>Keyword</th><th>Search Volume</th><th>Competition</th><th></th></tr></thead>';
                        html += '<tbody>';
                        
                        response.data.suggestions.forEach(function(keyword) {
                            const competitionClass = keyword.competition < 0.3 ? 'text-success' : (keyword.competition < 0.7 ? 'text-warning' : 'text-danger');
                            
                            html += '<tr>';
                            html += '<td>' + keyword.keyword + '</td>';
                            html += '<td>' + keyword.search_volume + '</td>';
                            html += '<td><span class="' + competitionClass + '">' + (keyword.competition * 100).toFixed(0) + '%</span></td>';
                            html += '<td><button class="btn btn-xs btn-outline-primary use-keyword-btn" data-keyword="' + keyword.keyword + '">Use</button></td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table></div>';
                    } else {
                        html = '<div class="alert alert-warning">No keyword suggestions found. Please make sure the website has a primary keyword set.</div>';
                    }
                    
                    $('.keyword-suggestions-content').html(html);
                    
                    // Handle use keyword buttons
                    $('.use-keyword-btn').on('click', function() {
                        const keyword = $(this).data('keyword');
                        $('#title').val(keyword);
                        $('#target_keywords').val(keyword);
                        $('#keywordSuggestionsModal').modal('hide');
                    });
                },
                error: function() {
                    $('.keyword-suggestions-content').html('<div class="alert alert-danger">Error loading keyword suggestions. Please try again.</div>');
                }
            });
        });
    });
</script>
@endsection
