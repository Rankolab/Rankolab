<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@rankolab.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => true,
                'remember_token' => Str::random(10),
            ]
        );
        
        // Create a demo user
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@rankolab.com'],
            [
                'name' => 'Demo User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => false,
                'remember_token' => Str::random(10),
            ]
        );
        
        // Create a license for demo user if it doesn't exist
        if (!$demoUser->licenses()->exists()) {
            $license = new License();
            $license->user_id = $demoUser->id;
            $license->license_key = strtoupper(Str::random(16));
            $license->plan = 'pro';
            $license->status = 'active';
            $license->max_websites = 5;
            $license->max_content_per_month = 50;
            $license->expires_at = now()->addYear();
            $license->save();
            
            // Create a demo website
            if (!$demoUser->websites()->exists()) {
                $website = new Website();
                $website->user_id = $demoUser->id;
                $website->url = 'https://demo.rankolab.com';
                $website->name = 'Demo Website';
                $website->primary_keyword = 'seo content automation';
                $website->description = 'A demo website for Rankolab';
                $website->save();
                
                // Register the domain with the license
                $license->registerDomain('demo.rankolab.com');
            }
        }
    }
}
