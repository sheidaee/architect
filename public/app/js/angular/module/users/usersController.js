angular.module ('builder.users', [])
    .controller ('UsersController', ['$scope', '$rootScope', '$state', 'user', function ($scope, $rootScope, $state, user) {

    $scope.loading = false;


    //console.log($('meta[name="csrf-token"]').attr('content'));
    //$scope.loginInfo = {};
    //if ($scope.isDemo) {
    //	$scope.loginInfo.email = 'demo@demo.com';
    //	$scope.loginInfo.password = 'demo';
    //}

    $scope.registerInfo = {};

    $scope.errors = {};

    $scope.login = function () {
        $scope.loading = true;

        user.login ($.extend ({}, $scope.loginInfo)).error (function (data, status) {
            $scope.errors = data;
        }).success (function (user) {
            console.log(user);
            $rootScope.user = user;
            $ ('.login-container').addClass ('animated fadeOutDown');
            setTimeout (function () {
                $rootScope.user = user;
                $state.go ('dashboard');
            }, 550);

        }).finally (function (data) {
            $scope.loading = false;
        });
    };

    $scope.register = function() {
    	$scope.loading = true;
        $scope.registerInfo.isRegister = 1;
    	user.register($.extend({}, $scope.registerInfo)).error(function(errors) {
    		$scope.errors = errors;
            //console.log($scope.registerInfo);
    	}).success(function(user) {
    		$('.login-container').addClass('animated fadeOutDown');
    		setTimeout(function() {
    			$rootScope.user = user;
    			$state.go('dashboard');
    		}, 550);

    	}).finally(function(data) {
    		$scope.loading = false;
    	});
    };

}])
//// Create Objects
    .factory ('user', ['$http', '$rootScope', '$state', function ($http, $rootScope, $state) {

    var user = {

        login: function (credentials) {
            // console.log($http.post('http://localhost/architect/users/login', credentials));
            //: {
            //   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //}
            //console.log($rootScope);
            var user = $http.post ('/auth/login', credentials);
            return user;
        },

        register: function(credentials) {
        	return $http.post('/auth/register', credentials);
        },
        //
        logout: function() {
        	return $http.post('/auth/logout/').success(function(data) {console.log(data);
        		$rootScope.user = false;
        		$state.go('home');
        	});
        }

    };

    return user;
}])

    .directive ('blFormValidator', function () {
    return {
        restrict: 'A',
        link: function ($scope, el) {
            var form = $ ('form');
            // Help to lisen change scope
            $scope.$watch ('errors', function (newErrors, oldErrors) {
                // console.log(newErrors);

                //remove old errors
                $ ('.form-error').remove ();

                $.each (newErrors, function (field, message) {

                    //if there's no field name append error as alert before the first input field
                    if ( field == '*' ) {
                        $ ('#login-page .alert').show ().addClass ('animated shake').find ('.message').text (message);
                    } else {
                        var field = $ ('[name="' + field + '"]');

                        $ ('<span class="form-error help-block">' + message + '</span>').appendTo ('#login-page').css ({
                            top: field.offset ().top - 4,
                            right: field.offset ().left + field.outerWidth (),
                        }).addClass ('animated flipInX');
                    }
                });
            });
        }
    };
})

