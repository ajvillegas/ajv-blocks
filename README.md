# AJV Block Library

A collection of common blocks and core block enhancements for use in client websites.

**Contributors**: [ajvillegas](http://profiles.wordpress.org/ajvillegas)  
**Tags**: [blocks](http://wordpress.org/plugins/tags/blocks), [block library](http://wordpress.org/plugins/tags/block-library), [block editor](http://wordpress.org/plugins/tags/block-editor), [gutenberg](http://wordpress.org/plugins/tags/gutenberg),  
**Requires at least**: 5.8  
**Tested up to**: 5.8  
**Requires PHP**: 5.6  
**Stable tag**: 1.0.0  
**License**: [GPLv2 or later](http://www.gnu.org/licenses/gpl-2.0.html)

## Description

This plugin adds a collection of common custom WordPress blocks and core block enhancements for use in client websites. It was built with small web agencies in mind to use as a foundation for creating their own unique block library plugins.

The blocks are registered conditionally so you have full control of which blocks to use on your site. Additionally the block assets are only loaded in the front-end of the site when the blocks are being used. This prevents unecessary assets from slowing down your site by reducing the amount of requests the browser makes on each page load.

### Registered Blocks

Here is a list of all the custom blocks registered by this plugin:

- **Accordion Block**
  - Adds an accordion section.
- **Grid Block**
  - Displays inner blocks in a grid pattern.
- **Section Intro**
  - Adds a section intro with heading and paragraph.

### Core Block Enhancements

The plugin adds extra settings and controls to the following core WordPress blocks:

- **Columns Block**
  - Column gutter settings
  - Responsive column settings
- **Cover and Group Blocks**
  - Inner content wrapper width settings
- **Spacer Block**
  - Responsive height settings

These settings allow users to easily create responsive layouts and full-with sections from within the block editor using the core WordPress blocks they are already familiar with.

### Plugin Settings

The plugin settings page allows users to enable or disable any of the custom blocks or the core block enhancements added by the plugin. You can find this page under **Settings** > **AJV Blocks**.

**Warning:** Disabling a block after it has been inserted on a page will return an error and prevent you from editing the block in the admin editor.

## Filters for Developers

The follwing filters are avilable for theme developers to easily overwrite the editor and front-end styles of each block.

- `ajvbl_{block-name}_style_path`
- `ajvbl_{block-name}_editor_style_url`

The `{block-name}` is the name of the block used in the `registerBlockType` method without the plugin namespace.

When using the `ajvbl_{block-name}_style_path` filter you can take full advantage of the way the plugin loads styles conditionally only when a block is rendered on the page.

Following are examples of how to use both filters in your theme.

**`ajvbl_{block-name}_style_path`:**

```php
<?php

add_filter( 'ajvbl_section-intro_style_path', 'myprefix_section_intro_style_path' );
/**
 * Filter the Section Intro block style path.
 *
 * @since  1.0.0
 * @param  string $path The file path for the block styles.
 * @return string
 */
function myprefix_section_intro_style_path( $path ) {

  $path = get_stylesheet_directory() . '/assets/css/section-intro.css';

  return $path;

}
```

**`ajvbl_{block-name}_editor_style_url`:**

```php
<?php

add_filter( 'ajvbl_section-intro_editor_style_url', 'myprefix_section_intro_editor_style_url' );
/**
 * Filter the Section Intro block editor style URL.
 *
 * @since  1.0.0
 * @param  string $url The file URL for the block editor styles.
 * @return string
 */
function myprefix_section_intro_editor_style_url( $url ) {

  $path = get_stylesheet_directory_uri() . '/assets/css/section-intro-editor.css';

  return $path;

}
```

**Notice how one filter requires a file path while the other requires the file URL**. Depending on which one you use, you might have to use either the `get_stylesheet_directory()` or the `get_stylesheet_directory_uri()` functions.

## Development

This plugin was created to serve as a framework or skeleton for building your own block library plugin. With the basic foundation built, you can concentrate on adding new blocks to develop your own unique plugin.

To add new blocks you must adhere to the following file system pattern:

```txt
my-block/
├─ src/
│  ├─ block.js     -> Main block JS file
│  ├─ editor.css   -> Editor styles
│  ├─ frontend.js  -> Front-end script
│  └─ style.css    -> Front-end styles
├─ block.min.js
├─ editor.min.css
├─ frontend.min.js
└─ style.min.css
```

The plugin looks for these specific file names in these specific folders when registering blocks and block assets. Only the main `block.js` and `block.min.js` files are required.

Blocks are added under the **library** > **blocks** folder and files must be named the same as in the example above.

The `scr` folder contains the development files and are automatically loaded when `SCRIPT_DEBUG` is set to `true`. The `.min` files in the main block folder are the production files.

The block folder name must be the same one used in the `registerBlockType` method without the plugin namespace. So if you register a block named `ajvbl/my-block` then the folder name must be `my-block`.

**Note:** You must use the same namespace for all blocks registered in the plugin – i.e., `ajvbl` for this plugin.

### No Build Step

All the blocks included in this plugin are written in plain JavaScript with ES5 and ES6+ syntax for simplicity and ease of use. The main benefit of this approach is that they do not require a build step or tools for transpiling the code.

Although the code is [supported by all modern browsers](https://caniuse.com/?search=es6), it does not support IE11 or older browsers. If you are concerned about compatibility, or want to support these older browsers you will need to transpile the ES6+ code to ES5 before adding a new block.

## Installation

### Using The WordPress Dashboard

1. Navigate to the 'Add New' Plugin Dashboard
2. Click on 'Upload Plugin' and select `ajv-blocks.zip` from your computer
3. Click on 'Install Now'
4. Activate the plugin on the WordPress Plugins Dashboard

### Using FTP

1. Extract `ajv-blocks.zip` to your computer
2. Upload the `ajv-blocks` directory to your `wp-content/plugins` directory
3. Activate the plugin on the WordPress Plugins Dashboard

## Screenshots

*Plugin Settings Page*
![Admin table view](wp-assets/screenshot-1.jpg?raw=true)

*Core Block Enhancements: Columns, Spacer, and Cover / Group Blocks*
![Testimonial editor page](wp-assets/screenshot-2.jpg?raw=true)

## Changelog

### 1.0.0

- Initial release.
