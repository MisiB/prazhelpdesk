<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\KnowledgeBase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SupportPortalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Create categories
        $categories = [
            [
                'name' => 'Getting Started',
                'slug' => 'getting-started',
                'description' => 'Learn the basics and get started quickly',
                'order' => 1,
            ],
            [
                'name' => 'Account & Billing',
                'slug' => 'account-billing',
                'description' => 'Manage your account and billing information',
                'order' => 2,
            ],
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'description' => 'Technical issues and troubleshooting',
                'order' => 3,
            ],
            [
                'name' => 'Features',
                'slug' => 'features',
                'description' => 'Learn about features and how to use them',
                'order' => 4,
            ],
            [
                'name' => 'Integration',
                'slug' => 'integration',
                'description' => 'Integration guides and API documentation',
                'order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Create tags
        $tags = [
            ['name' => 'API', 'slug' => 'api', 'color' => '#3B82F6'],
            ['name' => 'Authentication', 'slug' => 'authentication', 'color' => '#10B981'],
            ['name' => 'Billing', 'slug' => 'billing', 'color' => '#F59E0B'],
            ['name' => 'Integration', 'slug' => 'integration', 'color' => '#8B5CF6'],
            ['name' => 'Tutorial', 'slug' => 'tutorial', 'color' => '#EC4899'],
            ['name' => 'Quick Start', 'slug' => 'quick-start', 'color' => '#06B6D4'],
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(
                ['slug' => $tagData['slug']],
                $tagData
            );
        }

        // Create sample knowledge base articles
        $articles = [
            [
                'title' => 'How to Get Started with Our Platform',
                'slug' => 'how-to-get-started',
                'content' => "# Getting Started\n\nWelcome to our platform! This guide will help you get started quickly.\n\n## Step 1: Create Your Account\n\nFirst, create your account by visiting the sign-up page. You'll need to provide:\n- Your name\n- Email address\n- A secure password\n\n## Step 2: Complete Your Profile\n\nOnce registered, complete your profile with additional information to personalize your experience.\n\n## Step 3: Explore Features\n\nTake some time to explore our features:\n- Dashboard overview\n- Knowledge base\n- Support tickets\n- API integration\n\n## Need Help?\n\nIf you need assistance, our support team is here to help!",
                'excerpt' => 'A comprehensive guide to getting started with our platform',
                'category' => 'getting-started',
                'tags' => ['quick-start', 'tutorial'],
                'is_published' => true,
                'is_featured' => true,
                'views' => 150,
                'helpful_count' => 45,
                'not_helpful_count' => 3,
            ],
            [
                'title' => 'Understanding API Authentication',
                'slug' => 'understanding-api-authentication',
                'content' => "# API Authentication Guide\n\nLearn how to authenticate with our API.\n\n## Getting Your API Key\n\n1. Log in to your dashboard\n2. Navigate to Settings > API Keys\n3. Click 'Generate New API Key'\n4. Save your key securely\n\n## Using Your API Key\n\nInclude your API key in the Authorization header:\n\n```\nAuthorization: Bearer YOUR_API_KEY\n```\n\n## Best Practices\n\n- Never share your API key\n- Rotate keys regularly\n- Use environment variables\n- Implement rate limiting\n\n## Example Request\n\n```bash\ncurl -H 'Authorization: Bearer YOUR_API_KEY' \\\n  https://api.example.com/v1/users\n```",
                'excerpt' => 'Learn how to authenticate and secure your API requests',
                'category' => 'integration',
                'tags' => ['api', 'authentication'],
                'is_published' => true,
                'is_featured' => true,
                'views' => 230,
                'helpful_count' => 78,
                'not_helpful_count' => 5,
            ],
            [
                'title' => 'How to Update Billing Information',
                'slug' => 'update-billing-information',
                'content' => "# Update Billing Information\n\nKeep your billing information up to date.\n\n## Accessing Billing Settings\n\n1. Click on your profile icon\n2. Select 'Account Settings'\n3. Navigate to 'Billing' tab\n\n## Updating Payment Method\n\nYou can update:\n- Credit card information\n- Billing address\n- Payment preferences\n\n## Managing Subscriptions\n\n- View current plan\n- Upgrade or downgrade\n- Cancel subscription\n- View invoice history\n\n## Security\n\nAll payment information is encrypted and PCI-DSS compliant.",
                'excerpt' => 'Step-by-step guide to updating your billing and payment information',
                'category' => 'account-billing',
                'tags' => ['billing'],
                'is_published' => true,
                'is_featured' => false,
                'views' => 95,
                'helpful_count' => 32,
                'not_helpful_count' => 2,
            ],
            [
                'title' => 'Troubleshooting Common Login Issues',
                'slug' => 'troubleshooting-login-issues',
                'content' => "# Login Issues Troubleshooting\n\n## Forgot Password\n\n1. Click 'Forgot Password' on login page\n2. Enter your email address\n3. Check your inbox for reset link\n4. Create a new password\n\n## Account Locked\n\nIf your account is locked after multiple failed attempts:\n- Wait 30 minutes for automatic unlock\n- Or contact support for immediate assistance\n\n## Browser Issues\n\nTry:\n- Clear browser cache and cookies\n- Use incognito/private mode\n- Try a different browser\n- Disable browser extensions\n\n## Still Having Issues?\n\nContact our support team with:\n- Your email address\n- Browser and version\n- Error messages received",
                'excerpt' => 'Quick solutions for common login and access issues',
                'category' => 'technical-support',
                'tags' => ['authentication'],
                'is_published' => true,
                'is_featured' => false,
                'views' => 180,
                'helpful_count' => 65,
                'not_helpful_count' => 8,
            ],
            [
                'title' => 'Using the AI Search Feature',
                'slug' => 'using-ai-search-feature',
                'content' => "# AI-Powered Search Guide\n\nOur AI search helps you find answers faster.\n\n## How It Works\n\nThe AI analyzes your question and:\n- Understands context and intent\n- Searches across all articles\n- Ranks results by relevance\n- Provides highlighted excerpts\n\n## Search Tips\n\n1. **Be specific**: 'How to reset password' vs 'password'\n2. **Use natural language**: Ask questions naturally\n3. **Include context**: Mention what you're trying to do\n4. **Check suggestions**: Review AI-suggested articles\n\n## Example Searches\n\nGood:\n- 'How do I integrate the API with my application?'\n- 'What payment methods are supported?'\n- 'How to export my data?'\n\nLess effective:\n- 'API'\n- 'payment'\n- 'export'\n\n## Feedback\n\nHelp improve our AI by marking articles as helpful or not helpful!",
                'excerpt' => 'Learn how to use our AI-powered search to find answers quickly',
                'category' => 'features',
                'tags' => ['tutorial'],
                'is_published' => true,
                'is_featured' => true,
                'views' => 120,
                'helpful_count' => 55,
                'not_helpful_count' => 4,
            ],
        ];

        foreach ($articles as $articleData) {
            $category = Category::where('slug', $articleData['category'])->first();
            $tagSlugs = $articleData['tags'];
            
            unset($articleData['category'], $articleData['tags']);
            
            $article = KnowledgeBase::firstOrCreate(
                ['slug' => $articleData['slug']],
                array_merge($articleData, [
                    'category_id' => $category->id,
                    'author_id' => $admin->id,
                ])
            );

            // Attach tags
            $tags = Tag::whereIn('slug', $tagSlugs)->pluck('id');
            $article->tags()->sync($tags);
        }

        $this->command->info('Support portal seeded successfully!');
    }
}

