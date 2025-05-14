<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // General settings
        $this->createSetting(
            'site_name', 
            'Rankolab', 
            'general', 
            'Site name'
        );
        
        $this->createSetting(
            'company_name', 
            'Rankolab Inc.', 
            'general', 
            'Company name for legal documents'
        );
        
        $this->createSetting(
            'support_email', 
            'support@rankolab.com', 
            'general', 
            'Support email address'
        );
        
        $this->createSetting(
            'contact_phone', 
            '+1-555-123-4567', 
            'general', 
            'Contact phone number'
        );
        
        // API settings
        $this->createSetting(
            'openai_api_key', 
            env('OPENAI_API_KEY', ''), 
            'api', 
            'OpenAI API key for content generation'
        );
        
        $this->createSetting(
            'google_api_key', 
            env('GOOGLE_API_KEY', ''), 
            'api', 
            'Google API key for Search Console and PageSpeed'
        );
        
        $this->createSetting(
            'moz_access_id', 
            env('MOZ_ACCESS_ID', ''), 
            'api', 
            'Moz API access ID for domain analysis'
        );
        
        $this->createSetting(
            'moz_secret_key', 
            env('MOZ_SECRET_KEY', ''), 
            'api', 
            'Moz API secret key'
        );
        
        $this->createSetting(
            'copyscape_api_key', 
            env('COPYSCAPE_API_KEY', ''), 
            'api', 
            'Copyscape API key for plagiarism checks'
        );
        
        // Content settings
        $this->createSetting(
            'default_min_words', 
            '1000', 
            'content', 
            'Default minimum word count for content generation'
        );
        
        $this->createSetting(
            'default_plagiarism_threshold', 
            '10', 
            'content', 
            'Default maximum plagiarism score allowed (%)'
        );
        
        $this->createSetting(
            'default_readability_threshold', 
            '70', 
            'content', 
            'Default minimum readability score required'
        );
        
        $this->createSetting(
            'ai_model', 
            env('AI_MODEL', 'gpt-3.5-turbo'), 
            'content', 
            'Default AI model for content generation'
        );
        
        // Email settings
        $this->createSetting(
            'mail_driver', 
            env('MAIL_DRIVER', 'smtp'), 
            'email', 
            'Email service driver'
        );
        
        $this->createSetting(
            'mail_host', 
            env('MAIL_HOST', 'smtp.gmail.com'), 
            'email', 
            'Email service host'
        );
        
        $this->createSetting(
            'mail_port', 
            env('MAIL_PORT', '587'), 
            'email', 
            'Email service port'
        );
        
        $this->createSetting(
            'mail_encryption', 
            env('MAIL_ENCRYPTION', 'tls'), 
            'email', 
            'Email encryption type'
        );
    }
    
    /**
     * Create or update a setting
     *
     * @param string $key
     * @param string $value
     * @param string $category
     * @param string $description
     * @return void
     */
    private function createSetting($key, $value, $category, $description)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'category' => $category,
                'description' => $description,
            ]
        );
    }
}
