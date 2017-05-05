angular.module('builder.projects')

    .controller('ExportController', ['$scope', 'project', function($scope, project) {

        $scope.project = project;

        //images to export (urls)
        $scope.images = [];

        //page that is currently being exported/previewed
        $scope.activePage = {
            name: 'index',
            ref:  {},
        };

        //change page for which to show code previews
        $scope.changeExportPage = function(name) {
            var changeTo = name ? name : $scope.activePage.name;

            for (var i = project.active.pages.length - 1; i >= 0; i--) {
                if (project.active.pages[i].name == changeTo) {
                    $scope.activePage.name = changeTo;
                    $scope.activePage.ref = project.active.pages[i];
                    break;
                }
            };

            $scope.cssPreview.setValue($scope.activePage.ref.css, -1);
            $scope.htmlPreview.setValue($scope.activePage.ref.html, -1);

            //console.log($scope.htmlPreview.getValue());
            $scope.images = $scope.getImages();
        };

        //get urls of all the local images used in
        //the page that is currently being exported
        $scope.getImages = function() {
            var local = [$scope.baseUrl, 'http://project.ir'];

            //get local image urls from image tag src in html

            var urls = $($scope.activePage.ref.html).find('img').map(function(i, node) {
                if (node.tagName == 'IMG') {
                    for (var i = local.length - 1; i >= 0; i--) {
                        if (node.src.indexOf(local[i]) > -1 || node.src.indexOf('//') === -1) {
                            return node.src;
                        }
                    };
                }
            }).get();

            //get local image urls from inline styles in html
            var pattern = new RegExp('url\\((.+?)\\)', 'g');

            while (matches = pattern.exec($scope.activePage.ref.html)) {
                if (matches[1]) { urls.push(matches[1]); };
            }

            //get local image urls from css styles
            //TODO: something wrong with regex should work with one backslash only
            var pattern = new RegExp('url\\((.+?)\\)', 'g');

            while (matches = pattern.exec($scope.activePage.ref.css)) {
                if (matches[1]) { urls.push(matches[1]); };
            }

            //filter out duplicates
            return urls.filter(function(v, i) {
                return urls.indexOf(v) == i;
            });
        };
    }])


    .directive('blRenderExportPanel', function() {
        return {
            restrict: 'A',
            link: function($scope, el) {
                var deregister = $scope.$watch('activePanel', function(name) {
                    if (name == 'export') {

                        //on builder page change set export active page as well
                        $scope.$on('builder.page.changed', function(e, page) {
                            $scope.changeExportPage(page.name);
                        });

                        //innitiate zeroClipboard plugin
                        ZeroClipboard.config({ swfPath: 'lib/ZeroClipboard.swf' });
                        var htmlClient = new ZeroClipboard($('#copy-html'));

                        htmlClient.on('ready', function(e) {
                            htmlClient.on('copy', function (event) {
                                event.clipboardData.setData('text/plain', $scope.htmlPreview.getValue());
                            });

                            htmlClient.on('aftercopy', function(event) {
                                var c = $('<span class="copy-confirmation rtl">کپی شد!</span>').insertAfter(event.target);

                                setTimeout(function() {
                                    c.remove();
                                }, 350);
                            });
                        });

                        var cssClient = new ZeroClipboard($('#copy-css'));

                        cssClient.on('ready', function(e) {
                            cssClient.on('copy', function (event) {
                                event.clipboardData.setData('text/plain', $scope.cssPreview.getValue());
                            });

                            cssClient.on('aftercopy', function(event) {
                                var c = $('<span class="copy-confirmation rtl">کپی شد!</span>').insertAfter(event.target);

                                setTimeout(function() {
                                    c.remove();
                                }, 350);
                            });
                        });

                        //initiate previews
                        $scope.cssPreview = ace.edit('css-export-preview');
                        $scope.cssPreview.setTheme('ace/theme/chrome');
                        $scope.cssPreview.setReadOnly(true);
                        $scope.cssPreview.setShowPrintMargin(false);
                        $scope.cssPreview.setDisplayIndentGuides(false);
                        $scope.cssPreview.getSession().setMode('ace/mode/css');
                        $scope.cssPreview.getSession().setUseWorker(false);

                        $scope.htmlPreview = ace.edit('html-export-preview');
                        $scope.htmlPreview.setTheme('ace/theme/chrome');
                        $scope.htmlPreview.setReadOnly(true);
                        $scope.htmlPreview.setShowPrintMargin(false);
                        $scope.htmlPreview.setDisplayIndentGuides(false);
                        $scope.htmlPreview.getSession().setMode('ace/mode/html');

                        deregister();
                    }
                });
            }
        };
    })

    .directive('blExportPage', function() {
        return {
            restrict: 'A',
            link: function($scope, el) {
                el.on('click', function(e) {
                    if ( ! $scope.downloadIframe) {
                        $scope.downloadIframe = $('<iframe class="hidden"></iframe>').appendTo(el);
                    }

                    //set iframe src to the server side download link
                    $scope.downloadIframe.attr('src', $scope.baseUrl+'/export/page/'+$scope.activePage.ref.id);
                });
            }
        };
    })

    .directive('blExportProject', function() {
        return {
            restrict: 'A',
            link: function($scope, el) {
                el.on('click', function(e) {
                    if ( ! $scope.downloadIframe) {
                        $scope.downloadIframe = $('<iframe class="hidden"></iframe>').appendTo(el);
                    }

                    //set iframe src to the server side download link
                    $scope.downloadIframe.attr('src', $scope.baseUrl+'/export/project/'+$scope.project.active.id);
                });
            }
        };
    })

    .directive('blExportImages', function() {
        return {
            restrict: 'A',
            link: function($scope, el) {

                //remove url() css wrapper and site root url and encode what's left including periods
                var formatUrl = function(string) {
                    return string.replace('url(', '').replace(')', '').replace($scope.baseUrl, '').replace('.', '%2E').replace(/\//g, '%3B');
                    //
                }

                el.on('click', 'li', function(e) {
                    if ( ! $scope.downloadIframe) {
                        $scope.downloadIframe = $('<iframe class="hidden"></iframe>').insertBefore(el);
                    }

                    //set iframe src to the server side download link
                    console.log($scope.baseUrl+'/export/image/'+formatUrl($(e.currentTarget).css('background-image')));
                    $scope.downloadIframe.attr('src', $scope.baseUrl+'/export/image/'+formatUrl($(e.currentTarget).css('background-image')));
                });
            }
        };
    })