# Advanced WooCommerce User Panel Plugin — File-by-File Roadmap

This guide describes how to build an advanced customer dashboard plugin for WooCommerce by working through the project file by file. Follow the numbered phases in order; every phase contains clearly scoped tasks so you can commit small, reviewable chunks.

## Phase 0 – Prerequisites & Setup
1. **Local environment**
   - WordPress 6.0+ and WooCommerce 7.0+ installed locally.
   - WP-CLI configured for quick activation/deactivation.
2. **Development tooling**
   - Enable `WP_DEBUG` and `SCRIPT_DEBUG` in `wp-config.php`.
   - Install Node.js if you plan to bundle assets (e.g., with Vite or Webpack).
3. **Repository structure**
   - Create a new directory inside `wp-content/plugins/` named `woo-advanced-user-panel`.
   - Inside it add empty `includes/`, `templates/`, and `assets/{css,js,images}` folders.

## Phase 1 – Bootstrap the Plugin
1. **`woo-advanced-user-panel.php`**
   - Add the plugin header, guard against direct access, and require the loader class.
   - Instantiate `Waup_Loader` and call a `run()` method.
   - Register activation/deactivation hooks (even if they are temporary stubs) so you can extend them later.
2. **Smoke test**
   - ✅ Activate the plugin in wp-admin and ensure the site loads without fatal errors.

## Phase 2 – Loader & Service Registration
1. **`includes/class-waup-loader.php`**
   - Create the class with `register_shortcodes()`, `enqueue_assets()`, and `register_rest_routes()` methods.
   - In the constructor, include other class files using `require_once` so future phases can add features incrementally.
   - Provide helper methods to register dashboard widgets via WordPress hooks.
2. **`includes/helpers.php`**
   - Add reusable functions (e.g., `waup_get_user_wallet_balance()`) as pure functions to keep class logic thin.
3. **Quality checks**
   - ✅ Run `php -l` on the two files.

## Phase 3 – Dashboard Rendering Flow
1. **`includes/class-waup-dashboard.php`**
   - Implement shortcode callback logic that verifies login state, gathers user context, and loads the main template via `wc_get_template()`.
   - Define filter hooks like `waup_dashboard_context` so later files can inject data.
2. **`templates/dashboard.php`**
   - Build the layout container with semantic HTML sections (header, quick stats, tab navigation, content area).
   - Include action hooks (`do_action( 'waup_dashboard_section_top' )`, etc.) for extensibility.
3. **`assets/css/dashboard.css`**
   - Start with the base grid/flex layout and typography tokens.
   - Add utility classes for status badges and buttons that later templates can reuse.
4. **`assets/js/dashboard.js`**
   - Initialize a namespace (e.g., `window.WAUP = {}`) and stub methods for tab switching and AJAX helpers.
5. **Verification**
   - ✅ Load a page with the `[waup_dashboard]` shortcode and confirm the base markup renders without styling issues.

## Phase 4 – Core Feature Modules
Work on one module at a time, committing after each file pair (class + template).

1. **Orders Module**
   - `includes/class-waup-orders.php`: expose `get_recent_orders()`, `get_order_stats()` methods.
   - `templates/partials/orders-summary.php`: render a summary card with filters.
2. **Wallet Module**
   - `includes/class-waup-wallet.php`: manage CRUD for wallet balances using user meta or a custom table; include nonce checks for top-ups.
   - `templates/partials/wallet.php`: show balance history and top-up form.
3. **Support/Tickets Module**
   - `includes/class-waup-support.php`: register `waup_ticket` custom post type and REST routes for AJAX ticket submission.
   - `templates/partials/support.php`: display ticket list and modal for new ticket.
4. **Notifications Module**
   - `includes/class-waup-notifications.php`: fetch/store notifications; provide REST endpoint returning unread counts.
   - `templates/partials/notifications.php`: list notifications with read/unread states.
5. **After each module**
   - ✅ Add unit or integration coverage where possible (e.g., PHPUnit for data helpers, Playwright for UI smoke tests).

## Phase 5 – Enhancements & Polish
1. **Internationalization**
   - Generate a `.pot` file using `wp i18n make-pot` and ensure all strings are translatable.
2. **Account Security**
   - Implement capability checks (`current_user_can()`) and sanitize every user input.
3. **Performance**
   - Lazy-load heavy data through AJAX requests triggered by tabs.
   - Cache expensive queries via transients keyed by user ID.
4. **UI/UX improvements**
   - Add skeleton loaders in `dashboard.js` for data fetches.
   - Provide theme overrides by allowing templates to be copied into `theme/woo-advanced-user-panel/`.

## Phase 6 – Release Workflow
1. Update the plugin version and changelog.
2. Run automated tests and linting (PHP_CodeSniffer, ESLint/Stylelint).
3. Tag the release in Git and create a `.zip` for distribution.
4. Document installation and configuration steps in `README.md` within the plugin folder.

---

By following this phased roadmap you can move sequentially from bootstrap to advanced features, committing a small, reviewable set of files at each step. Adjust module order based on business priorities, but keep the single-file focus so implementation remains predictable and testable.
