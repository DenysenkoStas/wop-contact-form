# WOP Contact Form

Custom WordPress contact form plugin built from scratch — without relying on any
form-building plugins like Contact Form 7 or Gravity Forms.

## Features

- Frontend form rendered via the `[contact_form]` shortcode
- AJAX submission (no page reload), success/error messages shown inside the form
- Client- and server-side validation of name, phone and email
- Phone number input mask (Inputmask, format configurable in PHP)
- File upload with drag & drop and click-to-browse (jpg, png)
- Files stored via the standard WordPress media mechanism
- Custom database table for submissions, dropped on plugin deactivation
- Admin page with all submissions listed in a native WP list table

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Installation

1. Copy the `wop-contact-form` folder to `wp-content/plugins/`, **or** upload the
   ZIP archive via *Plugins → Add New → Upload Plugin*.
2. Activate the plugin in *Plugins*. The custom table
   `{prefix}wop_cf_submissions` is created on activation.
3. Place the shortcode `[contact_form]` on any page or post.
4. Submissions can be viewed in the WP admin under the *Submissions* menu
   (visible to administrators only).

> **Note.** Deactivating the plugin **drops the submissions table** — this is
> required by the test brief. Reactivate to recreate the table.

## Configuration

A few values are intentionally kept as PHP constants so they live in source
control:

| Setting                | Where                                             | Default               |
|------------------------|---------------------------------------------------|-----------------------|
| Phone mask format      | `WOP_CF_Assets::enqueue_frontend()` → `phoneMask` | `+380 (99) 999-99-99` |
| Maximum file size (MB) | `WOP_CF_File_Uploader::MAX_FILE_SIZE_MB`          | `5`                   |
| Allowed extensions     | `WOP_CF_File_Uploader::ALLOWED_EXTENSIONS`        | `jpg`, `jpeg`, `png`  |
| Required capability    | `WOP_CF_Admin_Page::CAPABILITY`                   | `manage_options`      |

## Project structure

```
wop-contact-form/
├── wop-contact-form.php          Main plugin file (header, constants, autoloader)
├── README.md
├── .gitignore
├── includes/
│   ├── class-plugin.php          Wires components together
│   ├── class-activator.php       Creates DB table on activation
│   ├── class-deactivator.php     Drops DB table on deactivation
│   ├── class-shortcode.php       Registers [contact_form] and renders the template
│   ├── class-assets.php          Enqueues CSS/JS, only on pages with the shortcode
│   ├── class-ajax-handler.php    Handles admin-ajax.php requests
│   ├── class-form-validator.php  Sanitizes + validates form input
│   ├── class-submission-repository.php  All DB access for submissions
│   ├── class-file-uploader.php   Uploads files via WP media mechanism
│   └── class-admin-page.php      Registers the admin menu and renders the list
├── templates/
│   ├── form.php                  Frontend form markup
│   └── admin-submissions.php     Submissions table markup
└── assets/
    ├── css/
    │   └── form.css              Form styles with field states and responsive layout
    └── js/
        └── form.js               Validation, phone mask, drag & drop, AJAX
```

## Architecture overview

The plugin uses a small object-oriented structure with a simple PSR-4-like
autoloader (defined in the main plugin file). Each class has a single
responsibility:

- **`WOP_CF_Plugin`** — single entry point, instantiates and wires components.
- **`WOP_CF_Activator` / `WOP_CF_Deactivator`** — handle the lifecycle hooks.
- **`WOP_CF_Shortcode`** — registers the shortcode, renders `templates/form.php`.
- **`WOP_CF_Assets`** — enqueues CSS, the Inputmask library (CDN) and our JS.
  Loads only on singular pages whose content contains the shortcode.
- **`WOP_CF_Ajax_Handler`** — receives the AJAX submission, verifies the nonce,
  delegates sanitization, file upload and persistence.
- **`WOP_CF_Form_Validator`** — sanitizes raw `$_POST` and validates the
  required fields.
- **`WOP_CF_File_Uploader`** — wraps `media_handle_upload()`. Performs three-tier
  validation (extension whitelist, `wp_check_filetype`, magic-bytes via finfo).
- **`WOP_CF_Submission_Repository`** — encapsulates all DB access; the only
  class that talks to `$wpdb`.
- **`WOP_CF_Admin_Page`** — registers the admin menu item and renders
  `templates/admin-submissions.php`.

## Database schema

Created automatically on activation, dropped on deactivation. Table name uses
the standard WP prefix (e.g. `wp_wop_cf_submissions`).

| Column            | Type                 | Notes                                                                             |
|-------------------|----------------------|-----------------------------------------------------------------------------------|
| `id`              | BIGINT UNSIGNED PK   | Auto-increment                                                                    |
| `full_name`       | VARCHAR(255)         |                                                                                   |
| `phone`           | VARCHAR(50)          | Stored with the mask formatting                                                   |
| `email`           | VARCHAR(255)         |                                                                                   |
| `city`            | VARCHAR(255)         |                                                                                   |
| `project_details` | TEXT                 |                                                                                   |
| `attachment_id`   | BIGINT UNSIGNED NULL | WP attachment ID (file URL is resolved dynamically via `wp_get_attachment_url()`) |
| `created_at`      | DATETIME             | Site timezone (`current_time('mysql')`)                                           |

## Security

- **Nonce verification** on every AJAX request (`check_ajax_referer`).
- **Sanitization** before DB writes: `sanitize_text_field`, `sanitize_email`,
  `sanitize_textarea_field`, `wp_unslash`.
- **Output escaping** everywhere data is rendered: `esc_html`, `esc_url`,
  `esc_html__`, `nl2br( esc_html(...) )` for multi-line text.
- **File-type validation** in three layers — extension, `wp_check_filetype`,
  finfo magic-bytes — to prevent renamed-script uploads.
- **Capability check** (`manage_options`) on the admin submissions page.

## Notes on the brief

A few items the brief explicitly excludes:

- Email notification — not implemented.
- Pagination of the submissions list — not implemented.
- Settings page, anti-spam, browser notifications — not implemented.

## License

GPL-2.0-or-later
