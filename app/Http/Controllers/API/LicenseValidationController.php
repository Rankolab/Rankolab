<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LicenseValidationController extends Controller
{
    /**
     * Validate a license key for the WordPress plugin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
        ]);
        
        Log::info('License validation attempt', [
            'license_key' => $validated['license_key'],
            'domain' => $validated['domain'],
            'ip' => $request->ip(),
        ]);
        
        // Find the license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Check if license is active
        if ($license->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'License is ' . $license->status,
            ], 401);
        }
        
        // Check if license has expired
        if ($license->expires_at && now()->gt($license->expires_at)) {
            $license->update(['status' => 'expired']);
            
            return response()->json([
                'success' => false,
                'message' => 'License has expired',
            ], 401);
        }
        
        // Check if domain is allowed
        $domains = $license->domains ? json_decode($license->domains, true) : [];
        
        // If no domains are registered yet, register this one
        if (empty($domains)) {
            $domains = [$validated['domain']];
            $license->update(['domains' => json_encode($domains)]);
        } 
        // If domain not in allowed list and we've reached max domains
        elseif (!in_array($validated['domain'], $domains) && count($domains) >= $license->max_websites) {
            return response()->json([
                'success' => false,
                'message' => 'License has reached maximum number of websites',
            ], 401);
        }
        // If domain not in allowed list but we can add more
        elseif (!in_array($validated['domain'], $domains)) {
            $domains[] = $validated['domain'];
            $license->update(['domains' => json_encode($domains)]);
        }
        
        // Return success with license details
        return response()->json([
            'success' => true,
            'message' => 'License validated successfully',
            'data' => [
                'plan' => $license->plan,
                'expires_at' => $license->expires_at ? $license->expires_at->toDateString() : null,
                'max_websites' => $license->max_websites,
                'max_content_per_month' => $license->max_content_per_month,
                'features' => $this->getFeaturesForPlan($license->plan),
            ],
        ]);
    }
    
    /**
     * Deactivate a license for a specific domain.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
        ]);
        
        // Find the license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Check if domain is in the allowed list
        $domains = $license->domains ? json_decode($license->domains, true) : [];
        
        if (in_array($validated['domain'], $domains)) {
            // Remove domain from list
            $domains = array_diff($domains, [$validated['domain']]);
            $license->update(['domains' => json_encode(array_values($domains))]);
            
            return response()->json([
                'success' => true,
                'message' => 'License deactivated for this domain',
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Domain not registered with this license',
        ], 400);
    }
    
    /**
     * Check usage stats for a license.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function usage(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
        ]);
        
        // Find the license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Get content count for current month
        $contentThisMonth = $license->user->websites()
            ->with(['contents' => function ($query) {
                $query->whereYear('created_at', now()->year)
                      ->whereMonth('created_at', now()->month);
            }])
            ->get()
            ->pluck('contents')
            ->flatten()
            ->count();
        
        // Get registered domains
        $domains = $license->domains ? json_decode($license->domains, true) : [];
        
        return response()->json([
            'success' => true,
            'data' => [
                'content_used_this_month' => $contentThisMonth,
                'content_remaining' => $license->max_content_per_month - $contentThisMonth,
                'domains_used' => count($domains),
                'domains_remaining' => $license->max_websites - count($domains),
                'registered_domains' => $domains,
            ],
        ]);
    }
    
    /**
     * Get features available for a specific plan.
     *
     * @param  string  $plan
     * @return array
     */
    private function getFeaturesForPlan($plan)
    {
        $features = [
            'free' => [
                'content_generation' => true,
                'domain_analysis' => true,
                'readability_check' => true,
                'plagiarism_check' => false,
                'ai_detection_bypass' => false,
                'seo_optimization' => false,
                'search_console_integration' => false,
            ],
            'basic' => [
                'content_generation' => true,
                'domain_analysis' => true,
                'readability_check' => true,
                'plagiarism_check' => true,
                'ai_detection_bypass' => false,
                'seo_optimization' => true,
                'search_console_integration' => false,
            ],
            'pro' => [
                'content_generation' => true,
                'domain_analysis' => true,
                'readability_check' => true,
                'plagiarism_check' => true,
                'ai_detection_bypass' => true,
                'seo_optimization' => true,
                'search_console_integration' => true,
            ],
            'enterprise' => [
                'content_generation' => true,
                'domain_analysis' => true,
                'readability_check' => true,
                'plagiarism_check' => true,
                'ai_detection_bypass' => true,
                'seo_optimization' => true,
                'search_console_integration' => true,
                'custom_ai_model' => true,
                'priority_support' => true,
            ],
        ];
        
        return $features[$plan] ?? $features['free'];
    }
}
