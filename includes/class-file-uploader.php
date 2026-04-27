<?php
/**
 * Handles file uploads via the standard WordPress media mechanism.
 *
 * Returns either a positive attachment ID, or a WP_Error describing why
 * the upload was rejected.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_File_Uploader {

	const MAX_FILE_SIZE_MB = 5;

	const ALLOWED_MIME_TYPES = array(
		'image/jpeg',
		'image/pjpeg',
		'image/png',
	);

	const ALLOWED_EXTENSIONS = array('jpg', 'jpeg', 'png');

	/**
	 * Upload a single file from the $_FILES array and create a WP attachment.
	 *
	 * @param string $field_name Key in $_FILES (the form field name).
	 *
	 * @return int|WP_Error Attachment ID on success, WP_Error on failure.
	 */
	public function upload($field_name) {
		if (empty($_FILES[$field_name]) || ! isset($_FILES[$field_name]['error'])) {
			return new WP_Error('no_file', __('No file was uploaded.', 'wop-contact-form'));
		}

		$file = $_FILES[$field_name];

		if (UPLOAD_ERR_NO_FILE === (int) $file['error']) {
			return new WP_Error('no_file', __('No file was uploaded.', 'wop-contact-form'));
		}

		$validation = $this->validate($file);
		if (is_wp_error($validation)) {
			return $validation;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$overrides = array(
			'test_form' => false,
			'mimes'     => array(
				'jpg|jpeg' => 'image/jpeg',
				'png'      => 'image/png',
			),
		);

		$attachment_id = media_handle_upload($field_name, 0, array(), $overrides);

		if (is_wp_error($attachment_id)) {
			return $attachment_id;
		}

		return (int) $attachment_id;
	}

	/**
	 * Validate uploaded file before passing it to WP core.
	 *
	 * @param array $file One entry from $_FILES.
	 *
	 * @return true|WP_Error
	 */
	private function validate(array $file) {
		if ( ! empty($file['error']) && UPLOAD_ERR_OK !== (int) $file['error']) {
			return new WP_Error('upload_error', $this->php_upload_error_message((int) $file['error']));
		}

		$max_bytes = self::MAX_FILE_SIZE_MB * 1024 * 1024;
		if ((int) $file['size'] > $max_bytes) {
			return new WP_Error(
				'file_too_large',
				sprintf(
					__('File is too large. Maximum size is %d MB.', 'wop-contact-form'),
					self::MAX_FILE_SIZE_MB
				)
			);
		}

		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if ( ! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
			return new WP_Error('wrong_extension', __('Only JPG and PNG files are allowed.', 'wop-contact-form'));
		}

		$check = wp_check_filetype(
			$file['name'],
			array(
				'jpg|jpeg' => 'image/jpeg',
				'png'      => 'image/png',
			)
		);
		if (empty($check['type']) || ! in_array($check['type'], self::ALLOWED_MIME_TYPES, true)) {
			return new WP_Error('wrong_mime', __('Only JPG and PNG files are allowed.', 'wop-contact-form'));
		}

		$finfo_type = $this->detect_mime_type($file['tmp_name']);
		if ($finfo_type && ! in_array($finfo_type, self::ALLOWED_MIME_TYPES, true)) {
			return new WP_Error('wrong_mime', __('Only JPG and PNG files are allowed.', 'wop-contact-form'));
		}

		return true;
	}

	/**
	 * Detect MIME type by reading actual file bytes (when the finfo extension is available).
	 *
	 * @param string $path
	 *
	 * @return string|null
	 */
	private function detect_mime_type($path) {
		if ( ! function_exists('finfo_open')) {
			return null;
		}
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		if ( ! $finfo) {
			return null;
		}
		$mime = finfo_file($finfo, $path);
		finfo_close($finfo);

		return $mime ? $mime : null;
	}

	/**
	 * Translate PHP UPLOAD_ERR_* constants into a human-readable message.
	 *
	 * @param int $code
	 *
	 * @return string
	 */
	private function php_upload_error_message($code) {
		switch ($code) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return __('File is too large.', 'wop-contact-form');
			case UPLOAD_ERR_PARTIAL:
				return __('File was only partially uploaded. Please try again.', 'wop-contact-form');
			case UPLOAD_ERR_NO_TMP_DIR:
			case UPLOAD_ERR_CANT_WRITE:
			case UPLOAD_ERR_EXTENSION:
				return __('Server could not save the uploaded file.', 'wop-contact-form');
			default:
				return __('File upload failed.', 'wop-contact-form');
		}
	}
}
