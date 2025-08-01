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

require_once "vendor/autoload.php";
use Mexitek\PHPColors\Color;

if (!defined("ABSPATH")) {
    exit();
}

class ConstantCSS
{
    public function __construct()
    {
        add_action("admin_menu", [$this, "ccss_settings_page"]);
        add_action("admin_init", [$this, "ccss_settings_init"]);
        add_action("update_option_ccss_color_primary", [
            $this,
            "ccss_generate_shades",
        ]);
        add_action("wp_enqueue_scripts", [$this, "enqueue_ccss_files"], 999);
    }

    public function enqueue_ccss_files(): void
    {
        wp_enqueue_style("ccss-base", plugins_url("ccss-base.css", __FILE__));
        wp_enqueue_style(
            "ccss-colors",
            plugins_url("ccss-colors.css", __FILE__),
        );
    }

    public function ccss_settings_page(): void
    {
        add_menu_page(
            "ConstantCSS Settings",
            "ConstantCSS",
            "manage_options",
            "ccss",
            [$this, "ccss_settings_page_html"],
        );
    }

    public function ccss_settings_init(): void
    {
        register_setting("ccss", "ccss_color_primary");

        add_settings_section("ccss_section_colors", "Colors", "", "ccss");

        add_settings_field(
            "ccss_color_primary",
            "Primary",
            [$this, "ccss_field_colors_primary_callback"],
            "ccss",
            "ccss_section_colors",
        );
    }

    public function ccss_settings_page_html(): void
    {
        ?>
        <div class="wrap">
            <h1>ConstantCSS Settings</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields("ccss");
                do_settings_sections("ccss");
                submit_button();?>
            </form>
        </div>
        <?php
    }

    public function ccss_field_colors_primary_callback(): void
    {
        ?>
            <input type="color" name="ccss_color_primary" value="<?= esc_attr(
                get_option("ccss_color_primary", "#ff0000"),
            ) ?>" />
    <?php
    }

    public function ccss_generate_shades(): void
    {
        $user_primary_color = get_option("ccss_color_primary");
        $primary_color = new Color($user_primary_color);

        $new_colors = [
            "primary" => $primary_color->getHex(),
            "primary-light" => $primary_color->lighten(10),
            "primary-ultralight" => $primary_color->lighten(20),
            "primary-dark" => $primary_color->darken(10),
            "primary-ultradark" => $primary_color->darken(20),
            "primary-hover" => $primary_color->lighten(10),
        ];

        $css_content = "@layer constantcss {\n\t:root {\n";

        foreach ($new_colors as $name => $color) {
            $css_content .= "\t\t--ccss-color-$name: #$color;\n";
        }

        $css_content .= "\t}\n}";

        $css_file_path = plugin_dir_path(__FILE__) . "ccss-colors.css";

        if (file_put_contents($css_file_path, $css_content) === false) {
            wp_die("Failed to write CSS file");
        }
    }
}

new ConstantCSS();
