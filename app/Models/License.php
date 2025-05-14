<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'license_key',
        'plan',
        'status',
        'max_websites',
        'max_content_per_month',
        'expires_at',
        'domains',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the license.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if the license is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active' && (!$this->expires_at || now()->lt($this->expires_at));
    }
    
    /**
     * Get websites registered with this license.
     *
     * @return array
     */
    public function getRegisteredWebsites()
    {
        return $this->domains ? json_decode($this->domains, true) : [];
    }
    
    /**
     * Check if a domain is registered with this license.
     *
     * @param string $domain
     * @return bool
     */
    public function isDomainRegistered($domain)
    {
        $domains = $this->getRegisteredWebsites();
        return in_array($domain, $domains);
    }
    
    /**
     * Register a new domain with this license.
     *
     * @param string $domain
     * @return bool
     */
    public function registerDomain($domain)
    {
        $domains = $this->getRegisteredWebsites();
        
        if (count($domains) >= $this->max_websites) {
            return false; // Maximum websites reached
        }
        
        if (!in_array($domain, $domains)) {
            $domains[] = $domain;
            $this->update(['domains' => json_encode($domains)]);
        }
        
        return true;
    }
    
    /**
     * Unregister a domain from this license.
     *
     * @param string $domain
     * @return bool
     */
    public function unregisterDomain($domain)
    {
        $domains = $this->getRegisteredWebsites();
        
        if (in_array($domain, $domains)) {
            $domains = array_diff($domains, [$domain]);
            $this->update(['domains' => json_encode(array_values($domains))]);
            return true;
        }
        
        return false;
    }
}
