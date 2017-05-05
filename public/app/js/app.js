var builder = {};
angular.module('builder', [ 'ngCookies',
    'ui.router', 'builder.users', 'builder.styling', 'builder.projects', 'builder.directives',
    'builder.settings', 'builder.editors', 'builder.elements', 'dragAndDrop', 'builder.wysiwyg', 'undoManager',
    'builder.inspector', 'angularFileUpload',

])
    .config(
        function($stateProvider, $urlRouterProvider) {

        $urlRouterProvider.otherwise("/");
            $stateProvider
                .state('home', {
                    url: "/",
                    templateUrl: "app/view/login.php"
                })
                .state('register', {
                    url: '/register',
                    templateUrl: 'app/view/register.php'
                })
                .state('dashboard', {
                    url: '/dashboard',
                    templateUrl: 'app/view/dashboard.php',
                    controller: 'DashboardController'
                })
                .state('new', {
                    url: '/new',
                    templateUrl: 'app/view/newProject.php',
                    controller: 'NewProjectController'
                })
                .state('builder', {
                    url: '/builder/{name}',
                    templateUrl: 'app/view/builder.php',
                    controller: 'BuilderController'
                });

    })
    .run(['$rootScope', '$state', '$cookies', '$http', function($rootScope, $state, $cookies, $http) {
        $rootScope.isWebkit       = navigator.userAgent.indexOf('AppleWebKit') > -1;
        $rootScope.isIE           = navigator.userAgent.indexOf('MSIE ') > -1 || navigator.userAgent.indexOf('Trident/') > -1;
        $rootScope.baseUrl        = 'http://project.ir';
        $rootScope.$on('$stateChangeStart', function(e, toState, toParams, fromState, fromParams) {

            //console.log($cookieStore.get('prUser'));
            if ( ! $rootScope.user) {
                $rootScope.user = $cookies.get('prUser');
            }

            if (toState.name == 'dashboard' || toState.name == 'builder') {

                if ( ! $rootScope.user) {
                    e.preventDefault();
                    $state.go('home');
                }
            } else if (toState.name == 'home') {
                //if($cookieStore.get('prUser'))
                //    alert('ok');
                //console.log($cookies.get('prUser'));
                if ($rootScope.user) {
                    e.preventDefault();
                    $state.go('dashboard');
                }
            } else if (toState.name !== 'register' && toState.name !== 'new') {
                e.preventDefault();
            }
        })
    }])
    //, 'elements', 'keybinds', 'settings'
    .factory('bootstrapper', ['$rootScope', '$state', 'project', 'elements', function($rootScope, $state, project, elements) {

        var strapper = {

            loaded: false,

            eventsAttached: false,

            start: function() {
                this.initDom();
                this.initProps();
                this.initSidebars();
                //this.initSettings();

                //if ( ! this.eventsAttached) {
                    $rootScope.$on('builder.dom.loaded', function(e) {
                        strapper.initProject();
                        //strapper.initKeybinds();
                        strapper.eventsAttached = true;
                    });
                //}

                this.loaded = true;
            },

            initDom: function() {
                $rootScope.frame = $('#iframe');
                $rootScope.frame[0].src = 'about:blank';

                $rootScope.frame.load(function() {
                    $rootScope.frameWindow    = $rootScope.frame.get(0).contentWindow;
                    $rootScope.frameDoc       = $rootScope.frameWindow.document;
                    $rootScope.frameBody      = $($rootScope.frameDoc).find('body');
                    $rootScope.frameHead      = $($rootScope.frameDoc).find('head');
                    $rootScope.$broadcast('builder.dom.loaded');
                });

                $rootScope.frameOverlay   = $('#frame-overlay');
                $rootScope.hoverBox       = $('#hover-box');
                $rootScope.selectBox      = $('#select-box');
                $rootScope.selectBoxTag   = $rootScope.selectBox.find('.element-tag')[0];
                $rootScope.hoverBoxTag    = $rootScope.hoverBox.find('.element-tag')[0];
                $rootScope.selectBoxActions = document.getElementById('select-box-actions');
                $rootScope.hoverBoxActions = document.getElementById('hover-box-actions');
                $rootScope.textToolbar    = $('#text-toolbar');
                $rootScope.windowWidth    = $(window).width();
                $rootScope.inspectorCont  = $('#inspector');
                $rootScope.contextMenu    = $('#context-menu');
                $rootScope.linker         = $('#linker');
                $rootScope.inspectorWidth = $rootScope.inspectorCont.width();
                $rootScope.elemsContWidth = $("#elements-container").width();
                $rootScope.mainHead       = $('head');
                $rootScope.body           = $('body');
                $rootScope.viewport       = $('#viewport');
                $rootScope.navbar         = $('nav');
                $rootScope.contextMenuOpen= false;
                $rootScope.activePanel    = 'export';
                $rootScope.flyoutOpen     = false;

                //set the iframe offset so we can calculate nodes positions
                //during drag and drop or sorting correctly
                $rootScope.frameOffset = {top: 89, left: 234};
                $(document).ready(function() {
                    setTimeout(function() {
                        $rootScope.frameOffset = $rootScope.frame.offset();
                        $rootScope.frameWrapperHeight = $('#frame-wrapper').height();
                    }, 1000);
                });
            },

            initProject: function() {
                if ($state.params.name) {
                    project.load($state.params.name);
                } else{
                    $state.go('dashboard');
                }
            },

            initSidebars: function() {
                elements.init();
            },

            initProps: function() {
                //information about currently user selected DOM node
                $rootScope.selected  = {

                    //return selected elements html, prioratize preview html
                    html: function(type) {
                        if ( ! type || type == 'preview') {
                            return this.element.previewHtml || this.element.html;
                        } else {
                            return this.element.html;
                        }
                    },

                    getStyle: function(prop) {
                        if (this.node) {
                            return window.getComputedStyle(this.node, null).getPropertyValue(prop);
                        }
                    },
                };

                //information about node user is currently hovering over
                $rootScope.hover = {};

                //whether or not we're currently in progress of selecting
                //a new active DOM node
                $rootScope.selecting = false;
            },
        };

        return strapper;
    }])
    //, 'elements', 'settings', 'grid', 'preview', 'themes'
    .controller('BuilderController', ['$scope', '$rootScope', 'bootstrapper', 'elements', 'settings', 'grid', 'preview', 'themes', function($scope, $rootScope, bootstrapper, elements, settings, grid, preview, themes) {
        //$scope.themes = themes;

        $scope.bootstrapper = bootstrapper;
        bootstrapper.start();

        $scope.closePreview = function() {
            preview.hide();
        };

        /**
        * Whether or not passed in attribute is editable
        * on the currently active DOM node.
        *
        * @param  string prop
        * @return boolean
        */
        $scope.canEdit = function(prop) {

            if ( ! $scope.selected.node) {
                return true;
            } else {
                return $scope.selected.element && $scope.selected.element.canModify.indexOf(prop) !== -1;
            }
        };

        $rootScope.repositionBox = function(name, node, el) {

            //hide context boxes depending on user settings
            if (! settings.get('enable'+name.ucFirst()+'Box')) {
                return $scope[name+'Box'].hide();
            }

            if (! node) {
                node = $scope.selected.node;
            }

            if (node && node.nodeName == 'BODY') {
                return $scope[name+'Box'].hide();
            }

            if (! el) {
                el = $scope.selected.element;
            }

            if (! el) return true;

            if (name == 'select') {
                $scope.hoverBox.hide();
            }

            var rect = node.getBoundingClientRect();

            if ( ! rect.width || ! rect.height) {
                $scope[name+'Box'].hide();
            } else {
                $scope[name+'Box'].css({
                    top: rect.top + 39,
                    left: rect.left - 7 + $scope.frameOffset.left - $scope.elemsContWidth,
                    height: rect.height,
                    width: rect.width,
                }).show();

                $scope[name+'BoxTag'].textContent = el.name;

                //make sure boxes don't go over the breadcrumbs
                if (rect.top + 39 < 55) {
                    $scope[name+'BoxActions'].style.top = 0;
                } else {
                    $scope[name+'BoxActions'].style.top = '-27px';
                }
            }
        }

        $rootScope.elementFromPoint = function(x, y) {
            var el = $scope.frameDoc.elementFromPoint(x, y);

            //firefox returns html if body is empty,
            //IE doesn't work at all sometimes.
            if ( ! el || el.nodeName === 'HTML') {
                return $scope.frameBody[0];
            }

            return el;
        }

        /**
         * Set given node as active one in the builder.
         * Wrap calls to this method in $apply to avoid sync problems.
         *
         * @param  DOM node
         * @return void
         */
        $rootScope.selectNode = function(node) {
            if ($scope.rowEditorOpen) { return true; };

            $scope.selecting = true;

            $scope.selected.previous = $scope.selected.node;

            //if we get passed an integer instead of a dom node we'll
            //select a node at that index in the currently stored path
            if (angular.isNumber(node)) {
                node = $scope.selected.path[node].node;
            }

            //if we haven't already stored a reference to passed in node, do it now
            if (node && $scope.selected.node !== node) {
                $scope.selected.node = node;
            }

            //cache some more references about the node for later use
            $scope.selected.element = elements.match($scope.selected.node, 'select', true);
            $scope.selected.parent = $scope.selected.node.parentNode;
            $scope.selected.parentContents = $scope.selected.parent.childNodes;

            //position select box on top of the newly selected node
            $scope.repositionBox('select');

            //whether or node the new node is locked
            $scope.selected.locked = $scope.selected.node.className.indexOf('locked') > -1;
            $scope.selected.isImage = $scope.selected.node.nodeName == 'IMG' &&
            $scope.selected.node.className.indexOf('preview-node') === -1;

            //create an array from all parents of this node
            var parents = $($scope.selected.node).parentsUntil('body').toArray();
            parents.unshift($scope.selected.node);

            //create a path to use for breadcrumbs and css selectors
            $scope.selected.path = $.map(parents.reverse(), function(node) {
                return { node: node, name: elements.match(node).name };
            });

            //whether or not this node is a column
            $scope.selected.isColumn = grid.isColumn($scope.selected.node);

            $scope.frameWindow.focus();

            $rootScope.$broadcast('element.reselected', $scope.selected.node);

            setTimeout(function(){
                $scope.selecting = false;
            }, 200);
        };

    }])

    .factory('preview', ['$rootScope', 'dom', 'project', 'elements', function($rootScope, dom, project, elements) {
        var preview = {

            iframe: $rootScope.previewFrame,

            /**
              * Show preview iframe.
              *
              * @return void
              */
            show: function() {
                 //save project first so code in editors and dom is synced with db.
                 project.save('all').finally(function() {
                     var self = preview;

                     if ( ! self.iframe) {
                         self.iframe  = $('<iframe id="preview-frame"></iframe>').insertAfter('#viewport');
                         self.doc     = self.iframe[0].contentWindow.document;
                         self.builder = $('#viewport').add('nav');
                         self.closer  = $('#preview-closer');
                     }

                     self.load(dom.getHtml(project.activePage, true, true));

                     //add base tag so we can load assets with relative paths
                     $(self.doc).find('head').prepend('<base href="'+$rootScope.baseUrl+'">');

                     self.handlePreviews();
                     self.handleLinks();

                     self.iframe.removeClass('hidden');
                     self.builder.addClass('hidden');
                     self.closer.removeClass('hidden');
                 });
            },

            /**
              * Hide preview iframe.
              *
              * @return void
              */
            hide: function() {
                 this.iframe.addClass('hidden');
                 this.closer.addClass('hidden');
                 this.builder.removeClass('hidden');
            },

            /**
              * Load given html into preview iframe.
              *
              * @param  string html
              * @return void
              */
            load: function(html) {
                 this.doc.open('text/html', 'replace');
                 this.doc.write(html);
                 this.doc.close();
            },

            /**
              * Replace preview images with actual element html (like iframes)
              *
              * @type void
              */
            handlePreviews: function() {
                 $(this.doc).find('.preview-node').each(function(i, node) {
                     var el = elements.getElement(node.dataset.name);

                     if (el) {
                         var newNode = $(el.html).replaceAll(node);

                         if (node.dataset.src) {
                             newNode.find('iframe').attr('src', node.dataset.src);
                         }
                     }
                 });
            },

            /**
              * Handle user clicks on links in preview iframe.
              *
              * @return void
              */
            handleLinks: function() {
                 $(this.doc).find('a').off('click').on('click', function(e) {
                     e.preventDefault();

                     var href = e.currentTarget.getAttribute('href');

                     //if it's a url to hash just bail
                     if (href.indexOf('#') > -1)
                     {
                         return;
                     }

                     //if it's an absolute url confirm and continue
                     else if (href.indexOf('//') > -1)
                     {
                         alertify.confirm('This might navigate you away from the builder, are you sure you want to continue?', function(confirmed) {
                             if (confirmed) {
                                 window.location = href;
                             }
                         });
                     }

                     //otherwise it's a builder page and we'll need to load that
                     //pages contents into preview iframe on link click
                     else {
                         var pageName = href.replace('.html', '').toLowerCase();

                         for (var i = 0; i < project.active.pages.length; i++) {
                             var page = project.active.pages[i];

                             if (page.name.toLowerCase() == pageName) {
                                 preview.load(dom.getHtml(page, true, true));
                                 preview.handlePreviews();
                                 preview.handleLinks();
                             }
                         };
                     }
                 });
            },

        };

        return preview;
    }]);

function changeInterpolateProvider($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
}

