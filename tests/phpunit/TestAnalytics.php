<?php
/**
 * Test analytics functionality
 */

class TestAnalytics extends WP_Mock\Tools\TestCase {
    /**
     * Test getting post analytics
     */
    public function testGetPostAnalytics() {
        global $wpdb;

        // Mock database query
        $mock_results = [
            (object) [
                'platform' => 'twitter',
                'impressions' => 100,
                'engagements' => 10,
                'clicks' => 5,
                'shares' => 2,
            ],
            (object) [
                'platform' => 'facebook',
                'impressions' => 200,
                'engagements' => 20,
                'clicks' => 15,
                'shares' => 8,
            ],
        ];

        WP_Mock::userFunction('wpdb::get_results', [
            'args' => [WP_Mock\Functions::type('string')],
            'return' => $mock_results,
        ]);

        // Call get_post_analytics method
        $result = WPSMA\Core\Analytics::get_post_analytics(123);

        // Assert that analytics data is correct
        $this->assertArrayHasKey('twitter', $result);
        $this->assertArrayHasKey('facebook', $result);
        $this->assertEquals(100, $result['twitter']['impressions']);
        $this->assertEquals(200, $result['facebook']['impressions']);
    }

    /**
     * Test getting analytics summary
     */
    public function testGetAnalyticsSummary() {
        global $wpdb;

        // Mock database query
        $mock_results = [
            (object) [
                'platform' => 'twitter',
                'total_impressions' => 500,
                'total_engagements' => 50,
                'total_clicks' => 25,
                'total_shares' => 10,
            ],
            (object) [
                'platform' => 'facebook',
                'total_impressions' => 1000,
                'total_engagements' => 150,
                'total_clicks' => 100,
                'total_shares' => 50,
            ],
        ];

        WP_Mock::userFunction('wpdb::get_results', [
            'args' => [WP_Mock\Functions::type('string')],
            'return' => $mock_results,
        ]);

        // Call get_analytics_summary method
        $result = WPSMA\Core\Analytics::get_analytics_summary();

        // Assert that summary data is correct
        $this->assertEquals(1500, $result['total_impressions']);
        $this->assertEquals(200, $result['total_engagements']);
        $this->assertEquals(125, $result['total_clicks']);
        $this->assertEquals(60, $result['total_shares']);
        $this->assertArrayHasKey('twitter', $result['by_platform']);
        $this->assertArrayHasKey('facebook', $result['by_platform']);
    }

    /**
     * Test updating analytics data
     */
    public function testUpdateAnalyticsData() {
        global $wpdb;

        // Mock database query
        $mock_posts = [
            (object) [
                'post_id' => 123,
                'platforms' => 'twitter,facebook',
            ],
        ];

        WP_Mock::userFunction('wpdb::get_results', [
            'args' => [WP_Mock\Functions::type('string')],
            'return' => $mock_posts,
        ]);

        WP_Mock::userFunction('get_post', [
            'args' => [123],
            'return' => (object) [
                'ID' => 123,
                'post_title' => 'Test Post',
            ],
        ]);

        // Mock store_analytics_data method
        WP_Mock::userFunction('WPSMA\Core\Analytics::store_analytics_data', [
            'args' => [
                WP_Mock\Functions::type('int'),
                WP_Mock\Functions::type('string'),
                WP_Mock\Functions::type('array'),
            ],
            'return' => true,
        ]);

        // Create analytics instance
        $analytics = new WPSMA\Core\Analytics();

        // Call update_analytics_data method
        $analytics->update_analytics_data();

        // Assert that the method completed
        $this->assertTrue(true);
    }
}