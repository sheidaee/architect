//, 'preview'
angular.module('builder').controller('NavbarController', ['$scope', '$rootScope', '$timeout', '$state', '$cookieStore', 'undoManager', 'settings', 'project', 'user', 'preview', function($scope, $rootScope, $timeout, $state, $cookieStore, undoManager, settings, project, user, preview) {

    //angular.module('builder').controller('NavbarController', ['$scope', '$rootScope', '$state', 'user', function($scope, $rootScope, $state, user) {

    $scope.settings = settings;
    $scope.undoManager = undoManager;
    $scope.project = project;
	$scope.state = $state;
    //$scope.loading = false;

	$scope.logout = function() {
		user.logout();
	};

	$scope.openPanel = function(name) {
		$rootScope.activePanel = name;
		$rootScope.flyoutOpen = true;
	};

	$scope.preview = function() {
		preview.show();
	};

	//$scope.isBoolean = function(value) {
	//	return typeof value !== 'boolean';
	//};

	$scope.resizeCanvas = function(size) {

		switch (size) {
		    case 'xs':
		        $scope.frame.removeClass().addClass('xs-width');
		        break;
		    case 'sm':
		        $scope.frame.removeClass().addClass('sm-width');
		        break;
		    case 'md':
		       $scope.frame.removeClass().addClass('md-width');
		        break;
		    default:
		        $scope.frame.removeClass().addClass('full-width');
		}

		//wait 400 ms till css transition ends so we can
		//get an accurate offset
		$timeout(function(){
			$rootScope.frameOffset = $scope.frame.offset();
		}, 450);

		$scope.selectBox.hide();
		$scope.hoverBox.hide();
		$scope.textToolbar.addClass('hidden');
        $scope.contextMenu.hide();
        $scope.linker.addClass('hidden');
        if ($scope.colorPickerCont) { $scope.colorPickerCont.addClass('hidden') };
	}

    $scope.undo = function() {
        undoManager.undo();
    }
    $scope.redo = function() {
        undoManager.redo();
    }
	
}]);