angular.module('builder.styling')

    .controller('ThemesController', ['$scope', 'themes', 'dom', function($scope, themes, dom) {
        $scope.themes = themes;
        //$scope.export = {};

        themes.init();
        ////variables to filter themes list on
        //$scope.filter = {
        //    type: '',
        //    search: '',
        //};

        ////set given theme as active one
        //$scope.activateTheme = function(theme) {
        //    themes.loadTheme(theme);
        //};

        //check if user id on the theme matches currently logged in users one
        $scope.canEdit = function(theme) {
            //return true;
            $scope.user && theme.userId == $scope.user.id;
        };
    }])
    .factory('themes', ['$rootScope', '$http', 'localStorage', function($rootScope, $http, localStorage) {

        var themes = {

            /**
             * A list of all available themes.
             *
             * @type Array
             */
            all: [],

            /**
             * Currently active theme.
             *
             * @type Object
             */
            active: {},

            /**
             * Themes that is currently being edited
             *
             * @type Object
             */
            editing: false,

            /**
             * Load given theme as active one.
             *
             * @param  mixed name
             * @return void
             */
            loadTheme: function(name, noEvent) {
                if ( ! name) { return };

                var loader = $('#theme-loading').removeClass('hidden');

                if (angular.isString(name)) {
                    for (var i = this.all.length - 1; i >= 0; i--) {
                        if (this.all[i].name.toLowerCase() == name.toLowerCase()) {
                            this.active = this.all[i];
                        }
                    };
                } else {
                    this.active = name;
                }
                //console.log(this.active[0]);
                if (this.active.name) {
                    //Add new theme stylesheet link into the DOM by either replacing
                    //the original theme (base bootstrap) or just appending this one
                    if (this.active.replaceOriginal) {
                        $rootScope.frameHead.find('#main-sheet').prop('disabled', true).remove();
                        var link = $('<link id="main-sheet" rel="stylesheet" href="'+this.active.path+'">').prependTo($rootScope.frameHead);
                    } else {
                        $rootScope.frameHead.find('#theme-sheet').prop('disabled', true).remove();
                        var link = $('<link id="theme-sheet" rel="stylesheet" href="'+this.active.path+'">').prependTo($rootScope.frameHead);
                    }

                    //safari doesn't fire load event on link yet so we need to use some magic
                    var img = document.createElement('img');
                    img.onerror = function(){
                        loader.addClass('hidden');
                        $rootScope.$broadcast('builder.theme.changed', themes.active);
                    }
                    img.src = this.active.path;
                    //$rootScope.selectBox.hide();
                    //$rootScope.hoverBox.hide();
                }
            },

            //delete: function(theme) {
            //    $http.delete('http://localhost/architect/pr-themes/'+theme.id).success(function(data) {
            //        for (var i = themes.all.length - 1; i >= 0; i--) {
            //            if (themes.all[i].id == theme.id) {
            //                themes.all.splice(i, 1);
            //            }
            //        };
            //    });
            //},
            //
            //edit: function(theme) {
            //    themes.editing = theme;
            //},
            //
            /**
            * Return a theme by name.
            *
            * @param  string name
            * @return Object
            */
            get: function(name) {
                for (var i = 0; i < this.all.length; i++) {
                    if (this.all[i].name == name) {
                        return this.all[i];
                    }
                };
            },

            init: function() {
                $http.get('/pr-themes/').success(function(data) {
                    for (var i = data.length - 1; i >= 0; i--) {
                        themes.all.push({
                            name: data[i].name,
                            image: data[i].thumbnail,
                            description: data[i].description,
                            path: $rootScope.baseUrl+'/'+data[i].path,
                            replaceOriginal: true,
                            source: data[i]['source'] || 'Architect',
                            type: data[i].type,
                            userId: data[i].user_id,
                            id: data[i].id,
                            customLess: data[i]['custom_less'],
                            vars: data[i]['modified_vars'],
                        });
                    };
                });
                //console.log(themes);
                //console.log('hello');
            },
        };

        return themes;
    }])

//Compile and insert themes panel html into the dom on element click.
    .directive('blRenderThemes', ['$compile', 'dom', function($compile, dom) {
        return {
            restrict: 'A',
            link: function($scope, el) {

                var deregister = $scope.$watch('activePanel', function(name) {
                    if (name == 'themes') {

                        var img = "theme.image ? theme.image : 'themes/'+theme.name+'/image.png'";

                        var html = $compile(
                            '<div class="col-xs-12 col-lg-6" ng-repeat="theme in filteredThemes = (themes.all | filter:filter.type | filter:filter.search)" ng-click="activateTheme(theme)">'+
                            '<figure id="{{ theme.name }}-theme" ng-class="{ active: themes.active.name == theme.name }">'+
                            '<img ng-src="{{ '+img+' }}" class="img-responsive" alt="{{ theme.name }}">'+
                            '<i ng-if="canEdit(theme)" ng-click="themes.edit(theme);$event.stopPropagation()" class="fa fa-gears edit-theme" data-toggle="tooltip" data-placement="top" title="Edit Theme"></i>'+
                            '<i ng-if="canEdit(theme)" ng-click="themes.delete(theme);$event.stopPropagation()" class="fa fa-trash-o delete-theme" data-toggle="tooltip" data-placement="top" title="Delete Theme"></i>'+
                            '<figcaption class="clearfix">'+
                            '<span class="name pull-left">{{ theme.name.ucFirst() }}</span>'+
                            '<span class="source pull-right">{{ theme.source }}</span>'+
                            '</figcaption>'+
                            '</figure>'+
                            '</div>'+
                            '<h2 ng-if="filteredThemes.length === 0">No results found.</h2>'
                        )($scope);

                        el.find('#themes-list').append(html);

                        var iframe = $('<iframe class="scale-iframe" id="theme-preview-iframe"></iframe>');
                        iframe.appendTo(el.find('#themes-preview'));
                        iframe.attr('src', 'about:blank');

                        iframe.load(function(e) {
                            $scope.doc = iframe[0].contentWindow.document;

                            //reload preview iframe html when active theme is changed
                            $scope.$on('builder.theme.changed', function() {
                                if ($scope.activePanel == 'themes') {
                                    $scope.doc.open('text/html', 'replace');
                                    $scope.doc.write(dom.getHtml(false, true, false));
                                    $scope.doc.close();
                                }
                            });

                            //load current builder html into preview iframe on themes panel open
                            setTimeout(function() {
                                $scope.doc.open('text/html', 'replace');
                                $scope.doc.write(dom.getHtml(false, true, false));
                                $scope.doc.close();
                            }, 100);

                            $scope.$watch('activePanel', function(name) {
                                if (name == 'themes') {
                                    $scope.doc.open('text/html', 'replace');
                                    $scope.doc.write(dom.getHtml(false, true, false));
                                    $scope.doc.close();
                                }
                            });

                            iframe.unbind('load');
                        });

                        deregister();
                    }
                });
            }
        };
    }])