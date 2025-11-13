<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            // SEO & Website Tasks (Any business type)
            [
                'title' => 'Set up Google Business Profile',
                'description' => 'Create and verify your Google Business Profile with complete information',
                'duration_hours' => 3,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => true,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Local SEO'
            ],
            [
                'title' => 'Optimize Google Business Profile',
                'description' => 'Add photos, business hours, services, and complete all profile sections',
                'duration_hours' => 2,
                'frequency' => 'monthly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => true,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Local SEO'
            ],
            [
                'title' => 'Request Google Reviews',
                'description' => 'Ask satisfied clients to leave reviews on Google',
                'duration_hours' => 2,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => true,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Local SEO'
            ],
            
            // Social Media Tasks
            [
                'title' => 'Create Instagram Business Profile',
                'description' => 'Set up professional Instagram account with bio, profile pic, and contact info',
                'duration_hours' => 2,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Social Media'
            ],
            [
                'title' => 'Post on Instagram',
                'description' => 'Create and publish 3-4 posts showcasing your work and services',
                'duration_hours' => 4,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Social Media'
            ],
            [
                'title' => 'Create Instagram Stories',
                'description' => 'Share daily behind-the-scenes content and client results',
                'duration_hours' => 2,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Social Media'
            ],
            [
                'title' => 'Create Facebook Page',
                'description' => 'Set up professional Facebook business page',
                'duration_hours' => 2,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Social Media'
            ],
            [
                'title' => 'Post on Facebook',
                'description' => 'Share service updates, promotions, and valuable content',
                'duration_hours' => 3,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Social Media'
            ],
            [
                'title' => 'Engage with Followers',
                'description' => 'Respond to comments, DMs, and engage with your audience',
                'duration_hours' => 2,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Social Media'
            ],
            
            // Content Marketing
            [
                'title' => 'Create Before/After Content',
                'description' => 'Document client transformations and results',
                'duration_hours' => 3,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Content'
            ],
            [
                'title' => 'Write Educational Blog Posts',
                'description' => 'Create helpful articles about your services and expertise',
                'duration_hours' => 4,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => true,
                'difficulty_level' => 'intermediate',
                'category' => 'Content'
            ],
            [
                'title' => 'Create Video Content',
                'description' => 'Film short educational or promotional videos',
                'duration_hours' => 4,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'intermediate',
                'category' => 'Content'
            ],
            
            // Website Tasks
            [
                'title' => 'Set up Basic Website',
                'description' => 'Create a simple website with services, pricing, and contact info',
                'duration_hours' => 8,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'intermediate',
                'category' => 'Website'
            ],
            [
                'title' => 'Add Online Booking',
                'description' => 'Integrate booking system for appointments',
                'duration_hours' => 4,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => true,
                'difficulty_level' => 'intermediate',
                'category' => 'Website'
            ],
            [
                'title' => 'Create Service Pages',
                'description' => 'Detailed pages for each service you offer',
                'duration_hours' => 6,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => true,
                'difficulty_level' => 'beginner',
                'category' => 'Website'
            ],
            [
                'title' => 'Add Client Testimonials',
                'description' => 'Collect and display client reviews on website',
                'duration_hours' => 3,
                'frequency' => 'monthly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => true,
                'difficulty_level' => 'beginner',
                'category' => 'Website'
            ],
            
            // Email Marketing
            [
                'title' => 'Set up Email List',
                'description' => 'Create email signup form and start building your list',
                'duration_hours' => 3,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Email Marketing'
            ],
            [
                'title' => 'Send Monthly Newsletter',
                'description' => 'Share tips, updates, and special offers with subscribers',
                'duration_hours' => 3,
                'frequency' => 'monthly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Email Marketing'
            ],
            [
                'title' => 'Create Welcome Email Sequence',
                'description' => 'Automated email series for new subscribers',
                'duration_hours' => 6,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'intermediate',
                'category' => 'Email Marketing'
            ],
            
            // Paid Advertising
            [
                'title' => 'Set up Facebook Ads',
                'description' => 'Create first Facebook/Instagram ad campaign',
                'duration_hours' => 6,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'advanced',
                'category' => 'Paid Ads'
            ],
            [
                'title' => 'Set up Google Ads',
                'description' => 'Launch search ads for local services',
                'duration_hours' => 6,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => true,
                'requires_website' => true,
                'difficulty_level' => 'advanced',
                'category' => 'Paid Ads'
            ],
            [
                'title' => 'Monitor Ad Performance',
                'description' => 'Review and optimize ad campaigns',
                'duration_hours' => 2,
                'frequency' => 'weekly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'intermediate',
                'category' => 'Paid Ads'
            ],
            
            // Networking & Partnerships
            [
                'title' => 'Partner with Local Businesses',
                'description' => 'Build relationships with complementary local businesses',
                'duration_hours' => 4,
                'frequency' => 'monthly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => true,
                'requires_website' => false,
                'difficulty_level' => 'intermediate',
                'category' => 'Networking'
            ],
            [
                'title' => 'Attend Local Events',
                'description' => 'Network at community events and business meetups',
                'duration_hours' => 4,
                'frequency' => 'monthly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => true,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Networking'
            ],
            
            // Promotions & Offers
            [
                'title' => 'Create Referral Program',
                'description' => 'Set up system to reward client referrals',
                'duration_hours' => 4,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'intermediate',
                'category' => 'Marketing'
            ],
            [
                'title' => 'Run Seasonal Promotion',
                'description' => 'Create special offer for holidays or seasons',
                'duration_hours' => 3,
                'frequency' => 'quarterly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Marketing'
            ],
            [
                'title' => 'Create First-Time Client Offer',
                'description' => 'Design attractive introductory offer for new clients',
                'duration_hours' => 2,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'beginner',
                'category' => 'Marketing'
            ],
            
            // Analytics & Tracking
            [
                'title' => 'Set up Google Analytics',
                'description' => 'Install and configure website analytics',
                'duration_hours' => 2,
                'frequency' => 'once',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => true,
                'difficulty_level' => 'intermediate',
                'category' => 'Analytics'
            ],
            [
                'title' => 'Review Monthly Metrics',
                'description' => 'Analyze marketing performance and adjust strategy',
                'duration_hours' => 2,
                'frequency' => 'monthly',
                'dependencies' => null,
                'business_type' => 'service',
                'language' => 'en',
                'is_local' => false,
                'requires_website' => false,
                'difficulty_level' => 'intermediate',
                'category' => 'Analytics'
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }
    }
}
