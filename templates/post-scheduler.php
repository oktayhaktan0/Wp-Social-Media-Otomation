<!DOCTYPE html>
<html>
<head>
    <title>Schedule Post</title>
</head>
<body>
    <div class="wrap">
        <h1>Schedule Post for Social Media</h1>

        <form id="wpsma-schedule-form">
            <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="wpsma-post-title">Post Title</label></th>
                    <td>
                        <input type="text" id="wpsma-post-title" value="<?php echo esc_attr(get_the_title($post_id)); ?>" readonly>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="wpsma-platforms">Platforms</label></th>
                    <td>
                        <?php foreach ($available_platforms as $platform_key => $platform) : ?>
                            <label>
                                <input type="checkbox" name="platforms[]" value="<?php echo esc_attr($platform_key); ?>" checked>
                                <?php echo esc_html($platform['name']); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="wpsma-schedule-date">Schedule Date/Time</label></th>
                    <td>
                        <input type="datetime-local" id="wpsma-schedule-date" name="scheduled_date" required>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="wpsma-custom-message">Custom Message</label></th>
                    <td>
                        <textarea id="wpsma-custom-message" name="custom_message" rows="5" cols="50"></textarea>
                        <p class="description">Leave empty to use post title and excerpt</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="wpsma-hashtags">Hashtags</label></th>
                    <td>
                        <input type="text" id="wpsma-hashtags" name="hashtags" placeholder="#wordpress, #blogging">
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" class="button button-primary">Schedule Post</button>
                <a href="<?php echo esc_url(get_edit_post_link($post_id)); ?>" class="button">Cancel</a>
            </p>
        </form>
    </div>
</body>
</html>