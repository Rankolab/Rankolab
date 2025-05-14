<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'original_url',
        'tracking_url',
        'content_id',
        'commission_rate',
        'status',
    ];

    /**
     * Get the content that owns the affiliate link.
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
    
    /**
     * Get the tracking events for this affiliate link.
     */
    public function trackings()
    {
        return $this->morphMany(ContentTracking::class, 'trackable');
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
     * Get impression count.
     *
     * @return int
     */
    public function getImpressionCount()
    {
        return $this->trackings()->where('event_type', 'impression')->count();
    }
    
    /**
     * Get conversion count.
     *
     * @return int
     */
    public function getConversionCount()
    {
        return $this->trackings()->where('event_type', 'conversion')->count();
    }
    
    /**
     * Get total revenue.
     *
     * @return float
     */
    public function getTotalRevenue()
    {
        return $this->trackings()->where('event_type', 'conversion')->sum('value');
    }
    
    /**
     * Get click-through rate.
     *
     * @return float
     */
    public function getClickThroughRate()
    {
        $impressions = $this->getImpressionCount();
        
        if ($impressions > 0) {
            return ($this->getClickCount() / $impressions) * 100;
        }
        
        return 0;
    }
    
    /**
     * Get conversion rate.
     *
     * @return float
     */
    public function getConversionRate()
    {
        $clicks = $this->getClickCount();
        
        if ($clicks > 0) {
            return ($this->getConversionCount() / $clicks) * 100;
        }
        
        return 0;
    }
    
    /**
     * Get earnings per click.
     *
     * @return float
     */
    public function getEarningsPerClick()
    {
        $clicks = $this->getClickCount();
        
        if ($clicks > 0) {
            return $this->getTotalRevenue() / $clicks;
        }
        
        return 0;
    }
}
