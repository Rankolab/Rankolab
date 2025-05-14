<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'url',
        'name',
        'primary_keyword',
        'description',
    ];

    /**
     * Get the user that owns the website.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contents for the website.
     */
    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    /**
     * Get the domain analyses for the website.
     */
    public function analyses()
    {
        return $this->hasMany(DomainAnalysis::class);
    }
    
    /**
     * Get the latest domain analysis for the website.
     */
    public function latestDomainAnalysis()
    {
        return $this->hasOne(DomainAnalysis::class)->latest();
    }
    
    /**
     * Count content generated this month.
     *
     * @return int
     */
    public function contentGeneratedThisMonth()
    {
        return $this->contents()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    }
    
    /**
     * Count total content generated.
     *
     * @return int
     */
    public function totalContentGenerated()
    {
        return $this->contents()->count();
    }
    
    /**
     * Get published content count.
     *
     * @return int
     */
    public function publishedContentCount()
    {
        return $this->contents()->where('status', 'published')->count();
    }
    
    /**
     * Get domain name from URL.
     *
     * @return string
     */
    public function getDomainName()
    {
        $parseUrl = parse_url($this->url);
        return isset($parseUrl['host']) ? $parseUrl['host'] : '';
    }
}
