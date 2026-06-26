# WP Social Media Automation

**Advanced WordPress plugin for social media automation and analytics**

[![License: GPL v2](https://img.shields.io/badge/License-GPL_v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![WordPress](https://img.shields.io/badge/WordPress-5.8+-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4.svg)](https://www.php.net/)

## Features

✅ **Multi-platform Support** - Facebook, Instagram, LinkedIn
✅ **Scheduled Posting** - Plan content in advance with easy scheduling
✅ **Performance Analytics** - Track impressions, engagements, clicks, and shares
✅ **URL Shortening** - Built-in Bitly and Rebrandly integration
✅ **Best Time Recommendations** - AI-powered posting time suggestions
✅ **Hashtag Analysis** - Smart hashtag recommendations for better reach
✅ **React Admin Dashboard** - Modern, responsive interface
✅ **REST API** - Full API support for custom integrations
✅ **Cron Automation** - Automatic posting and analytics updates

## Requirements

- WordPress 5.8+
- PHP 7.4+
- MySQL 5.6+
- cURL extension
- JSON extension

## Installation

### Method 1: WordPress Admin (Recommended)
1. Go to **Plugins → Add New** in WordPress admin
2. Search for "WP Social Media Automation"
3. Click **Install Now** and then **Activate**

### Method 2: Manual Upload
1. Download the latest release from GitHub
2. Upload the plugin files to `/wp-content/plugins/wp-social-media-automation` directory
3. Go to **Plugins** menu in WordPress admin
4. Find "WP Social Media Automation" and click **Activate**

### Method 3: Composer
```bash
composer require oktayhaktan0/wp-social-media-automation
```

## Configuration

After activation:

1. **Connect Accounts**: Go to **Social Media → Settings** and enter API keys for each platform
2. **Enable Platforms**: Select which social networks to use
3. **Set Default Schedule**: Configure default posting times
4. **Configure URL Shortener**: Choose Bitly, Rebrandly, or none
5. **Enable Analytics**: Turn on performance tracking

## Usage

### Scheduling Posts

#### From Post Editor
1. Edit any WordPress post
2. In the "Publish" metabox, click "Schedule Post" button
3. Select platforms and schedule time
4. Click "Schedule" to confirm

#### Using PHP API
```php
// Schedule a post for multiple platforms
$post_id = 123;
$platforms = ['twitter', 'facebook', 'linkedin'];
$schedule_date = '2023-12-25 14:00:00';

$result = WPSMA\Core\Scheduler::schedule_post($post_id, $platforms, $schedule_date);

if ($result) {
    echo 'Post scheduled successfully! ID: ' . $result;
}
```

### Retrieving Analytics

```php
// Get analytics for a specific post
$post_id = 123;
$analytics = WPSMA\Core\Analytics::get_post_analytics($post_id);

// Get overall analytics summary
$summary = WPSMA\Core\Analytics::get_analytics_summary();

// Display Twitter impressions
if (isset($analytics['twitter'])) {
    echo 'Twitter Impressions: ' . $analytics['twitter']['impressions'];
}
```

### Working with Platforms

```php
// Get available platforms
$platforms = new WPSMA\Core\Platforms();
$available = $platforms->get_available_platforms();

// Get best time to post
$best_time = $platforms->get_best_time_to_post('twitter');

// Get suggested hashtags
$hashtags = $platforms->get_suggested_hashtags('My awesome blog post', 'twitter');
```

### URL Shortening

```php
// Shorten URL using configured service
$url = 'https://yourwebsite.com/long-url-here';
$platform = 'twitter';

// Bitly or Rebrandly will be used based on settings
$short_url = apply_filters('wpsma_shortened_url', $url, $platform);
```

## REST API

The plugin provides a comprehensive REST API for custom integrations.

### Endpoints

- **POST** `/wp-json/wpsma/v1/schedule-post/` - Schedule a post
- **GET** `/wp-json/wpsma/v1/scheduled-posts/` - List scheduled posts
- **GET** `/wp-json/wpsma/v1/analytics/` - Get analytics data
- **GET** `/wp-json/wpsma/v1/platforms/` - List available platforms

### Example: Schedule Post via REST API

```bash
curl -X POST \
  https://yourwebsite.com/wp-json/wpsma/v1/schedule-post/ \
  -H 'Authorization: Bearer YOUR_AUTH_TOKEN' \
  -H 'Content-Type: application/json' \
  -d '{
    "post_id": 123,
    "platforms": ["twitter", "facebook"],
    "scheduled_date": "2023-12-25 14:00:00"
  }'
```

## Development

### Setup

```bash
# Clone the repository
git clone https://github.com/oktayhaktan0/Wp-Social-Media-Otomation.git
cd wp-social-media-automation

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Build assets for development (watches for changes)
npm run dev

# Build assets for production
npm run build
```

### Running Tests

```bash
# Run PHPUnit tests
composer test

# Run tests with coverage
composer test -- --coverage-html tests/coverage

# Run specific test
./vendor/bin/phpunit tests/phpunit/TestPlugin.php
```

### Code Quality

```bash
# Run PHP CodeSniffer
composer lint

# Run ESLint
npm run lint
```

## Architecture

### Directory Structure

```
wp-social-media-automation/
├── social-media-automation.php          # Main plugin file
├── includes/
│   ├── class-plugin.php                 # Main plugin class
│   ├── class-autoloader.php             # PSR-4 autoloader
│   ├── admin/                           # Admin classes
│   │   ├── class-admin-admin.php        # Admin functionality
│   │   ├── class-admin-settings.php      # Settings management
│   │   └── class-admin-menu.php          # Menu and UI elements
│   ├── core/                            # Core functionality
│   │   ├── class-core-scheduler.php     # Post scheduling
│   │   ├── class-core-analytics.php     # Analytics tracking
│   │   ├── class-core-platforms.php      # Platform integrations
│   │   └── class-core-api.php           # REST API endpoints
│   └── integrations/                    # Third-party integrations
│       ├── class-integrations-bitly.php # Bitly URL shortener
│       └── class-integrations-rebrandly.php # Rebrandly URL shortener
├── assets/
│   ├── js/                              # JavaScript files
│   │   ├── admin.js                     # React admin app
│   │   └── public.js                    # Public scripts
│   └── css/                             # Stylesheets
│       ├── admin.css                    # Admin styles
│       └── public.css                   # Public styles
├── templates/                           # Template files
│   ├── scheduled-posts.php             # Scheduled posts list
│   ├── analytics-dashboard.php         # Analytics display
│   └── post-scheduler.php              # Post scheduling form
├── tests/                               # Test files
│   ├── phpunit/                         # PHPUnit tests
│   │   ├── TestPlugin.php               # Plugin tests
│   │   ├── TestScheduler.php            # Scheduler tests
│   │   └── TestAnalytics.php            # Analytics tests
│   └── bootstrap.php                    # Test bootstrap
├── vendor/                              # Composer dependencies
├── node_modules/                        # NPM dependencies
├── .babelrc                             # Babel configuration
├── composer.json                        # Composer config
├── package.json                         # NPM config
├── phpunit.xml                          # PHPUnit config
├── webpack.config.js                    # Webpack config
└── README.md                            # Documentation
```

### Key Components

1. **Main Plugin Class** (`WPSMA\Plugin`)
   - Initializes all components
   - Handles activation/deactivation
   - Sets up database tables and cron jobs

2. **Scheduler** (`WPSMA\Core\Scheduler`)
   - Manages post scheduling
   - Processes scheduled posts via cron
   - Handles post publishing to platforms

3. **Analytics** (`WPSMA\Core\Analytics`)
   - Tracks social media performance
   - Updates analytics via cron
   - Provides data visualization

4. **Platforms** (`WPSMA\Core\Platforms`)
   - Manages social media platform integrations
   - Provides best time recommendations
   - Generates hashtag suggestions

5. **API** (`WPSMA\Core\API`)
   - REST API endpoints
   - AJAX handlers
   - External API integrations

## Database Schema

### Tables Created

#### `wpsma_scheduled_posts`
Stores scheduled social media posts

| Column | Type | Description |
|--------|------|-------------|
| id | bigint(20) | Primary key |
| post_id | bigint(20) | WordPress post ID |
| platforms | varchar(255) | Comma-separated platforms |
| scheduled_date | datetime | When to post |
| status | varchar(20) | pending/published/failed |
| created_at | datetime | Creation timestamp |
| updated_at | datetime | Last update timestamp |

#### `wpsma_analytics`
Tracks social media performance metrics

| Column | Type | Description |
|--------|------|-------------|
| id | bigint(20) | Primary key |
| post_id | bigint(20) | WordPress post ID |
| platform | varchar(50) | Social media platform |
| post_date | datetime | When analytics recorded |
| impressions | bigint(20) | Number of impressions |
| engagements | bigint(20) | Number of engagements |
| clicks | bigint(20) | Number of clicks |
| shares | bigint(20) | Number of shares |
| created_at | datetime | Creation timestamp |

## Cron Jobs

The plugin sets up two cron jobs:

1. **`wpsma_check_scheduled_posts`** (Hourly)
   - Checks for posts that should be published
   - Processes pending scheduled posts

2. **`wpsma_update_analytics`** (Daily)
   - Fetches latest analytics from platforms
   - Updates database with new metrics

## Filters and Actions

### Filters

- `wpsma_available_platforms` - Modify available platforms
- `wpsma_shortened_url` - Custom URL shortening logic
- `wpsma_post_content` - Modify post content before publishing

### Actions

- `wpsma_before_post_publish` - Before posting to social media
- `wpsma_after_post_publish` - After posting to social media
- `wpsma_analytics_updated` - When analytics are updated

## Security

- All AJAX endpoints use nonce verification
- REST API requires proper authentication
- Database queries use $wpdb->prepare()
- API keys are stored securely in wp_options
- Admin functionality requires manage_options capability

## Performance

- Database queries are optimized with proper indexes
- Cron jobs run at appropriate intervals
- Analytics data is cached for performance
- Asset files are minified for production

## Roadmap

### Future Features

- [ ] AI-powered content suggestions
- [ ] Automatic hashtag generation
- [ ] Bulk post scheduling
- [ ] Content calendar view
- [ ] Team collaboration features
- [ ] Multi-account support per platform
- [ ] Advanced analytics reporting
- [ ] Export/import functionality

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

### Bug Reports

When reporting bugs, please include:
- WordPress version
- PHP version
- Plugin version
- Steps to reproduce
- Expected vs actual behavior
- Screenshots if applicable

### Feature Requests

Feature requests should include:
- Detailed description
- Use case scenario
- Potential implementation ideas

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Create a new Pull Request

## License

This project is licensed under the GPL-2.0+ License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please open an issue on GitHub or contact us at support@yourwebsite.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and updates.

## Credits

- **Lead Developer**: Your Name
- **Contributors**: [See contributors on GitHub](https://github.com/oktayhaktan0/Wp-Social-Media-Otomation/graphs/contributors)
- **Special Thanks**: WordPress community, WP_Mock contributors

## Donations

If you find this plugin useful, consider supporting development:

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/donate?hosted_button_id=YOUR_BUTTON_ID)

## Stay Connected

- **GitHub**: [oktayhaktan0/Wp-Social-Media-Otomation](https://github.com/oktayhaktan0/Wp-Social-Media-Otomation)
- **Website**: [haktanoktay.com](https://haktanoktay.com)
- **Twitter**: [@yourhandle](https://twitter.com/yourhandle)
- **Website**: [yourwebsite.com](https://yourwebsite.com)