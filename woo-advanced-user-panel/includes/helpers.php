<?php
/**
 * Misc helper functions used across the plugin.
 *
 * @package Woo_Advanced_User_Panel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'waup_render_template' ) ) {
    /**
     * Safely loads a template file and exposes the context variables.
     *
     * @param string $template Relative template path.
     * @param array  $context  Variables passed into the template.
     */
    function waup_render_template( $template, array $context = [] ) {
        $path = waup_locate_template( $template );

        if ( ! $path ) {
            /* translators: %s: template path */
            echo esc_html( sprintf( __( 'Template %s not found.', 'woo-advanced-user-panel' ), $template ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            return;
        }

        extract( $context, EXTR_SKIP );
        include $path;
    }
}

if ( ! function_exists( 'waup_locate_template' ) ) {
    /**
     * Locates a template file, allowing theme overrides.
     *
     * @param string $template Relative template path.
     *
     * @return string|false
     */
    function waup_locate_template( $template ) {
        $template = ltrim( $template, '/' );
        $theme_path = trailingslashit( get_stylesheet_directory() ) . 'woo-advanced-user-panel/' . $template;
        $parent_theme_path = trailingslashit( get_template_directory() ) . 'woo-advanced-user-panel/' . $template;
        $plugin_path = WAUP_PLUGIN_PATH . 'templates/' . $template;

        if ( file_exists( $theme_path ) ) {
            return $theme_path;
        }

        if ( file_exists( $parent_theme_path ) ) {
            return $parent_theme_path;
        }

        if ( file_exists( $plugin_path ) ) {
            return $plugin_path;
        }

        return false;
    }
}

if ( ! function_exists( 'waup_format_currency' ) ) {
    /**
     * Formats a monetary amount using WooCommerce helpers when available.
     *
     * @param float|int|string $amount Amount to format.
     *
     * @return string
     */
    function waup_format_currency( $amount ) {
        if ( function_exists( 'wc_price' ) ) {
            return wc_price( $amount );
        }

        $amount = floatval( $amount );
        return esc_html( number_format_i18n( $amount, 2 ) );
    }
}
