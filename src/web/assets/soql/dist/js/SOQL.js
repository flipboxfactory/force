/** global: Craft */
/** global: Garnish */

Craft.ForceQuery = Garnish.Base.extend(
    {
        $results: null,
        $form: null,

        init: function (form, settings) {
            this.$form = $(form);
            this.$results = this.$form.find("#query-results");

            this.setSettings(settings, Craft.ForceQuery.defaults);

            this.$form.on('submit', $.proxy(this, 'onSubmit'));

            var $spinner = this.$form.find('.spinner');
            if ($spinner.length) {
                this.$spinner = $spinner;
            } else {
                this.$spinner = $('<div class="spinner hidden"/>').insertBefore(this.$results);
            }
        },

        onSubmit: function (ev) {
            ev.preventDefault();

            this.$spinner.removeClass('hidden');

            Craft.actionRequest(
                'POST',
                this.settings.action,
                $(ev.currentTarget).serialize(),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.$spinner.addClass('hidden');
                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.afterQuery(response);

                            Craft.cp.displayNotice(
                                Craft.t('force', this.settings.messageSuccess)
                            );
                        }
                    },
                    this
                )
            );
        },
        afterQuery: function (response) {
            this.$results.html(JSON.stringify(response, null, 2));
            this.onAfterQuery(response);
        },

        onAfterQuery: function (response) {
            this.settings.onAfterQuery(response);
            this.trigger('afterQuery', {response: response});
        }
    },
    {
        defaults: {
            action: 'force/cp/queries/request',
            messageError: "Failed to execute query",
            messageSuccess: "Query executed successfully",
            onAfterQuery: $.noop
        }
    }
);

