<!DOCTYPE html>
<html>
<head>
    <title>Analytics Dashboard</title>
</head>
<body>
    <div class="wrap">
        <h1>Social Media Analytics Dashboard</h1>

        <div class="wpsma-analytics-summary">
            <div class="wpsma-card">
                <h3>Total Impressions</h3>
                <p class="wpsma-stat-number"><?php echo esc_html($analytics['total_impressions']); ?></p>
            </div>

            <div class="wpsma-card">
                <h3>Total Engagements</h3>
                <p class="wpsma-stat-number"><?php echo esc_html($analytics['total_engagements']); ?></p>
            </div>

            <div class="wpsma-card">
                <h3>Total Clicks</h3>
                <p class="wpsma-stat-number"><?php echo esc_html($analytics['total_clicks']); ?></p>
            </div>

            <div class="wpsma-card">
                <h3>Total Shares</h3>
                <p class="wpsma-stat-number"><?php echo esc_html($analytics['total_shares']); ?></p>
            </div>
        </div>

        <div class="wpsma-platform-analytics">
            <h2>Performance by Platform</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Impressions</th>
                        <th>Engagements</th>
                        <th>Clicks</th>
                        <th>Shares</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($analytics['by_platform'])) : ?>
                        <?php foreach ($analytics['by_platform'] as $platform => $data) : ?>
                            <tr>
                                <td><?php echo esc_html(ucfirst($platform)); ?></td>
                                <td><?php echo esc_html($data['impressions']); ?></td>
                                <td><?php echo esc_html($data['engagements']); ?></td>
                                <td><?php echo esc_html($data['clicks']); ?></td>
                                <td><?php echo esc_html($data['shares']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5">No analytics data available yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="wpsma-chart-container">
            <canvas id="wpsma-analytics-chart" width="800" height="400"></canvas>
        </div>
    </div>
</body>
</html>