<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\WebsiteController;
use App\Http\Controllers\Api\WebsiteDesignController;
use App\Http\Controllers\Api\ContentPlanController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\RssFeedController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Api\LinkBuildingController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\ChatbotController;

// Import Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\LicenseController as AdminLicenseController;
use App\Http\Controllers\Admin\WebsiteController as AdminWebsiteController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post("/auth/register", [AuthController::class, "register"]);
Route::post("/auth/login", [AuthController::class, "login"]);

// Protected routes (require authentication via Sanctum)
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/auth/logout", [AuthController::class, "logout"]);
    
    // User details route
    Route::get("/user", function (Request $request) {
        return $request->user();
    });

    // License Management Routes
    Route::post("/license/validate", [LicenseController::class, "validateLicense"]);
    Route::get("/license/status", [LicenseController::class, "getStatus"]);

    // Website Management Routes
    Route::post("/websites", [WebsiteController::class, "store"]);
    Route::get("/websites/{website_id}/metrics", [WebsiteController::class, "getMetrics"]);

    // Website Design Route
    Route::post("/websites/{website_id}/design", [WebsiteDesignController::class, "storeOrUpdate"]);

    // Content Planning Route
    Route::post("/websites/{website_id}/content-plan", [ContentPlanController::class, "store"]);

    // Content Generation Routes
    Route::post("/websites/{website_id}/content", [ContentController::class, "store"]);
    Route::post("/websites/{website_id}/content/publish", [ContentController::class, "publish"]);

    // RSS Feed Route
    Route::post("/websites/{website_id}/rss-feeds", [RssFeedController::class, "store"]);

    // Performance Tracking Route
    Route::get("/websites/{website_id}/performance", [PerformanceController::class, "getPerformance"]);

    // Link Building Route
    Route::post("/websites/{website_id}/links", [LinkBuildingController::class, "store"]);

    // Social Media Route
    Route::post("/websites/{website_id}/social-posts", [SocialMediaController::class, "store"]);

    // Newsletter Route
    Route::post("/websites/{website_id}/newsletters", [NewsletterController::class, "store"]);

    // Chatbot Route
    Route::post("/websites/{website_id}/chatbot/log", [ChatbotController::class, "logInteraction"]);

    // Add any other minor protected routes here based on Rankolab System APIs.markdown
});

// Admin Routes (require authentication AND admin role)
Route::prefix("admin")->middleware(["auth:sanctum", "admin"])->group(function () {
    // Dashboard
    Route::get("/dashboard/stats", [AdminDashboardController::class, "getStats"]); // Placeholder method
    Route::get("/dashboard/health", [AdminDashboardController::class, "getHealth"]); // Placeholder method

    // User Management
    Route::apiResource("users", AdminUserController::class);

    // License Management
    Route::apiResource("licenses", AdminLicenseController::class);

    // Website Management (Likely read-only for admin, maybe status updates)
    Route::get("websites", [AdminWebsiteController::class, "index"]);
    Route::get("websites/{website}", [AdminWebsiteController::class, "show"]);
    // Route::put("websites/{website}/status", [AdminWebsiteController::class, "updateStatus"]); // Example if status management is needed

    // Content Management (Read-only, maybe moderation)
    Route::get("content", [AdminContentController::class, "index"]);
    Route::get("content/{content}", [AdminContentController::class, "show"]);
    // Route::put("content/{content}/status", [AdminContentController::class, "updateStatus"]); // Example for moderation

    // Monitoring
    Route::get("monitoring/logs", [AdminMonitoringController::class, "getApiLogs"]); // Placeholder method
    Route::get("monitoring/jobs", [AdminMonitoringController::class, "getJobQueueStatus"]); // Placeholder method
    Route::post("monitoring/jobs/retry/{failedJobId}", [AdminMonitoringController::class, "retryJob"]); // Placeholder method
    Route::delete("monitoring/jobs/delete/{failedJobId}", [AdminMonitoringController::class, "deleteJob"]); // Placeholder method

    // Blog Management (Added based on user request - Step 007)
    Route::get("blogs", [AdminContentController::class, "indexBlogs"]);
    Route::post("blogs", [AdminContentController::class, "storeBlog"]);
    // Use the existing show, update, destroy methods from the apiResource for content
    Route::get("blogs/{content}", [AdminContentController::class, "show"]); // Reuse show
    Route::put("blogs/{content}", [AdminContentController::class, "update"]); // Reuse update for blogs
    Route::delete("blogs/{content}", [AdminContentController::class, "destroy"]); // Reuse destroy
});


