<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'website_id',
        'title',
        'content',
        'target_keywords',
        'min_words',
        'word_count',
        'status',
        'plagiarism_score',
        'readability_score',
        'instructions',
        'generated_at',
        'published_at',
        'quality_checked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'generated_at' => 'datetime',
        'published_at' => 'datetime',
        'quality_checked_at' => 'datetime',
        'plagiarism_score' => 'float',
        'readability_score' => 'float',
    ];

    /**
     * Get the website that owns the content.
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }
    
    /**
     * Get the affiliate links associated with this content.
     */
    public function affiliateLinks()
    {
        return $this->hasMany(AffiliateLink::class);
    }
    
    /**
     * Get the tracking events for this content.
     */
    public function trackings()
    {
        return $this->morphMany(ContentTracking::class, 'trackable');
    }
    
    /**
     * Check if content has passed quality checks.
     *
     * @return bool
     */
    public function hasPassedQualityChecks()
    {
        // Readability score above 70 (grade 6 level) is considered good
        $readabilityPassed = $this->readability_score && $this->readability_score >= 70;
        
        // Plagiarism score below 10% is considered acceptable
        $plagiarismPassed = $this->plagiarism_score && $this->plagiarism_score <= 10;
        
        return $readabilityPassed && $plagiarismPassed;
    }
    
    /**
     * Get content excerpt.
     *
     * @param int $length
     * @return string
     */
    public function getExcerpt($length = 150)
    {
        if (!$this->content) {
            return '';
        }
        
        $excerpt = strip_tags($this->content);
        if (strlen($excerpt) > $length) {
            $excerpt = substr($excerpt, 0, $length) . '...';
        }
        
        return $excerpt;
    }
    
    /**
     * Get impression count.
     *
     * @return int
     */
    public function getImpressionCount()
    {
        return $this->trackings()->where('event_type', 'impression')->count();
    }
    
    /**
     * Get click count.
     *
     * @return int
     */
    public function getClickCount()
    {
        return $this->trackings()->where('event_type', 'click')->count();
    }
    
    /**
     * Get share count.
     *
     * @return int
     */
    public function getShareCount()
    {
        return $this->trackings()->where('event_type', 'share')->count();
    }
}
