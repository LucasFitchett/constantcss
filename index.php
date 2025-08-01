<?php
/**
 * Plugin Name: ConstantCSS
 * Description: Open source utility-first CSS framework for WordPress.
 * Version: 1.0.0
 * Author: Lucas Fitchett
 * Author URI: https://lucasfitchett.com
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: constantcss
 */

add_action("wp_enqueue_scripts", function () {
    wp_enqueue_style(
        "constantcss",
        plugins_url("constantcss.css", __FILE__),
        [],
        "1.0.0",
    );
});
