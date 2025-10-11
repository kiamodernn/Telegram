<?php
/**
 * Default dashboard template.
 *
 * @var WP_User $user Current user instance.
 * @var array   $atts Shortcode attributes.
 * @var string  $content Shortcode content.
 */
?>
<div class="waup-dashboard" data-user-id="<?php echo esc_attr( $user->ID ); ?>">
    <header class="waup-dashboard__header">
        <h1 class="waup-dashboard__title"><?php echo esc_html( sprintf( __( 'Welcome back, %s', 'woo-advanced-user-panel' ), $user->display_name ) ); ?></h1>
        <?php do_action( 'waup_dashboard_header_after', $user, $atts ); ?>
    </header>

    <section class="waup-dashboard__stats">
        <?php do_action( 'waup_dashboard_stats', $user, $atts ); ?>
    </section>

    <nav class="waup-dashboard__navigation" aria-label="<?php esc_attr_e( 'Dashboard navigation', 'woo-advanced-user-panel' ); ?>">
        <ul class="waup-tabs" role="tablist">
            <?php do_action( 'waup_dashboard_tabs', $user, $atts ); ?>
        </ul>
    </nav>

    <section class="waup-dashboard__content" id="waup-dashboard-content" role="region" aria-live="polite">
        <?php
        /**
         * Fires inside the default dashboard content container.
         */
        do_action( 'waup_dashboard_content', $user, $atts, $content );
        ?>
    </section>
</div>
