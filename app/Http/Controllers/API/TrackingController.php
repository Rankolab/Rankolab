<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Content;
use App\Models\ContentTracking;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    /**
     * Track content impressions and engagement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackContent(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'content_id' => 'required|exists:contents,id',
            'event_type' => 'required|in:impression,click,share',
            'url' => 'nullable|string',
            'user_agent' => 'nullable|string',
            'referrer' => 'nullable|string',
        ]);
        
        // Validate license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Get content
        $content = Content::find($validated['content_id']);
        
        // Check if content belongs to user's websites
        $userWebsiteIds = $license->user->websites()->pluck('id')->toArray();
        
        if (!in_array($content->website_id, $userWebsiteIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to track this content',
            ], 403);
        }
        
        // Record tracking event
        ContentTracking::create([
            'trackable_id' => $content->id,
            'trackable_type' => Content::class,
            'event_type' => $validated['event_type'],
            'url' => $validated['url'] ?? null,
            'user_agent' => $validated['user_agent'] ?? $request->userAgent(),
            'referrer' => $validated['referrer'] ?? null,
            'ip_address' => $request->ip(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Event tracked successfully',
        ]);
    }
    
    /**
     * Track affiliate link clicks.
     *
     * @param  string  $trackingId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function trackAffiliateClick($trackingId, Request $request)
    {
        // Find affiliate link by tracking ID
        $affiliateLink = AffiliateLink::where('tracking_url', 'LIKE', '%' . $trackingId . '%')->first();
        
        if (!$affiliateLink || $affiliateLink->status !== 'active') {
            // Redirect to home if link not found or inactive
            return redirect('/');
        }
        
        // Record click event
        ContentTracking::create([
            'trackable_id' => $affiliateLink->id,
            'trackable_type' => AffiliateLink::class,
            'event_type' => 'click',
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'ip_address' => $request->ip(),
        ]);
        
        // Get destination URL
        $destinationUrl = $request->query('url', $affiliateLink->original_url);
        
        // Redirect to destination
        return redirect()->away($destinationUrl);
    }
    
    /**
     * Record a conversion for an affiliate link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordConversion(Request $request)
    {
        $validated = $request->validate([
            'tracking_id' => 'required|string',
            'order_id' => 'required|string',
            'value' => 'required|numeric|min:0',
        ]);
        
        // Find affiliate link by tracking ID
        $affiliateLink = AffiliateLink::where('tracking_url', 'LIKE', '%' . $validated['tracking_id'] . '%')->first();
        
        if (!$affiliateLink) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid tracking ID',
            ], 404);
        }
        
        // Record conversion event
        ContentTracking::create([
            'trackable_id' => $affiliateLink->id,
            'trackable_type' => AffiliateLink::class,
            'event_type' => 'conversion',
            'meta_data' => json_encode([
                'order_id' => $validated['order_id'],
            ]),
            'value' => $validated['value'],
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Conversion recorded successfully',
        ]);
    }
    
    /**
     * Get tracking statistics for content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContentStats(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'content_id' => 'required|exists:contents,id',
        ]);
        
        // Validate license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Get content
        $content = Content::find($validated['content_id']);
        
        // Check if content belongs to user's websites
        $userWebsiteIds = $license->user->websites()->pluck('id')->toArray();
        
        if (!in_array($content->website_id, $userWebsiteIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this content',
            ], 403);
        }
        
        // Get stats
        $impressions = $content->trackings()->where('event_type', 'impression')->count();
        $clicks = $content->trackings()->where('event_type', 'click')->count();
        $shares = $content->trackings()->where('event_type', 'share')->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'content_id' => $content->id,
                'title' => $content->title,
                'impressions' => $impressions,
                'clicks' => $clicks,
                'shares' => $shares,
                'engagement_rate' => $impressions > 0 ? round(($clicks + $shares) / $impressions * 100, 2) : 0,
            ],
        ]);
    }
}
