<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainAnalysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'website_id',
        'seo_score',
        'website_authority',
        'backlinks_count',
        'website_speed',
        'issues',
        'recommendations',
        'raw_data',
    ];

    /**
     * Get the website that owns the domain analysis.
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }
    
    /**
     * Get issues as an array.
     *
     * @return array
     */
    public function getIssuesArray()
    {
        return $this->issues ? json_decode($this->issues, true) : [];
    }
    
    /**
     * Get recommendations as an array.
     *
     * @return array
     */
    public function getRecommendationsArray()
    {
        return $this->recommendations ? json_decode($this->recommendations, true) : [];
    }
    
    /**
     * Get raw data as an array.
     *
     * @return array
     */
    public function getRawDataArray()
    {
        return $this->raw_data ? json_decode($this->raw_data, true) : [];
    }
    
    /**
     * Format website speed for display.
     *
     * @return string
     */
    public function getFormattedSpeed()
    {
        if ($this->website_speed >= 90) {
            $rating = 'Excellent';
            $class = 'text-success';
        } elseif ($this->website_speed >= 70) {
            $rating = 'Good';
            $class = 'text-info';
        } elseif ($this->website_speed >= 50) {
            $rating = 'Average';
            $class = 'text-warning';
        } else {
            $rating = 'Poor';
            $class = 'text-danger';
        }
        
        return [
            'score' => $this->website_speed,
            'rating' => $rating,
            'class' => $class,
        ];
    }
    
    /**
     * Format SEO score for display.
     *
     * @return string
     */
    public function getFormattedSeoScore()
    {
        if ($this->seo_score >= 90) {
            $rating = 'Excellent';
            $class = 'text-success';
        } elseif ($this->seo_score >= 70) {
            $rating = 'Good';
            $class = 'text-info';
        } elseif ($this->seo_score >= 50) {
            $rating = 'Average';
            $class = 'text-warning';
        } else {
            $rating = 'Poor';
            $class = 'text-danger';
        }
        
        return [
            'score' => $this->seo_score,
            'rating' => $rating,
            'class' => $class,
        ];
    }
    
    /**
     * Get the most critical issues.
     *
     * @param int $limit
     * @return array
     */
    public function getCriticalIssues($limit = 3)
    {
        $issues = $this->getIssuesArray();
        
        // Sort issues by severity (high to low)
        usort($issues, function($a, $b) {
            $severityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
            return $severityOrder[$a['severity']] <=> $severityOrder[$b['severity']];
        });
        
        return array_slice($issues, 0, $limit);
    }
}
