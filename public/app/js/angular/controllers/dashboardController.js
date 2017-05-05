angular.module('builder').controller('DashboardController', ['$scope', '$http', 'project', function($scope, $http, project) {
    $scope.projects = project;

    $scope.deleteProject = function(pr) {
        alertify.confirm('این عمل غیر قابل بازگشت است، ادامه می دهید؟' , function (e) {
            if (e) {
                project.delete(pr);
            }
        });
    };

    $scope.filters = {
        query: '',
        status: '',
        sort: 'newest',
        setSortProp: function() {

            //newest first
            if (this.sort == 'newest') {
                this.order = 'created_at';
                this.reverse = true;
            }

            //oldest first
            else if (this.sort == 'oldest') {
                this.order = 'created_at';
                this.reverse = false;
            }

            //A-Z
            else if (this.sort == 'a-z') {
                this.order = 'name';
                this.reverse = false;
            }

            //Z-A
            else if (this.sort == 'z-a') {
                this.order = 'name';
                this.reverse = true;
            }
        },
        order: 'created_at',
        reverse: true,
    };

    project.getAll();
}])

    .directive('blOpenInBuilder', ['$state', function($state) {
        return {
            restrict: 'A',
            link: function($scope, el) {

                el.on('click', function(e) {
                    var figure = el.closest('figure');
                    figure.find('.spinner').removeClass('hidden');
                    $state.go('builder', {name: figure.data('name')});
                });
            }
        }
    }])