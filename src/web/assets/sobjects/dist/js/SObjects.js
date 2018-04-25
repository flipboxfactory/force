/** global: Craft */
/** global: Garnish */

Craft.ForceSObjectsActions = Garnish.Base.extend(
    {
        $actionBtn: null,
        $actionMenu: null,
        $spinner: null,

        _initialized: false,

        init: function (settings) {
            this.setSettings(settings, Craft.ForceSObjectsActions.defaults);
            this.initActions();

            this._initialized = true;
        },

        initActions: function () {
            var safeMenuActions = [],
                destructiveMenuActions = [];

            var i;

            for (i = 0; i < this.settings.actions.length; i++) {
                var action = this.settings.actions[i];

                if (!action.destructive) {
                    safeMenuActions.push(action);
                } else {
                    destructiveMenuActions.push(action);
                }
            }

            if (safeMenuActions.length || destructiveMenuActions.length) {
                var $menu = this.getActionMenu();

                var $safeList = this._createMenuTriggerList(safeMenuActions, false),
                    $destructiveList = this._createMenuTriggerList(destructiveMenuActions, true);

                if ($safeList) {
                    $safeList.appendTo($menu);
                }

                if ($safeList && $destructiveList) {
                    $('<hr/>').appendTo($menu);
                }

                if ($destructiveList) {
                    $destructiveList.appendTo($menu);
                }
            }

            this.$actionBtn.menubtn();
            this.$actionBtn.data('menubtn').on('optionSelect', $.proxy(this, '_handleMenuActionTriggerSubmit'));
        },

        getActionMenu: function () {
            if (this.$actionMenu) {
                return this.$actionMenu;
            }

            this.$actionBtn = $('<div class="btn menubtn dashed" title="' + Craft.t('app', 'Actions') + '"/>')
                .insertAfter(this.$addBtn);

            this.$actionMenu = $('<ul class="menu"/>')
                .insertAfter(this.$actionBtn);

            return this.$actionMenu;
        },

        activateSpinner: function () {
            this.$spinner.removeClass('hidden');
        },

        deactivateSpinner: function () {
            this.$spinner.addClass('hidden');
        },

        updateBtn: function () {
            this.updateActionBtn();
        },

        updateActionBtn: function () {
            if (this.$sObjects.length === 0) {
                this.enableActionBtn();
            } else {
                this.disableActionBtn();
            }
        },

        disableActionBtn: function () {
            if (this.$actionBtn && !this.$actionBtn.hasClass('disabled')) {
                this.$actionBtn.addClass('disabled');

                if (this._initialized) {
                    this.$actionBtn.velocity('fadeOut', Craft.ForceSObjectsField.ADD_FX_DURATION);
                } else {
                    this.$actionBtn.hide();
                }
            }
        },

        enableActionBtn: function () {
            if (this.$actionBtn && this.$actionBtn.hasClass('disabled')) {
                this.$actionBtn.removeClass('disabled');

                if (this._initialized) {
                    this.$actionBtn.velocity('fadeIn', Craft.ForceSObjectsField.REMOVE_FX_DURATION);
                } else {
                    this.$actionBtn.show();
                }
            }
        },

        _createMenuTriggerList: function (actions, destructive) {
            if (actions && actions.length) {
                var $ul = $('<ul/>');

                for (var i = 0; i < actions.length; i++) {
                    var action = actions[i];

                    if (action.trigger) {
                        $(action.trigger).appendTo($ul);
                    } else {
                        var actionClass = action.type;
                        $('<li/>').append($('<a/>', {
                            id: Craft.formatInputId(actionClass) + '-actiontrigger',
                            'class': (destructive ? 'error' : null),
                            'data-action': actionClass,
                            text: actions[i].name
                        })).appendTo($ul);
                    }
                }

                return $ul;
            }
        },

        _handleMenuActionTriggerSubmit: function (ev) {
            var $option = $(ev.option);

            if ($option.hasClass('disabled') || $option.data('ignore') || $option.data('custom-handler')) {
                return;
            }

            var actionClass = $option.data('action');
            this.submitAction(actionClass);
        },

        actionData: function (action, actionClass) {
            return $.extend(this.settings.data, this.settings.actionData, action.params, {
                action: actionClass
            });
        },

        submitAction: function (actionClass) {
            var action;

            for (var i = 0; i < this.settings.actions.length; i++) {
                if (this.settings.actions[i].type === actionClass) {
                    action = this.settings.actions[i];
                    break;
                }
            }

            if (!action || (action.confirm && !confirm(action.confirm))) {
                return;
            }

            this.activateSpinner();

            Craft.actionRequest(
                'POST',
                this.settings.actionAction,
                this.actionData(action, actionClass),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.deactivateSpinner();

                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.afterAction(action, response);

                            if (response.message) {
                                Craft.cp.displayNotice(
                                    Craft.t('force', response.message)
                                );
                            }
                        }
                    },
                    this
                )
            );
        },

        afterAction: function (action, response) {
            Craft.cp.runQueue();
            this.onAfterAction(action, response);
        },

        onAfterAction: function (action, response) {
            this.settings.onAfterAction(action, response);
            this.trigger('afterAction', {action: action, response: response});
        }
    },
    {
        ADD_FX_DURATION: 200,
        REMOVE_FX_DURATION: 200,

        defaults: {
            actions: {},
            actionData: {},
            actionAction: 'force/cp/fields/perform-action',
            onAfterAction: $.noop
        }
    }
);
Craft.ForceSObjectsField = Craft.ForceSObjectsActions.extend(
    {
        sObjectSelect: null,
        sObjectSort: null,

        $container: null,
        $sObjectsContainer: null,
        $sObjects: null,
        $addBtn: null,

        $spinner: null,

        _$triggers: null,

        init: function (container, settings) {
            this.$container = $(container);
            this.$container.data('sobjects', this);

            this.setSettings(settings, Craft.ForceSObjectsField.defaults);

            // No reason for this to be sortable if we're only allowing 1 selection
            if (this.settings.limit === 1) {
                this.settings.sortable = false;
            }

            this.$sObjectsContainer = this.$container.children('.sObjects');
            ;
            this.$addBtn = this.$container.find('.btn.add');
            if (this.$addBtn) {
                this.addListener(this.$addBtn, 'activate', 'onAdd');
            }

            if (this.$addBtn && this.settings.limit === 1) {
                this.$addBtn
                    .css('position', 'absolute')
                    .css('top', 0)
                    .css(Craft.left, 0);
            }

            this.$spinner = $('<div class="spinner hidden"/>').appendTo(this.$container);

            this.initSObjectSort();
            this.initActions();
            this.resetSObjects();

            this._initialized = true;
        },

        initSObjectSort: function () {
            if (this.settings.sortable) {
                this.sObjectSort = new Garnish.DragSort({
                    container: this.$sObjectsContainer,
                    ignoreHandleSelector: '.ignore-sort',
                    axis: 'list',
                    collapseDraggees: true,
                    magnetStrength: 4,
                    helperLagBase: 1.5
                });
            }
        },

        getSObjects: function () {
            return this.$sObjectsContainer.children();
        },

        resetSObjects: function () {
            if (this.$sObjects !== null) {
                this.removeSObjects(this.$sObjects);
            } else {
                this.$sObjects = $();
            }
            this.addSObjects(this.getSObjects());
        },

        addSObjects: function ($sObjects) {
            if (this.settings.sortable) {
                this.sObjectSort.addItems($sObjects);
            }

            var that = this;
            $sObjects.each(function (index, el) {
                that.createRow(el);
            });

            this.$sObjects = this.$sObjects.add($sObjects);
            this.updateBtn();
        },

        createRow: function (container) {
            return new Craft.ForceSObjectRow(container, $.extend({}, {
                onRemove: $.proxy(function (row) {
                    this.removeSObjects(row.$container);
                    row.$container.remove();
                }, this)
            }, this.settings.rowSettings));
        },

        removeSObjects: function ($sObjects) {
            // Disable the hidden input in case the form is submitted before this element gets removed from the DOM
            $sObjects.children('input').prop('disabled', true);

            this.$sObjects = this.$sObjects.not($sObjects);
            this.updateBtn();

            this.onRemoveSObjects();
        },

        updateBtn: function () {
            this.base();
            this.updateAddBtn();
        },

        updateAddBtn: function () {
            if (this.canAddMore()) {
                this.enableAddBtn();
            } else {
                this.disableAddBtn();
            }
        },

        onAdd: function () {
            if (!this.canAddMore()) {
                return;
            }

            this.addRow();
        },

        addRow: function (id) {
            this.activateSpinner();

            Craft.actionRequest(
                'POST',
                this.settings.createRowAction,
                this.rowData({id: id}),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.deactivateSpinner();

                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            var $sobject = $(response.html);

                            Craft.appendHeadHtml(response.headHtml);
                            Craft.appendFootHtml(response.footHtml);

                            this.appendSObject($sobject);
                            this.addSObjects($sobject);

                            return $sobject;
                        }
                    },
                    this
                )
            );
        },

        appendSObject: function ($sObject) {
            $sObject.appendTo(this.$sObjectsContainer);
        },

        rowData: function (data) {
            return $.extend(data, this.settings.rowData);
        },

        canAddMore: function () {
            return (!this.settings.limit || this.$sObjects.length < this.settings.limit);
        },

        disableAddBtn: function () {
            if (this.$addBtn && !this.$addBtn.hasClass('disabled')) {
                this.$addBtn.addClass('disabled');

                if (this.settings.limit === 1) {
                    if (this._initialized) {
                        this.$addBtn.velocity('fadeOut', Craft.ForceSObjectsField.ADD_FX_DURATION);
                    } else {
                        this.$addBtn.hide();
                    }
                }
            }
        },

        enableAddBtn: function () {
            if (this.$addBtn && this.$addBtn.hasClass('disabled')) {
                this.$addBtn.removeClass('disabled');

                if (this.settings.limit === 1) {
                    if (this._initialized) {
                        this.$addBtn.velocity('fadeIn', Craft.ForceSObjectsField.REMOVE_FX_DURATION);
                    } else {
                        this.$addBtn.show();
                    }
                }
            }
        },

        onRemoveSObjects: function () {
            this.trigger('removeSObjects');
            this.settings.onRemoveSObjects();
        },

        afterAction: function (action, response) {
            this.addRow(response.id);
            this.base(action, response);
        }
    },
    {
        ADD_FX_DURATION: 200,
        REMOVE_FX_DURATION: 200,

        defaults: {
            limit: null,
            sortable: true,

            actionData: {},
            actionAction: 'force/cp/fields/perform-action',

            createRowAction: 'force/cp/fields/create-row',
            rowData: {},
            rowSettings: {},

            onRemoveSObjects: $.noop,
            onSortChange: $.noop,
            onAfterAction: $.noop
        }
    }
);

Craft.ForceSObjectRow = Craft.ForceSObjectsActions.extend(
    {
        $container: null,

        $actionContainer: null,
        $actionButton: null,
        $actionTriggers: null,

        $associateBtn: null,
        $dissociateBtn: null,
        $toggleBtn: null,

        $sObjectInput: null,
        $sObjectLabel: null,

        id: null,

        init: function (container, settings) {
            this.setSettings(settings, Craft.ForceSObjectRow.defaults);

            this.$container = $(container);
            this.$container.data('row', this);

            this.$associateBtn = this.$container.find('.associate');
            if (this.$associateBtn) {
                this.addListener(this.$associateBtn, 'activate', 'onAssociate');
            }

            this.$dissociateBtn = this.$container.find('.remove');
            if (this.$dissociateBtn) {
                this.addListener(this.$dissociateBtn, 'activate', 'onDissociate');
            }

            this.$toggleBtn = this.$container.find('.toggle-edit');
            if (this.$toggleBtn) {
                this.addListener(this.$toggleBtn, 'activate', 'onToggle');
            }

            this.$actionContainer = this.$container.find('.actions');
            this.$actionButton = this.$actionContainer.find('.menubtn');
            this.$actionTriggers = this.$actionContainer.find('.triggers');


            this.$actionBtn = this.$actionContainer.find('.menubtn');
            this.$actionMenu = this.$actionContainer.find('.triggers');

            this.$sObjectLabel = this.$container.find('.sObjectIdLabel');
            this.$sObjectInput = this.$container.find('input.sObjectId');
            this.id = this.$sObjectInput.val();

            this.$spinner = $('<div class="spinner hidden"/>').appendTo(this.$container);

            this.initActions();

            Craft.initUiElements(this.$container);
        },

        actionData: function (action, actionClass) {
            return $.extend(this.base(action, actionClass), {id: this.id});
        },

        onToggle: function (ev) {
            if (!this.id) {
                this.remove();
                return;
            }

            this.updateSObjectId(this.id);
            this.toggleEdit();
        },

        toggleEdit: function () {
            this.$container.toggleClass('edit-mode');
        },

        checkButtonVisibility: function () {
            if (this.$sObjectInput.val() !== '') {
                this.$sObjectLabel.removeClass('hidden')
            } else {
                this.$sObjectLabel.addClass('hidden')
            }
        },

        updateSObjectId: function (value) {
            this.$sObjectInput.val(value);
            this.$sObjectLabel.html(value);

            this.id = value;

            this.checkButtonVisibility();
        },

        getSortOrder: function () {
            var sortOrder = this.$container.parent('.sObjects').children().index(this.$container);
            return sortOrder + 1;
        },

        associationData: function () {
            var data = $.extend({}, this.settings.data, this.settings.associationData);

            data['sObjectId'] = this.$sObjectInput.val();
            data['sortOrder'] = this.getSortOrder();
            return data;
        },

        onAssociate: function (ev) {
            this.activateSpinner();

            Craft.actionRequest(
                'POST',
                this.settings.associateAction,
                this.associationData(),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.deactivateSpinner();

                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.toggleEdit();

                            if (response.hasOwnProperty("sObjectId")) {
                                this.updateSObjectId(response.sObjectId);
                            }

                            if (this.settings.associationMessageSuccess) {
                                Craft.cp.displayNotice(
                                    Craft.t('force', this.settings.associationMessageSuccess)
                                );
                            }
                        }
                    },
                    this
                )
            );
        },

        dissociationData: function () {
            var data = $.extend({}, this.settings.data, this.settings.associationData);
            data['sObjectId'] = this.id;
            return data;
        },

        onDissociate: function (ev) {
            if (!this.id) {
                this.remove();
                return;
            }

            this.activateSpinner();

            Craft.actionRequest(
                'POST',
                this.settings.dissociateAction,
                this.dissociationData(),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.deactivateSpinner();

                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.remove();

                            if (this.settings.dissociationMessageSuccess) {
                                Craft.cp.displayNotice(
                                    Craft.t('force', this.settings.dissociationMessageSuccess)
                                );
                            }
                        }
                    },
                    this
                )
            );
        },

        remove: function () {
            this.destroy();

            this.trigger('remove', {row: this});
            this.settings.onRemove(this);
        },


    },
    {
        defaults: {
            actionAction: 'force/cp/fields/perform-row-action',

            data: {},
            associationData: {},
            associateAction: 'force/cp/sobjects/associate',

            dissociateData: {},
            dissociateAction: 'force/cp/sobjects/dissociate',

            associationMessageError: "Failed to associate Salesforce Object.",
            associationMessageSuccess: "Successfully associated Salesforce Object.",

            dissociationMessageError: "Failed to dissociate Salesforce Object.",
            dissociationMessageSuccess: "Successfully dissociated Salesforce Object.",

            onRemove: $.noop
        }
    }
);