<?php
/**
 * AJAX handler for form submissions.
 *
 * Endpoint: admin-ajax.php?action=wop_cf_submit
 * Accessible to logged-in and anonymous users (form is public).
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class WOP_CF_Ajax_Handler {

	const ACTION = 'wop_cf_submit';

	/**
	 * @var WOP_CF_Form_Validator
	 */
	private $validator;

	/**
	 * @var WOP_CF_Submission_Repository
	 */
	private $repository;

	public function __construct(
		WOP_CF_Form_Validator $validator,
		WOP_CF_Submission_Repository $repository
	) {
		$this->validator  = $validator;
		$this->repository = $repository;
	}

	public function register() {
		add_action('wp_ajax_' . self::ACTION, array($this, 'handle'));
		add_action('wp_ajax_nopriv_' . self::ACTION, array($this, 'handle'));
	}

	/**
	 * Handle the AJAX submission.
	 */
	public function handle() {
		// 1. Verify nonce — rejects requests not originating from our form.
		if ( ! check_ajax_referer(WOP_CF_Assets::NONCE_ACTION, 'nonce', false)) {
			wp_send_json_error(
				array('message' => __('Security check failed. Please refresh the page and try again.', 'wop-contact-form')),
				403
			);
		}

		// 2. Sanitize + validate input.
		$result = $this->validator->process($_POST);

		if ( ! empty($result['errors'])) {
			wp_send_json_error(
				array(
					'message' => __('Please correct the highlighted fields.', 'wop-contact-form'),
					'errors'  => $result['errors'],
				),
				422
			);
		}

		// 3. File upload will be wired up in the next step.
		$attachment_id = null;

		// 4. Persist to DB.
		$submission_id = $this->repository->insert($result['data'], $attachment_id);

		if (false === $submission_id) {
			wp_send_json_error(
				array('message' => __('Could not save your request. Please try again.', 'wop-contact-form')),
				500
			);
		}

		wp_send_json_success(
			array(
				'message' => __('Thank you! Your request has been received.', 'wop-contact-form'),
				'id'      => $submission_id,
			)
		);
	}
}
