<?php
/**
 * Core loader responsible for registering services and WordPress integrations.
 *
 * @package Woo_Advanced_User_Panel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Waup_Loader' ) ) {
    /**
     * Bootstrapper that wires the plugin into WordPress.
     */
    class Waup_Loader {

        /**
         * Bootstraps the plugin by binding WordPress hooks.
         */
        public function run() {
            add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
            add_action( 'init', [ $this, 'register_shortcodes' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
            add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        }

        /**
         * Loads the plugin translation files.
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'woo-advanced-user-panel', false, dirname( plugin_basename( WAUP_PLUGIN_FILE ) ) . '/languages' );
        }

        /**
         * Registers public shortcodes.
         */
        public function register_shortcodes() {
            add_shortcode( 'waup_dashboard', [ $this, 'render_dashboard_shortcode' ] );
        }

        /**
         * Enqueues front-end assets when the shortcode is present.
         */
        public function enqueue_assets() {
            if ( ! is_user_logged_in() ) {
                return;
            }

            if ( ! $this->should_enqueue_assets() ) {
                return;
            }

            wp_register_style(
                'waup-dashboard',
                WAUP_PLUGIN_URL . 'assets/css/dashboard.css',
                [],
                WAUP_PLUGIN_VERSION
            );

            wp_register_script(
                'waup-dashboard',
                WAUP_PLUGIN_URL . 'assets/js/dashboard.js',
                [ 'jquery' ],
                WAUP_PLUGIN_VERSION,
                true
            );

            wp_enqueue_style( 'waup-dashboard' );
            wp_enqueue_script( 'waup-dashboard' );

            wp_localize_script(
                'waup-dashboard',
                'waupDashboard',
                [
                    'restUrl' => esc_url_raw( rest_url( 'waup/v1' ) ),
                    'nonce'   => wp_create_nonce( 'wp_rest' ),
                ]
            );
        }

        /**
         * Registers REST routes for dashboard interactions.
         */
        public function register_rest_routes() {
            register_rest_route(
                'waup/v1',
                '/ping',
                [
                    'methods'             => 'GET',
                    'callback'            => static function () {
                        return rest_ensure_response( [ 'status' => 'ok' ] );
                    },
                    'permission_callback' => static function () {
                        return current_user_can( 'read' );
                    },
                ]
            );

            register_rest_route(
                'waup/v1',
                '/tabs/(?P<slug>[a-z0-9\-_]+)',
                [
                    'methods'             => 'GET',
                    'callback'            => [ $this, 'handle_tab_request' ],
                    'permission_callback' => static function () {
                        return current_user_can( 'read' );
                    },
                    'args'                => [
                        'slug' => [
                            'sanitize_callback' => 'sanitize_key',
                        ],
                    ],
                ]
            );
        }

        /**
         * Handles REST requests for dashboard tabs.
         *
         * @param WP_REST_Request $request REST request instance.
         *
         * @return WP_REST_Response
         */
        public function handle_tab_request( WP_REST_Request $request ) {
            $slug = $request->get_param( 'slug' );

            $response = [
                'slug' => $slug,
                'html' => apply_filters( 'waup_render_tab_' . $slug, '', $request ),
            ];

            return rest_ensure_response( $response );
        }

        /**
         * Renders the dashboard shortcode output.
         *
         * @param array       $atts Shortcode attributes.
         * @param string|null $content Content between shortcode tags.
         * @param string      $tag Current shortcode tag.
         *
         * @return string
         */
        public function render_dashboard_shortcode( $atts, $content = null, $tag = 'waup_dashboard' ) {
            if ( ! is_user_logged_in() ) {
                return $this->render_guest_message();
            }

            $context = [
                'user'    => wp_get_current_user(),
                'atts'    => shortcode_atts( [], $atts, $tag ),
                'content' => $content,
            ];

            $context = apply_filters( 'waup_dashboard_context', $context );

            ob_start();
            waup_render_template( 'dashboard.php', $context );
            return (string) ob_get_clean();
        }

        /**
         * Returns a login prompt for non-authenticated visitors.
         *
         * @return string
         */
        protected function render_guest_message() {
            if ( function_exists( 'wc_get_page_permalink' ) ) {
                $account_url = wc_get_page_permalink( 'myaccount' );
            } else {
                $account_url = wp_login_url();
            }

            return sprintf(
                '<div class="waup-notice waup-notice--login-required">%s</div>',
                wp_kses_post(
                    sprintf(
                        /* translators: %s: account page URL */
                        __( 'Please <a href="%s">log in</a> to view your dashboard.', 'woo-advanced-user-panel' ),
                        esc_url( $account_url )
                    )
                )
            );
        }

        /**
         * Determines if dashboard assets should be enqueued.
         *
         * @return bool
         */
        protected function should_enqueue_assets() {
            global $post;

            if ( ! isset( $post ) ) {
                return false;
            }

            if ( has_shortcode( $post->post_content, 'waup_dashboard' ) ) {
                return true;
            }

            /**
             * Filter: allow third parties to conditionally load assets.
             */
            return (bool) apply_filters( 'waup_should_enqueue_assets', false );
        }

        /**
         * Fired on plugin activation.
         */
        public static function activate() {
            flush_rewrite_rules();
        }

        /**
         * Fired on plugin deactivation.
         */
        public static function deactivate() {
            flush_rewrite_rules();
        }
    }
}

require_once WAUP_PLUGIN_PATH . 'includes/helpers.php';
