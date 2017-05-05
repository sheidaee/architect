angular.module('builder.settings', [])

    .factory('settings', ['localStorage', function(localStorage) {

        var settings = {

            ///**
            // * All currently existing settings categories.
            // *
            // * @type {Array}
            // */
            //categories: [],
            //
            ///**
            // * Wether or not we should save setting after a change.
            // *
            // * @type {Boolean}
            // */
            //pauseSaving: false,
            //
            all: [
                {
                    name: 'enableHoverBox',
                    value: true,
                    category: 'contextBoxes',
                    description: 'Show/Hide box that appears when hovering over elements in the builder.',
                },
                {
                    name: 'enableSelectBox',
                    value: true,
                    category: 'contextBoxes',
                    description: 'Show/Hide box that appears when clicking an element in the builder.',
                },
                {
                    name: 'showWidthAndHeightHandles',
                    value: true,
                    category: 'contextBoxes',
                    description: 'Show/Hide circle handles used to change elements width and height by dragging them.',
                },
                {
                    name: 'enableAutoSave',
                    value: false,
                    category: 'autoSave',
                    description: 'Enable/Disable automatic saving when changes are made to the project or page. This may cause some delays on Firefox and Internet Explorer browsers.',
                },
                {
                    name: 'autoSaveDelay',
                    value: 2000,
                    category: 'autoSave',
                    description: 'How long (in miliseconds) to wait before auto saving after changes are made.',
                },
                {
                    name: 'openRightSidebarByDefault',
                    value: true,
                    category: 'Panels',
                    description: 'Should the right sidebar (inspector) be open or closed by default when builder is opened.',
                },
                {
                    name: 'openLeftSidebarByDefault',
                    value: true,
                    category: 'Panels',
                    description: 'Should the left sidebar (element panel) be open or closed by default when builder is opened.',
                },
                {
                    name: 'showInspectorCategoriesFilterByDefault',
                    value: false,
                    category: 'Panels',
                    description: 'Should the categories filter be shown by default at the top of the inspector (right sidebar).',
                },
                {
                    name: 'openCodeEditorByDefault',
                    value: false,
                    category: 'Panels',
                    description: 'Should the code editor at the bottom (html, css, js) be open by default.',
                },
                // {
                // 	name: 'contextBoxesColor',
                // 	value: '#179ede',
                // 	category: 'contextBoxes',
                // 	description: 'Select and hover context boxes color, will require page reload to take effect.',
                // },
            ],

            ///**
            // * Do any work needed to bootstrap the settings.
            // *
            // * @return void
            // */
            //init: function() {
            //    var values = localStorage.get('settings');
            //
            //    settings.pauseSaving = true;
            //
            //    if (values) {
            //        for (var name in values) {
            //            settings.set(name, values[name]);
            //        }
            //    }
            //
            //    settings.pauseSaving = false;
            //},
            //
            ///**
            // * Save current settings to localStorage.
            // *
            // * @return void
            // */
            //save: function() {
            //    var values = {};
            //
            //    for (var i = settings.all.length - 1; i >= 0; i--) {
            //        values[settings.all[i].name] = settings.all[i].value;
            //    };
            //    localStorage.set('settings', values);
            //},

            /**
             * Match given name to setting and return it's value.
             *
             * @param  string name
             * @return mixed
             */
            get: function(name) {
                for (var i = 0; i < settings.all.length; i++) {
                    if (settings.all[i].name == name) {
                        return settings.all[i].value;
                    }
                };
            },

            ///**
            // * Change given setting to given value.
            // *
            // * @param  string name
            // * @param  mixed  value
            // *
            // * @return void
            // */
            //set: function(name, value) {
            //    for (var i = settings.all.length - 1; i >= 0; i--) {
            //        if (settings.all[i].name == name) {
            //            settings.all[i].value = value;
            //
            //            if ( ! settings.pauseSaving) {
            //                settings.save();
            //            }
            //
            //            break;
            //        }
            //    };
            //},
            //
            ///**
            // * Return all currently existing settings categories.
            // *
            // * @return array
            // */
            //getCategories: function() {
            //    if (settings.categories.length) {
            //        return settings.categories;
            //    }
            //
            //    for (var i = settings.all.length - 1; i >= 0; i--) {
            //        var category = settings.all[i].category;
            //
            //        if (settings.categories.indexOf(category) == -1) {
            //            settings.categories.push(category);
            //        }
            //    };
            //
            //    return settings.categories;
            //},
        };

        return settings;
    }])