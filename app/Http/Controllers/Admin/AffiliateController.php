<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    /**
     * Display a listing of affiliate links and their performance.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $affiliateLinks = AffiliateLink::withCount(['trackings as clicks' => function ($query) {
                $query->where('event_type', 'click');
            }])
            ->withCount(['trackings as impressions' => function ($query) {
                $query->where('event_type', 'impression');
            }])
            ->withSum(['trackings as revenue' => function ($query) {
                $query->where('event_type', 'conversion');
            }], 'value')
            ->latest()
            ->paginate(10);
            
        // Get monthly stats for charts
        $monthlyStats = DB::table('affiliate_links')
            ->join('content_trackings', 'affiliate_links.id', '=', 'content_trackings.trackable_id')
            ->where('content_trackings.trackable_type', AffiliateLink::class)
            ->select(
                DB::raw('MONTH(content_trackings.created_at) as month'),
                DB::raw('YEAR(content_trackings.created_at) as year'),
                DB::raw('SUM(CASE WHEN content_trackings.event_type = "click" THEN 1 ELSE 0 END) as clicks'),
                DB::raw('SUM(CASE WHEN content_trackings.event_type = "impression" THEN 1 ELSE 0 END) as impressions'),
                DB::raw('SUM(CASE WHEN content_trackings.event_type = "conversion" THEN content_trackings.value ELSE 0 END) as revenue')
            )
            ->whereRaw('content_trackings.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        // Format for charts
        $months = [];
        $clicksData = [];
        $impressionsData = [];
        $revenueData = [];
        
        foreach ($monthlyStats as $stat) {
            $monthName = date('M Y', mktime(0, 0, 0, $stat->month, 1, $stat->year));
            $months[] = $monthName;
            $clicksData[] = $stat->clicks;
            $impressionsData[] = $stat->impressions;
            $revenueData[] = $stat->revenue;
        }
        
        return view('admin.affiliates.index', compact(
            'affiliateLinks',
            'months',
            'clicksData',
            'impressionsData',
            'revenueData'
        ));
    }

    /**
     * Store a newly created affiliate link in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'destination_url' => 'required|url',
            'content_id' => 'nullable|exists:contents,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        AffiliateLink::create([
            'name' => $validated['name'],
            'original_url' => $validated['destination_url'],
            'tracking_url' => $this->generateTrackingUrl($validated['destination_url']),
            'content_id' => $validated['content_id'] ?? null,
            'commission_rate' => $validated['commission_rate'] ?? 0,
            'status' => 'active',
        ]);

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate link created successfully.');
    }

    /**
     * Update the specified affiliate link in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AffiliateLink  $affiliateLink
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, AffiliateLink $affiliateLink)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'destination_url' => 'required|url',
            'content_id' => 'nullable|exists:contents,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $affiliateLink->update([
            'name' => $validated['name'],
            'original_url' => $validated['destination_url'],
            'content_id' => $validated['content_id'] ?? null,
            'commission_rate' => $validated['commission_rate'] ?? 0,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate link updated successfully.');
    }

    /**
     * Remove the specified affiliate link from storage.
     *
     * @param  \App\Models\AffiliateLink  $affiliateLink
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AffiliateLink $affiliateLink)
    {
        $affiliateLink->delete();

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate link deleted successfully.');
    }
    
    /**
     * Generate a tracking URL for an affiliate link.
     *
     * @param  string  $destinationUrl
     * @return string
     */
    private function generateTrackingUrl($destinationUrl)
    {
        $trackingId = uniqid('aff_');
        $baseUrl = config('app.url') . '/track/' . $trackingId;
        
        return $baseUrl . '?url=' . urlencode($destinationUrl);
    }
    
    /**
     * Show statistics for a specific affiliate link.
     *
     * @param  \App\Models\AffiliateLink  $affiliateLink
     * @return \Illuminate\View\View
     */
    public function stats(AffiliateLink $affiliateLink)
    {
        $clicks = $affiliateLink->trackings()->where('event_type', 'click')->count();
        $impressions = $affiliateLink->trackings()->where('event_type', 'impression')->count();
        $conversions = $affiliateLink->trackings()->where('event_type', 'conversion')->count();
        $revenue = $affiliateLink->trackings()->where('event_type', 'conversion')->sum('value');
        
        $clickRate = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
        $conversionRate = $clicks > 0 ? ($conversions / $clicks) * 100 : 0;
        
        // Get daily stats for the last 30 days
        $dailyStats = $affiliateLink->trackings()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN event_type = "click" THEN 1 ELSE 0 END) as clicks'),
                DB::raw('SUM(CASE WHEN event_type = "impression" THEN 1 ELSE 0 END) as impressions'),
                DB::raw('SUM(CASE WHEN event_type = "conversion" THEN 1 ELSE 0 END) as conversions'),
                DB::raw('SUM(CASE WHEN event_type = "conversion" THEN value ELSE 0 END) as revenue')
            )
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();
            
        // Format for charts
        $dates = [];
        $clicksData = [];
        $impressionsData = [];
        $conversionsData = [];
        $revenueData = [];
        
        foreach ($dailyStats as $stat) {
            $dates[] = $stat->date;
            $clicksData[] = $stat->clicks;
            $impressionsData[] = $stat->impressions;
            $conversionsData[] = $stat->conversions;
            $revenueData[] = $stat->revenue;
        }
        
        return view('admin.affiliates.stats', compact(
            'affiliateLink',
            'clicks',
            'impressions',
            'conversions',
            'revenue',
            'clickRate',
            'conversionRate',
            'dates',
            'clicksData',
            'impressionsData',
            'conversionsData',
            'revenueData'
        ));
    }
}
