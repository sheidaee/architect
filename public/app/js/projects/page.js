angular.module('builder.projects')

.controller('PagesController', ['$scope', '$http', 'project', 'localStorage', function($scope, $http, project, localStorage) {
	$scope.project = project;

    $scope.loading = false;

    $scope.emptyProject = function() {
        alertify.confirm("این عمل باعث حدف اطلاعات html, css و js صفحات پروژه می شود، آیا نسبت به انجام این عمل اطمینان دارید؟", function (e) {
            if (e) {
                $scope.loading = true;

                project.clear().then(function() {
                    $scope.loading = false;
                });
            }
        });
    };

    $scope.createNewPage = function() {
        
        $scope.loading = true;

        var name = 'Page'+(project.active.pages.length+1);

        //create a new page object
        project.active.pages.push({
            name: name,
            'pageable_id': project.active.id,
            'pageable_type': 'Project',
        });

        //save new page to database
        project.save('page').then(function() {
            project.changePage(name);
            $scope.loading = false;
        });
    };

    //Delete currently active page
    $scope.deletePage = function() {

        if (project.active.pages.length < 2) {
            return alertify.log('پروژه شما باید حداقل شمال یک صفحه باشد.', 'error', 3000);
        }

        alertify.confirm("این عمل غیر قابل بازگشت است، ادامه می دهید؟", function (e) {
            if (e) {
                $scope.loading = true;

                project.removePage(project.activePage.id).then(function() {

                    if (project.active.pages.length) {
                        project.changePage();
                    } else {
                        project.activePage = false;
                    }
                    
                    $scope.loading = false;

                });
            }
        });
    };

    //Save currently active page
    $scope.savePage = function() {
        $scope.loading = true;

        //console.log(project.activePage);
        project.save('page').then(function() {
            $scope.loading = false;
            localStorage.set('activePage', project.activePage.name);
            alertify.log('صفحه "'+project.activePage.name+'" با موفقیت ذخیره شد.', 'success', 2000);
        });
    };

    //Copy currently active page
    $scope.copyPage = function() {
        $scope.loading = true;

        //prepare a page object copy
        var copy = $.extend({}, $scope.project.activePage);
        copy.name = 'page'+(project.active.pages.length+1);
        delete copy.id; delete copy.$$hashKey;

        //push to pages array
        $scope.project.active.pages.push(copy);

        //save to db
        project.save('page').then(function() {
            $scope.loading = false;
        });
    };


}])

.directive('blRenderPagePreview', ['dom', function(dom) {
    return {
        restrict: 'A',
        link: function($scope, el) {
   
            var deregister = $scope.$watch('activePanel', function(name) {
                if (name == 'pages') {
                    var iframe = $('<iframe class="scale-iframe" id="page-preview-iframe" frameborder="0"></iframe>');
                    iframe.appendTo(el);
                    iframe.attr('src', 'about:blank');

                    iframe.load(function(e) {
                        $scope.pagesDoc = iframe.get(0).contentWindow.document;

                        //load the initial page as the event has already fired probably
                        setTimeout(function() {
                            $scope.pagesDoc.open('text/html', 'replace');
                            $scope.pagesDoc.write(dom.getHtml(false, true, false));
                            $scope.pagesDoc.close();
                        }, 100);

                        //afterwards on page change event load it's htlm into preview iframe
                        $scope.$on('builder.page.changed', function(e, page) {
                            $scope.pagesDoc.open('text/html', 'replace');
                            $scope.pagesDoc.write(dom.getHtml(page, true, false));
                            $scope.pagesDoc.close();
                        });

                        $scope.$on('builder.project.cleared', function(e) {
                            $($scope.pagesDoc.body).html('');
                        });
                        iframe.unbind('load');
                    });
                   
                    deregister();
                }            
            });        
        }
    };
}])