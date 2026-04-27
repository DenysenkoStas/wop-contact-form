<?php
/**
 * Frontend contact form template.
 *
 * @package WOP_CF
 */

if ( ! defined('ABSPATH')) {
	exit;
}
?>
<div class="wop-cf-wrapper">
  <form class="wop-cf-form" novalidate>
    <h2 class="wop-cf-title"><?php esc_html_e('Request a Free Quote', 'wop-contact-form'); ?></h2>

    <div class="wop-cf-field">
      <label for="wop-cf-full-name"><?php esc_html_e('Full Name', 'wop-contact-form'); ?></label>
      <input type="text" id="wop-cf-full-name" name="full_name" required>
      <span class="wop-cf-error" data-error-for="full_name"></span>
    </div>

    <div class="wop-cf-field">
      <label for="wop-cf-phone"><?php esc_html_e('Phone Number', 'wop-contact-form'); ?></label>
      <input type="tel" id="wop-cf-phone" name="phone" required>
      <span class="wop-cf-error" data-error-for="phone"></span>
    </div>

    <div class="wop-cf-field">
      <label for="wop-cf-email"><?php esc_html_e('Email Address', 'wop-contact-form'); ?></label>
      <input type="email" id="wop-cf-email" name="email" required>
      <span class="wop-cf-error" data-error-for="email"></span>
    </div>

    <div class="wop-cf-field">
      <label for="wop-cf-city"><?php esc_html_e('City', 'wop-contact-form'); ?></label>
      <input type="text" id="wop-cf-city" name="city">
      <span class="wop-cf-error" data-error-for="city"></span>
    </div>

    <div class="wop-cf-field">
      <label for="wop-cf-project-details"><?php esc_html_e('Project Details', 'wop-contact-form'); ?></label>
      <textarea id="wop-cf-project-details" name="project_details" rows="4"></textarea>
      <span class="wop-cf-error" data-error-for="project_details"></span>
    </div>

    <div class="wop-cf-dropzone" data-dropzone>
      <input type="file" id="wop-cf-file" name="photo" accept="image/jpeg,image/png,image/webp" hidden>
      <label for="wop-cf-file" class="wop-cf-dropzone-label">
        <svg class="wop-cf-dropzone-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="17 8 12 3 7 8"></polyline>
          <line x1="12" y1="3" x2="12" y2="15"></line>
        </svg>
        <span class="wop-cf-dropzone-title"><?php esc_html_e('Upload Photos (Optional)', 'wop-contact-form'); ?></span>
        <span
          class="wop-cf-dropzone-hint"><?php esc_html_e('Drag & drop or click to browse', 'wop-contact-form'); ?></span>
      </label>
      <div class="wop-cf-file-preview" data-file-preview hidden></div>
    </div>

    <button type="submit" class="wop-cf-submit"><?php esc_html_e('Get My Quote', 'wop-contact-form'); ?></button>

    <p class="wop-cf-secure">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
      </svg>
      <span><?php esc_html_e('Safe, Secure & Confidential', 'wop-contact-form'); ?></span>
    </p>

    <div class="wop-cf-message" data-message hidden></div>
  </form>
</div>
