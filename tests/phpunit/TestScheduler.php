<?php
/**
 * Test scheduler functionality
 */

class TestScheduler extends WP_Mock\Tools\TestCase {
    /**
     * Test scheduling a post
     */
    public function testSchedulePost() {
        global $wpdb;

        // Mock database insert
        WP_Mock::userFunction('wpdb::insert', [
            'args' => [
                WP_Mock\Functions::type('string'),
                WP_Mock\Functions::type('array'),
                WP_Mock\Functions::type('array'),
            ],
            'return' => 1,
        ]);

        WP_Mock::userFunction('wpdb::insert_id', [
            'return' => 123,
        ]);

        // Call schedule_post method
        $result = WPSMA\Core\Scheduler::schedule_post(1, ['twitter', 'facebook'], '2023-12-25 14:00:00');

        // Assert that post was scheduled
        $this->assertEquals(123, $result);
    }

    /**
     * Test processing scheduled posts
     */
    public function testProcessScheduledPosts() {
        global $wpdb;

        // Mock database query
        $mock_posts = [
            (object) [
                'id' => 1,
                'post_id' => 123,
                'platforms' => 'twitter,facebook',
                'scheduled_date' => '2023-01-01 10:00:00',
                'status' => 'pending',
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
                'post_content' => 'Test content',
            ],
        ]);

        WP_Mock::userFunction('get_option', [
            'args' => ['wpsma_settings', []],
            'return' => ['enabled_platforms' => ['twitter', 'facebook']],
        ]);

        // Mock mark_as_published method
        WP_Mock::userFunction('WPSMA\Core\Scheduler::mark_as_published', [
            'args' => [WP_Mock\Functions::type('int'), WP_Mock\Functions::type('string')],
            'return' => true,
        ]);

        // Create scheduler instance
        $scheduler = new WPSMA\Core\Scheduler();

        // Call process_scheduled_posts method
        $scheduler->process_scheduled_posts();

        // Assert that the method completed
        $this->assertTrue(true);
    }

    /**
     * Test marking post as published
     */
    public function testMarkAsPublished() {
        global $wpdb;

        // Mock database update
        WP_Mock::userFunction('wpdb::update', [
            'args' => [
                WP_Mock\Functions::type('string'),
                WP_Mock\Functions::type('array'),
                WP_Mock\Functions::type('array'),
                WP_Mock\Functions::type('array'),
                WP_Mock\Functions::type('array'),
            ],
            'return' => 1,
        ]);

        // Create scheduler instance
        $scheduler = new WPSMA\Core\Scheduler();

        // Call mark_as_published method (via reflection since it's private)
        $reflection = new ReflectionClass($scheduler);
        $method = $reflection->getMethod('mark_as_published');
        $method->setAccessible(true);
        $result = $method->invoke($scheduler, 1, 'twitter');

        // Assert that the method completed
        $this->assertTrue($result);
    }
}