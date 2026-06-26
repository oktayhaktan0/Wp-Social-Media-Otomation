<!DOCTYPE html>
<html>
<head>
    <title>Scheduled Posts</title>
</head>
<body>
    <div class="wrap">
        <h1>Scheduled Posts</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Post Title</th>
                    <th>Platforms</th>
                    <th>Scheduled Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($scheduled_posts)) : ?>
                    <?php foreach ($scheduled_posts as $post) : ?>
                        <tr>
                            <td><?php echo esc_html(get_the_title($post->post_id)); ?></td>
                            <td><?php echo esc_html($post->platforms); ?></td>
                            <td><?php echo esc_html($post->scheduled_date); ?></td>
                            <td><span class="wpsma-status-<?php echo esc_attr($post->status); ?>"><?php echo esc_html($post->status); ?></span></td>
                            <td>
                                <a href="<?php echo esc_url(get_edit_post_link($post->post_id)); ?>">Edit</a> |
                                <a href="#" class="wpsma-delete-scheduled" data-id="<?php echo esc_attr($post->id); ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">No scheduled posts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>