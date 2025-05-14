<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentTracking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trackable_id',
        'trackable_type',
        'event_type',
        'url',
        'user_agent',
        'referrer',
        'ip_address',
        'meta_data',
        'value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'float',
    ];

    /**
     * Get the trackable model (content or affiliate link).
     */
    public function trackable()
    {
        return $this->morphTo();
    }
    
    /**
     * Scope a query to only include impressions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeImpressions($query)
    {
        return $query->where('event_type', 'impression');
    }
    
    /**
     * Scope a query to only include clicks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClicks($query)
    {
        return $query->where('event_type', 'click');
    }
    
    /**
     * Scope a query to only include shares.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShares($query)
    {
        return $query->where('event_type', 'share');
    }
    
    /**
     * Scope a query to only include conversions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConversions($query)
    {
        return $query->where('event_type', 'conversion');
    }
    
    /**
     * Get meta data as array.
     *
     * @return array
     */
    public function getMetaDataArray()
    {
        return $this->meta_data ? json_decode($this->meta_data, true) : [];
    }
    
    /**
     * Get the browser name from user agent.
     *
     * @return string|null
     */
    public function getBrowser()
    {
        $userAgent = $this->user_agent;
        
        if (!$userAgent) {
            return null;
        }
        
        if (strpos($userAgent, 'Chrome') && strpos($userAgent, 'Safari')) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Safari') && !strpos($userAgent, 'Chrome')) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'MSIE') || strpos($userAgent, 'Trident/')) {
            return 'Internet Explorer';
        } elseif (strpos($userAgent, 'Edge')) {
            return 'Edge';
        } else {
            return 'Other';
        }
    }
    
    /**
     * Check if the request is from a mobile device.
     *
     * @return bool
     */
    public function isMobile()
    {
        $userAgent = $this->user_agent;
        
        if (!$userAgent) {
            return false;
        }
        
        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
    }
}
