<?php
/**
 * Admin page template — submissions table.
 *
 * @package WOP_CF
 *
 * @var array<int, object> $submissions Provided by WOP_CF_Admin_Page::render().
 */

if ( ! defined('ABSPATH')) {
	exit;
}
?>
<div class="wrap">
  <h1 class="wp-heading-inline"><?php esc_html_e('Contact Form Submissions', 'wop-contact-form'); ?></h1>

	<?php if (empty($submissions)) : ?>
      <p><?php esc_html_e('No submissions yet.', 'wop-contact-form'); ?></p>
	<?php else : ?>
      <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
          <th scope="col"><?php esc_html_e('Name', 'wop-contact-form'); ?></th>
          <th scope="col"><?php esc_html_e('Phone', 'wop-contact-form'); ?></th>
          <th scope="col"><?php esc_html_e('Email', 'wop-contact-form'); ?></th>
          <th scope="col"><?php esc_html_e('City', 'wop-contact-form'); ?></th>
          <th scope="col"><?php esc_html_e('Message', 'wop-contact-form'); ?></th>
          <th scope="col"><?php esc_html_e('File', 'wop-contact-form'); ?></th>
          <th scope="col"><?php esc_html_e('Date', 'wop-contact-form'); ?></th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($submissions as $row) : ?>
          <tr>
            <td><?php echo esc_html($row->full_name); ?></td>
            <td><?php echo esc_html($row->phone); ?></td>
            <td>
              <a href="<?php echo esc_url('mailto:' . $row->email); ?>">
				  <?php echo esc_html($row->email); ?>
              </a>
            </td>
            <td><?php echo esc_html($row->city); ?></td>
            <td><?php echo nl2br(esc_html($row->project_details)); ?></td>
            <td>
				<?php
				if ( ! empty($row->attachment_id)) {
					$file_url = wp_get_attachment_url((int) $row->attachment_id);
					if ($file_url) {
						printf(
							'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
							esc_url($file_url),
							esc_html__('View file', 'wop-contact-form')
						);
					} else {
						esc_html_e('— (file missing)', 'wop-contact-form');
					}
				} else {
					echo '—';
				}
				?>
            </td>
            <td>
				<?php echo esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $row->created_at)); ?>
            </td>
          </tr>
		<?php endforeach; ?>
        </tbody>
      </table>
	<?php endif; ?>
</div>
