<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\License;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics and recent activity.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get counts for dashboard stats
        $userCount = User::count();
        $licenseCount = License::count();
        $websiteCount = Website::count();
        $contentCount = Content::count();
        
        // Get active licenses
        $activeLicenses = License::where('status', 'active')->count();
        
        // Recent content generation
        $recentContents = Content::orderBy('created_at', 'desc')
            ->with('website')
            ->limit(5)
            ->get();
            
        // Content statistics by month (last 6 months)
        $contentStats = DB::table('contents')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
            ->get();
            
        // Format for chart display
        $months = [];
        $contentCounts = [];
        
        foreach ($contentStats as $stat) {
            $monthName = date('M', mktime(0, 0, 0, $stat->month, 1));
            $months[] = $monthName;
            $contentCounts[] = $stat->count;
        }
        
        return view('admin.dashboard.index', compact(
            'userCount', 
            'licenseCount', 
            'websiteCount', 
            'contentCount',
            'activeLicenses',
            'recentContents',
            'months',
            'contentCounts'
        ));
    }
}
