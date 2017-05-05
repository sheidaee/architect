angular.module('builder.directives', [])
//, 'elements', 'dom'
    .directive('blBuilder', ['$rootScope', 'elements', 'dom', function($rootScope, elements, dom) {
        return {
            restrict: 'A',
            link: function($scope, el) {

                $scope.$on('builder.dom.loaded', function(e) {
                    $scope.frameHead.append('<base href="'+$scope.baseUrl+'/">');
                    $scope.frameHead.append('<link id="main-sheet" rel="stylesheet" href="lib/bootstrap/dist/css/bootstrap.min.css">');
                    $scope.frameHead.append('<link rel="stylesheet" href="css/iframe.css">');
                    $scope.frameHead.append('<link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">');
                    $rootScope.customCss = $('<style id="editor-css"></style>').appendTo($scope.frameHead);

                    //$($scope.frameDoc).on('scroll', function(e) {
                    //    $scope.hoverBox.hide();
                    //});
                });

                ////init bootstrap tooltips
                //$('[data-toggle="tooltip"]').tooltip({
                //    container: 'body',
                //});
                //
                //listen for mousemove on iframe overlay
                $scope.frameOverlay.off('mousemove').on('mousemove', function(e) {

                    //see if we're dragging and element over iframe and if it actually
                    //moved since the last mouse move event
                    if ($scope.dragging && $scope.oldX != e.pageX && $scope.oldY != e.pageY) {

                        //account for the navbar/breadcrumbs/sidebar widths/heights when determining
                        //element position in the iframe
                        var coords = {x: e.pageX - $scope.frameOffset.left, y: e.pageY - $scope.frameOffset.top};

                        if ($scope.isIE) {
                            $scope.frameOverlay.hide();
                            var el = $scope.elementFromPoint(coords.x, coords.y);
                            $scope.frameOverlay.show();
                        } else {
                            var el = $scope.elementFromPoint(coords.x, coords.y);
                        }

                        //append dragged element to the one users cursor is hovering over
                        dom.appendSelectedTo(el, coords);

                        if ($scope.isWebkit) {
                            $scope.repositionBox('hover', el, elements.match(el));
                        }
                    }

                    $scope.oldX = e.pageX;
                    $scope.oldY = e.pageY;
                });

                //prevent all scrolling on main document
                $(document).on('scroll', function(e) {
                    $(document).scrollLeft(0);
                    $(document).scrollTop(0);
                });
            }
        };
    }])

    .directive('blPrettyScrollbar', function() {
        return {
            restrict: 'A',
            compile: function(el, attrs) {
                el.mCustomScrollbar({
                    theme: 'light-thin',
                    scrollInertia: 300,
                    autoExpandScrollbar: false,
                    autoHideScrollbar: true,
                });
            }
        }
    })

    .directive('blPanelsCollapsable', function() {
        return {
            restrict: 'A',
            link: function($scope, el, attrs) {
                el.find('.bl-panel-heading').on('click', function(e) {
                    var panel = $(e.target).next('.panel-box, .list-unstyled'),
                        icon  = $(e.target).find('.fa');

                    if (panel.hasClass('hidden')) {
                        panel.removeClass('hidden');
                        icon.removeClass('fa-plus').addClass('fa-minus');
                    } else {
                        panel.addClass('hidden');
                        icon.removeClass('fa-minus').addClass('fa-plus');
                    }
                });
            }
        }
    })

    .directive('blToggleSidebar', ['settings', function(settings) {
        return {
            restrict: 'A',
            link: function($scope, el, attrs) {
                var viewport = $('#viewport'),
                    parent   = el.parent(),
                    side     = el.hasClass('left') ? 'left' : 'right';

                // open or close panels be default depending on user settings
                if (side == 'right') {
                    if ( ! settings.get('openRightSidebarByDefault')) {
                        viewport.addClass('right-collapsed');
                        parent.css('right', '-234px');
                    }
                } else if (side == 'left') {
                    if ( ! settings.get('openLeftSidebarByDefault')) {
                        viewport.addClass('left-collapsed');
                        parent.css('left', '-234px');
                    }
                }

                el.on('click', function() {
                    if (viewport.hasClass(side+'-collapsed')) {
                        viewport.removeClass(side+'-collapsed');
                        parent.css(side, '0');
                    } else {
                        viewport.addClass(side+'-collapsed');
                        parent.css(side, '-234px');
                    }

                    setTimeout(function() {
                        $scope.repositionBox('select');
                    }, 250);

                });
            }
        }
    }])

    .directive('blPrettySelect', ['$parse', '$rootScope', function($parse, $rootScope) {

        //extend jquery ui widget so we can use different
        //styles for every select option
        $.widget('builder.prettyselect', $.ui.selectmenu, {
            _renderItem: function( ul, item ) {
                var li = $('<li>', {text: item.label});

                //grab any styles stored on options and apply them
                $.each(item.element.data(), function(i,v) {
                    li.css(i, v);
                })

                return li.appendTo(ul);
            },
        });

        return {
            restrict: 'A',
            link: function($scope, el, attrs) {

                //innitiate select plugin on element
                el.prettyselect({
                    width: attrs.width ? attrs.width : 100,
                    appendTo: $rootScope.inspectorCont,
                });

                //hide select menu on inspector scroll
                $scope.inspectorCont.on('scroll', function() {
                    el.prettyselect('close');
                });

                //get object reference to bind select value to
                var model = $parse(attrs.blPrettySelect);

                //assign new value to object on the scope we got above
                el.on('prettyselectchange', function(e, ui) {
                    $scope.$apply(function() {
                        model.assign($scope, ui.item.value);
                    });
                });

                //set up two way binding between select and model we got above
                $scope.$watch(attrs.blPrettySelect, function(elVal) {
                    if ( ! elVal) { return true; };

                    for (var i = el.get(0).options.length - 1; i >= 0; i--) {
                        var selVal = el.get(0).options[i].value.removeQoutes();

                        if (selVal == elVal || selVal.match(new RegExp('^.*?'+elVal+'.*?$'))) {
                            return el.val(selVal).prettyselect('refresh');
                        }
                    };
                });
            }
        }
    }])

    .directive('blColorPicker', ['$parse', '$rootScope', 'inspector', 'undoManager', function($parse, $rootScope, inspector, undoManager) {

        return {
            restrict: 'A',
            link: function($scope, el) {

                //create an input element to instantiate color picker on
                var input = $('<input id="color-picker"></input>').prependTo(el);

                //initiate color picker with some predefined colors to choose from
                input.spectrum({
                    flat: true,
                    palette: colorsForPicker,
                    showAlpha: true,
                    showPalette: true,
                    showInput: true,
                });

                //cache values needed for color picker repositioning
                var container  = $('#inspector .sp-container'),
                    triggers   = $('.color-picker-trigger'),
                    contWidth  = container.width(),
                    contHeight = container.height(),
                    arrow      = $('#color-picker-arrow'),
                    bottomEdge = $('#code-editor-wrapper').get(0).getBoundingClientRect().top - 40;

                $rootScope.colorPickerCont = container.add(arrow).addClass('hidden');

                container.find('.sp-choose').on('click', function(e) {
                    $rootScope.colorPickerCont.addClass('hidden');
                    return false;
                })

                triggers.on('click', function(e) {
                    var top   = $(e.target).offset().top + (e.currentTarget.offsetHeight/2 - 15),
                        model = e.currentTarget.dataset.controls,
                        scope = angular.element(e.currentTarget).scope();

                    if ( ! $scope.colorProperty || $scope.colorProperty == model) {
                        $rootScope.colorPickerCont.toggleClass('hidden');
                    }

                    //hide image/gradient panel
                    $('#background-flyout-panel').addClass('hidden');
                    $('#background-arrow').hide();

                    input.off('move.spectrum').on('move.spectrum', function(e, color) {

                        var rgb = color.toRgb();

                        //if transparency is 0 or 1 convert to hex
                        //format otherwise convert to rgba format
                        if (rgb.a === 0 || rgb.a === 1) {
                            color = color.toHexString();
                        } else {
                            color = color.toRgbString();
                        }

                        //write current color on inspector so we have real time
                        //reflection on element in the DOM
                        scope.$apply(function() {
                            $parse(model).assign(scope, color);
                        });
                    });

                    var tempTop = top - contHeight/2;

                    //position picker 15px above bottom edge if not enough space normally
                    if (bottomEdge < (tempTop + contHeight)) {
                        tempTop = bottomEdge - contHeight - 15;
                    }

                    //pisition picker 15px below top edge if not enough space normally
                    if (tempTop < 0) {
                        tempTop = 15;
                    }

                    container.css('top', tempTop);

                    arrow.css({
                        display: 'block',
                        left: $scope.windowWidth - $scope.inspectorWidth - 17,
                        top:  top,
                    })

                    input.spectrum('set', $parse(model)(scope));

                    //set trigger as previous and grab color propperty (background, border-color etc)
                    //from targets data attribute so we know what we should apply colors to
                    $scope.colorProperty = e.currentTarget.dataset.controls;
                    inspector.styles.color.property = $scope.colorProperty;
                });

                //create the command to undo color change on dragstart
                input.on("dragstart.spectrum", function(e, color) {
                    inspector.sliding = true;
                    $scope.$broadcast($scope.colorProperty+'.slidestart', 'color');
                });

                //add the command to undo manager on dragstop
                input.on("dragstop.spectrum", function(e, color) {
                    inspector.sliding = false;
                    $scope.$broadcast($scope.colorProperty+'.slidestop', 'color');
                });
            },
        }

    }])

    .directive('blPrettyScrollbar', function() {
        return {
            restrict: 'A',
            compile: function(el, attrs) {
                el.mCustomScrollbar({
                    theme: 'light-thin',
                    scrollInertia: 300,
                    autoExpandScrollbar: false,
                    autoHideScrollbar: true,
                });
            }
        }
    })

    .directive('blRangeSlider', ['$rootScope', '$parse', 'inspector', function($rootScope, $parse, inspector) {

        return {
            restrict: 'A',
            link: function($scope, el, attrs) {
                var model = $parse(attrs.blRangeSlider);

                //initiate slider
                el.slider({
                    min: 0,
                    step: 1,
                    max: attrs.max ? attrs.max : 100,
                    range: "min",
                    animate: true,
                    slide: function(e, ui) {
                        if (attrs.blRangeSlider.indexOf('props') > -1) {
                            $scope.$apply(function() { model.assign($scope, ui.value); });
                        } else {
                            inspector.applySliderValue(attrs.blRangeSlider, ui.value, 'px');
                        }

                    }
                });

                //reset slider when user selects a different DOM element or different
                //style directions (top, bot, left, right)
                $scope.$on('element.reselected', function() { el.slider('value', 0) });
                $scope.$on(attrs.blRangeSlider+'.directions.changed', function() { el.slider('value', 0) });

                el.on("slidestart", function(event, ui) {
                    inspector.sliding = true;
                    $scope.$broadcast(attrs.blRangeSlider.replace(/[A-Z][a-z]+/g, '')+'.slidestart', attrs.blRangeSlider);

                    //hide select and hover box while user is dragging
                    //as their positions will get messed up
                    $scope.selectBox.add($scope.hoverBox).hide();
                });

                el.on("slidestop", function(event, ui) {
                    $scope.$broadcast(attrs.blRangeSlider.replace(/[A-Z][a-z]+/g, '')+'.slidestop', attrs.blRangeSlider);
                    $scope.repositionBox('select');
                    inspector.sliding = false;
                    $rootScope.$broadcast('builder.css.changed');
                });
            },
        }
    }])

    .directive('blCheckboxes', ['$compile', function($compile) {
        return {
            restrict: 'A',
            link: function($scope, el, attrs) {
                var p = attrs.blCheckboxes,
                    d = ['top', 'right', 'bottom', 'left'];

                var html = '<div class="pretty-checkbox pull-left">'+
                    '<input type="checkbox" id="'+p+'.all" ng-click="inspector.toggleStyleDirections(\''+p+'\', \'all\')">'+
                    '<label for="'+p+'.all"><span class="ch-all"></span><span class="unch-all"></span></label>'+
                    '</div>'+
                    '<div class="pull-right">';

                for (var i = 0; i < 4; i++) {
                    html+= '<div class="pretty-checkbox">'+
                    '<input type="checkbox" id="'+p+'.'+d[i]+'" ng-click="inspector.toggleStyleDirections(\''+p+'\', \''+d[i]+'\')" ng-checked="inspector.checkboxes.'+p+'.indexOf(\''+d[i]+'\') !== -1">'+
                    '<label for="'+p+'.'+d[i]+'"><span class="ch-'+d[i]+'"></span><span class="unch-'+d[i]+'"></span></label>'+
                    '</div>'
                };

                html+='</div>';

                el.html($compile(html)($scope));
            }
        }
    }])

    .directive('blInputBoxes', ['$compile', '$timeout', function($compile, $timeout) {
        return {
            restrict: 'A',
            link: function($scope, el, attrs) {
                var p = attrs.blInputBoxes;

                var html = '<div class="big-box col-sm-6">'+
                    '<input ng-model="'+p+'All" ng-model-options="{ debounce: 300 }" ng-change="inspector.applyBigInputBoxValue(\''+p+'\', '+p+'All)">'+
                    '</div>'+
                    '<div class="small-boxes col-sm-6">'+
                    '<div class="row">'+
                    '<input ng-model="inspector.styles.'+p+'.top" ng-model-options="{ debounce: 300 }" ng-change="inspector.applyInputBoxValue(\''+p+'\', inspector.styles.'+p+'.top, \'top\')">'+
                    '</div>'+
                    '<div class="row">'+
                    '<div class="col-sm-6">'+
                    '<input ng-model="inspector.styles.'+p+'.left" ng-model-options="{ debounce: 300 }" ng-change="inspector.applyInputBoxValue(\''+p+'\', inspector.styles.'+p+'.left, \'left\')">'+
                    '</div>'+
                    '<div class="col-sm-6">'+
                    '<input ng-model="inspector.styles.'+p+'.right" ng-model-options="{ debounce: 300 }" ng-change="inspector.applyInputBoxValue(\''+p+'\', inspector.styles.'+p+'.right, \'right\')">'+
                    '</div>'+
                    '</div>'+
                    '<div class="row">'+
                    '<input ng-model="inspector.styles.'+p+'.bottom" ng-model-options="{ debounce: 300 }" ng-change="inspector.applyInputBoxValue(\''+p+'\', inspector.styles.'+p+'.bottom, \'bottom\')">'+
                    '</div>'+
                    '</div>';

                el.html($compile(html)($scope));

                $scope.$on('element.reselected', function(e) {
                    $timeout(function() {
                        el.find('input').blur();
                    }, 0, false);
                });
            }
        }
    }])

    .directive('blCloseFlyoutPanel', ['$rootScope', function($rootScope) {
        return {
            restrict: 'A',
            template: '<div title="Close Panel" class="fa-stack fa-lg flyout-close">'+
            '<i class="fa fa-circle fa-stack-2x"></i>'+
            '<i class="fa fa-times fa-stack-1x fa-inverse"></i>'+
            '</div>',
            replace: true,
            compile: function(el) {
                el.on('click', function(e) {
                    $rootScope.$apply(function() {
                        $rootScope.flyoutOpen = false;
                    });
                });
            }
        };
    }])


