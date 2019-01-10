/**
 * Widget Sync
 */
Craft.ForceObjectSyncWidget = Garnish.Base.extend(
    {
        $container: null,
        $submit: null,
        $spinner: null,

        init: function (container, settings) {
            this.$container = $(container);

            this.setSettings(settings, Craft.ForceObjectSyncWidget.defaults);

            this.$submit = this.$container.find('.submit');
            this.$spinner = $('<div class="spinner hidden"/>').insertAfter(this.$submit);

            this.$submit.on('click', $.proxy(this, 'onSubmit'));
        },
        assembleData: function () {
            return $.extend({id: this.$container.find('input[name="id"]').val()}, this.settings.data);
        },
        onSubmit: function (ev) {
            this.$spinner.removeClass('hidden');

            Craft.actionRequest(
                'POST',
                this.settings.action,
                this.assembleData(),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.$spinner.addClass('hidden');
                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.afterSync(response);

                            if (this.settings.messageSuccess) {
                                Craft.cp.displayNotice(
                                    Craft.t('salesforce', this.settings.messageSuccess)
                                );
                            }
                        } else {
                            if (this.settings.messageError) {
                                Craft.cp.displayError(
                                    Craft.t('salesforce', this.settings.messageError)
                                );
                            }
                        }
                    },
                    this
                )
            );
        },
        afterSync: function (response) {
            Craft.cp.runQueue();
            this.$container.find('input[type="text"]:visible').val('');
            this.onAfterSync(response);
        },

        onAfterSync: function (response) {
            this.settings.onAfterSync(response);
            this.trigger('afterSync', {response: response});
        }
    },
    {
        defaults: {
            action: 'salesforce/cp/widgets/sync-from',
            data: {},
            messageError: "Failed to sync Salesforce Object.",
            messageSuccess: "Success synced Salesforce Object.",
            onAfterSync: $.noop
        }
    }
);