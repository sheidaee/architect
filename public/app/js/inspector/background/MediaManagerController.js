angular.module('builder.inspector')

.controller('MediaManagerController', ['$scope', '$upload', '$http', 'inspector', function($scope, $upload, $http, inspector) {

		$scope.modal = $('#images-modal');

		$scope.sorting = { prop: 'created_at', reverse: false };

		//uploadd files to filesystem
        $scope.onFileSelect = function($files) {
		    for (var i = 0; i < $files.length; i++) {
		      	$scope.upload = $upload.upload({
		        	url: 'images/store',
		        	data: $scope.selectedFolder.id ? { folderName: $scope.selectedFolder.name, folderId: $scope.selectedFolder.id} : false,
		        	file: $files[i],
		      	}).success(function(data) {
                    console.log(data);
		        	for (var i = data.length - 1; i >= 0; i--) {
		        		$scope.images.push(data[i]);
		        	};
		      	});
		    }
		};	

		//fetch images and folders from backend when modal is first shown
		$scope.modal.one('show.bs.modal', function(e) {
			$http.get('folders/all').success(function(data) {
				$scope.folders = $scope.folders.concat(data);
			});

			$http.get('images/all').success(function(data) {
				$scope.images = data;

				setTimeout(function() {
					$scope.modal.find('[data-toggle="tooltip"]').tooltip({
						container: 'body',
					});
				}, 100);
			});
		});

		
		$scope.useImage = function() {
			if ($('#images-modal').data('type') == 'background') {
				$scope.setAsBackground();
			} else {
				$scope.setAsSource();
			}

			$scope.modal.modal('hide');
		};

		$scope.setAsSource = function() {
			if ($scope.activeTab == 'my-images') {
				for (var i = $scope.images.length - 1; i >= 0; i--) {
					if ($scope.images[i].id == $scope.selectedImages[0]) {
						var path = $scope.baseUrl+'/images/uploads/'+$scope.images[i]['file_name'];
					}
				};
			} else {
				if ($scope.downloadLocally) {
					$http.post('images/', { url: $scope.webImageUrl }).success(function(data) {
						$scope.selected.node.src = data;
					});
				} else {
					var path = $scope.webImageUrl;
				}
			}

			if (path) {
				$scope.selected.node.src = path;
			}
		};

		$scope.setAsBackground = function() {
			if ($scope.activeTab == 'my-images') {
				for (var i = $scope.images.length - 1; i >= 0; i--) {
					if ($scope.images[i].id == $scope.selectedImages[0]) {
						var path = $scope.baseUrl+'/images/uploads/'+$scope.images[i]['file_name'];
					}
				};
			} else {
				if ($scope.downloadLocally) {
					$http.post('images/', { url: $scope.webImageUrl }).success(function(data) {
						inspector.applyCss('background-image', 'url("'+data+'")', $scope.selected.getStyle('background-image'));
					});
				} else {
					var path = $scope.webImageUrl;
				}
			}

			if (path) {
				inspector.applyCss('background-image', 'url("'+path+'")', $scope.selected.getStyle('background-image'));
			}
		};

		//delete all selected images
		$scope.deleteSelectedImages = function() {
			$http.post('images/delete', { ids: $scope.selectedImages }).success(function(data) {
                for (var i = $scope.selectedImages.length - 1; i >= 0; i--) {
					for (var ind = $scope.images.length - 1; ind >= 0; ind--) {
						if ($scope.images[i] && $scope.images[i].id == $scope.selectedImages[ind]) {
							$scope.images.splice(ind, 1);
							continue;
						}
					}
				}
			}).error(function (data) {
                console.log(data);
            });
		};

		//delete a folder with selected id
		$scope.deleteFolder = function(id) {
			$http.delete('folders/'+id).success(function(data) {
				if (parseInt(data)) {
					for (var i = $scope.folders.length - 1; i >= 0; i--) {
						if ($scope.folders[i].id == id) {
							$scope.folders.splice(i, 1);
						}
					};
				}
			});
		};

		//remove temp folder from folders array and unset it as selected
		$scope.cancelFolderCreation = function() {
			for (var i = $scope.folders.length - 1; i >= 0; i--) {
				if ($scope.folders[i].creating == true) {
					$scope.folders.splice(i, 1);

					var prev = $scope.folders[i-1];
					$scope.selectedFolder = { id: prev.id, name: prev.name };
					$scope.creatingNewFolder = false;
				}
			};
		};

		//send request to server to create a new folder
		$scope.createFolder = function() {
			if ($scope.newFolder) {
				$http.post('folders', $scope.newFolder).success(function(data) {
					$scope.selectedFolder = { id: data.id, name: data.name };
					$scope.creatingNewFolder = false;

					for (var i = $scope.folders.length - 1; i >= 0; i--) {
						if ($scope.folders[i].creating === true) {
							$scope.folders[i] = data;
						}
					};

					$scope.newFolder.name = '';
				});
			}
		};

		//change sorting
		$scope.changeSorting = function(name) {
			$scope.sorting.prop = name;

			if (name == 'created_at') {
				$scope.sorting.reverse = true;
			} else {
				$scope.sorting.reverse = false;
			}
		};

		//whether or not image with given id is selected
		$scope.isSelected = function(id) {
			return $scope.selectedImages.indexOf(id) > -1;
		};

		//watch checkbox and select/deselect all images
		$scope.$watch('selectAll', function(value) {
			if (value === true) {
				for (var i = $scope.images.length - 1; i >= 0; i--) {
					$scope.selectedImages.push($scope.images[i].id);
				};
			} else if (value === false) {
				$scope.selectedImages = [];
			}
		});

		//all images user has access to
		$scope.images = [];

		//all folders user has access to
		$scope.folders = [{ name: 'All Images', id: false }];

		//new folder info to send to backend
		$scope.newFolder = { name: '' };

		//currently active folder
		$scope.selectedFolder = { name: 'All Images' };

		//ids of currently selected images
		$scope.selectedImages = [];

		$scope.activeTab = 'my-images';
}])

.directive('blNewImageFolder', ['$http', function($http) {
	return {
		restrict: 'A',
		link: function($scope, el, attrs, controller) {		
			el.on('click', function(e) {
				if ( ! $scope.creatingNewFolder) {
					$scope.$apply(function() {

						$scope.folders.splice(1, 0, {
							name: false,
							creating: true,
						});
						
						$scope.selectedFolder = $scope.folders[1];

						$scope.creatingNewFolder = true;

						$('#folders-cont input').focus();
					});
				}
			});
		}
	};
}])

//select folder
.directive('blImageFolderSelectable', function() {
	return {
		restrict: 'A',
		link: function($scope, el, attrs, controller) {
			$(el).on('click', 'li', function(e) {
				var li = $(e.currentTarget);

				if (li.data('id') || li.data('name') == 'All Images') {
					$scope.$apply(function() {
						$scope.selectedFolder.id = li.data('id') || '';
						$scope.selectedFolder.name = li.data('name') || '';
					});
				}
			});
		}
	};
})

//select image
.directive('blImagesSelectable', function() {
	return {
		restrict: 'A',
		link: function($scope, el, attrs, controller) {
			el.on('click', 'li', function(e) {
				var target = $(e.currentTarget),
					id     = target.data('id');

				if (target.hasClass('selected')) {
					for (var i = $scope.selectedImages.length - 1; i >= 0; i--) {
						if ($scope.selectedImages[i] == id) {
							$scope.$apply(function() {
								$scope.selectedImages.splice(i, 1);
							});
						}
					};
				} else {
					$scope.$apply(function() {
						$scope.selectedImages.push(id);
					});
				}					
			});
		}
	};
})
    //, 'imageEditor
.directive('blImageActions', ['$http', function($http) {
	return {
		restrict: 'A',
		link: function($scope, el, attrs, controller) {
			el.on('click', function(e) {
				var target = $(e.target);

				//delete image
				if (target.hasClass('delete-image')) {
					var id = target.closest('li').data('id');

					$http.delete('images/'+id).success(function(data) {
                        //console.log(data);
						if (parseInt(data)) {
							for (var i = $scope.images.length - 1; i >= 0; i--) {
								if ($scope.images[i].id == id) {
									$scope.images.splice(i, 1);
								}
							};
						}
					})
                    .error(function (data) {
                        console.log(data);
                    });
				}

				////edit image
				//else if (target.hasClass('edit-image')) {
				//	var url = target.closest('li').find('.img-wrapper').css('background-image').replace('url(', '').replace(')', '');
				//	imageEditor.oldUrl = url;
				//
				//	imageEditor.open($('<img src="'+url+'">'), url);
				//}
			});		
		}
	};
}])

.directive('openMediaManager', function() {
	return {
		restrict: 'A',
		link: function($scope, el, attrs) {		
			var type  = attrs.openMediaManager,
				modal = $('#images-modal');

			el.on('click', function(e) {
				modal.data('type', type).modal('show');
			});
		}
	};
})
