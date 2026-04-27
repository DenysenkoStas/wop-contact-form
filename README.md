# WOP Contact Form

Custom WordPress contact form plugin built from scratch — without relying on any form-building plugins like Contact Form
7 or Gravity Forms.

## Features

- Frontend form rendered via `[contact_form]` shortcode
- AJAX submission (no page reload)
- Client- and server-side validation
- Phone number input mask
- File upload with drag & drop (jpg, png, webp)
- Files stored via standard WordPress media mechanism
- Custom database table for submissions
- Admin page with submissions table

## Installation

1. Upload the plugin folder to `wp-content/plugins/`
2. Activate the plugin in WordPress admin (this creates the custom database table)
3. Add `[contact_form]` shortcode to any page or post

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Uninstall / Deactivate

On deactivation the custom database table is dropped.

## License

GPL-2.0-or-later
