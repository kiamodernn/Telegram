(function ($) {
    'use strict';

    const state = {
        activeTab: null,
    };

    const Dashboard = {
        init() {
            this.cacheDom();
            this.bindEvents();
        },

        cacheDom() {
            this.$container = $('.waup-dashboard');
            this.$tabs = this.$container.find('[data-waup-tab]');
            this.$content = $('#waup-dashboard-content');
        },

        bindEvents() {
            this.$container.on('click', '[data-waup-tab]', (event) => {
                event.preventDefault();
                const $target = $(event.currentTarget);
                this.switchTab($target.data('waup-tab'));
            });
        },

        switchTab(tab) {
            if (!tab || state.activeTab === tab) {
                return;
            }

            state.activeTab = tab;
            this.$tabs.attr('aria-selected', 'false');
            this.$tabs.filter(`[data-waup-tab="${tab}"]`).attr('aria-selected', 'true');

            this.fetchTab(tab);
        },

        fetchTab(tab) {
            const request = {
                url: `${waupDashboard.restUrl}/tabs/${tab}`,
                method: 'GET',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', waupDashboard.nonce);
                    this.showLoading();
                },
            };

            $.ajax(request)
                .done((response) => {
                    this.$content.html(response.html || '');
                })
                .fail(() => {
                    this.$content.html('<p class="waup-notice">Unable to load tab.</p>');
                })
                .always(() => {
                    this.hideLoading();
                });
        },

        showLoading() {
            this.$container.addClass('waup-is-loading');
        },

        hideLoading() {
            this.$container.removeClass('waup-is-loading');
        },
    };

    $(document).ready(() => Dashboard.init());
})(jQuery);
