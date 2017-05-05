angular.module('builder.styling')

.controller('ThemesCreatorController', ['$scope', '$http', 'themes', function($scope, $http, themes) {
	
	$scope.themes = themes;

	//less variables that have been modified for the current theme.	
	$scope.modifiedVars = {};

	//custom users less for the theme, if any
	$scope.customLess = false;

	//controls custom less panel visibility
	$scope.customLessOpen = false;

	//bootstrap theme variables
	$scope.bootstrap = {
		defaultVars: false,
		activeVars: false,
		currentTheme: false,
	};

	//themes that's currently being edited variables
	$scope.editing = {
		theme: '',
		name: '',
		type: 'private',
		saving: false,
		errorMessage: false,
	};

	//if we're navigating away from theme creator clear the inputs
	$scope.$watch('activePanel', function(name, oldName) {
		if (oldName == 'themesCreator') {
			$scope.editing = {
				theme: '',
				name: '',
				type: 'private',
				saving: false,
				errorMessage: false,
			};

			themes.editing = false;
		}
	});

	$scope.applyChanges = function() {
		var dirty = [];

		//loop trough default bootstrap variables and current
		//ones and push any that don't match into dirty array 
		for (var group in $scope.bootstrap.currentTheme) {
			var vars = $scope.bootstrap.currentTheme[group].variables;
			
			for (var i = vars.length - 1; i >= 0; i--) {
				if (vars[i].value !== $scope.bootstrap.defaultVars[group].variables[i].value) {
					var temp = {}; temp[vars[i].name] = vars[i].value;
					dirty.push(temp);
				}
			};
		}

		$scope.modifyVars(dirty);
	};

	$scope.$watch('bootstrap.currentTheme', function(newT, oldT) {
		if ( ! newT || ! oldT) return;

		var active   = $scope.bootstrap.activeGroup,
			group    = newT[active].variables,
			oldGroup = oldT[active].variables,
			dirty    = {};

		for (var i = group.length - 1; i >= 0; i--) {
			var variable = group[i];

			if (variable.value !== oldGroup[i].value) {
				dirty[variable.name] = variable.value;
			}
		};

		$scope.modifyVars(dirty);
	}, true);

	//whether or not passed value is a less color value
	$scope.isColor = function(string) {
		return string.indexOf('#') > -1 ||
			   string.indexOf('darken') > -1 ||
			   string.indexOf('lighten') > -1;
	};

	$scope.getPreviewStyle = function(string) {
		var color = string;

		return {
			'border-right-color': color,
			'border-right-width': '25px',
		}
	};

	$scope.modifyVars = function(vars, ignore) {

		//if we get passed a string it's a value that
		//we simply need to aplly to modified vars
		if (angular.isString(vars)) {
			$scope.modifiedVars[$scope.activeVar] = vars;

		//if it's an array we'll need to loop through it
		//and extend modified vars with each object
		} else if (angular.isArray(vars)) {
			for (var i = vars.length - 1; i >= 0; i--) {
				$.extend($scope.modifiedVars, vars[i]);
			};

		//it's an object, we just need to extend modified vars
		} else {
			$.extend($scope.modifiedVars, vars);
		}

		//there are unsaved changes
		if ( ! ignore) {
			$scope.dirty = true;
		}
	
		$scope.less.modifyVars($scope.modifiedVars);
	};

	$scope.saveImage = function() {
		html2canvas($scope.doc.find('#preview-screen'), {width: 506}).then(function(canvas) {
			$http.post('http://localhost/architect/pr-themes/save-image/', { image: canvas.toDataURL('image/png', '0.7'), theme: $scope.editing.name })
		})
	};

	$scope.saveCurrent = function() {
		$scope.editing.errorMessage = false;
		$scope.editing.saving = true;

		var payload = {
			vars: $scope.modifiedVars,
			theme: $scope.editing.theme,
			name: $scope.editing.name,
			type: $scope.editing.type,
			image: 'themes/'+$scope.editing.name+'/image.png',
			custom: $scope.customLess,
		};

		$http.post('http://localhost/architect/pr-themes/', payload).success(function(data) {
			$scope.editing.saving = false;
			$scope.dirty = false;

			if (angular.isObject(data)) {
				$scope.editing.theme = data;
				themes.all.push(data);
			} else {
				$scope.editing.theme = '';
			}
			
			$scope.saveImage();
		}).error(function(data) {
			$scope.editing.saving = false;
			$scope.editing.errorMessage = data;
		});
	};

	$scope.toggleCustomLessPanel = function() {
		if ($scope.customLessOpen) {
			$scope.customLessOpen = false;
		} else {
			$scope.customLessOpen = true;
		}

		setTimeout(function() {
			$scope.lessEditor.resize();
			$scope.lessEditor.focus();
		}, 250);
	};
}])

.directive('blThemeCreator', ['$rootScope', '$http', 'localStorage', function($rootScope, $http, localStorage) {
    return {
   		restrict: 'A',
      	link: function($scope, el) {
      		var iframe = $('<iframe id="theme-creator-iframe" src="themePreview.html"></iframe>'),
      			list   = $('.vars-groups-list'),
      			picker = $('#bootstrap-theme-creator .sp-container'),
      			panel  = el.parent(), pickerCont = false,
      			button = $('#create-new-theme-button'),
      			booted = false, customStyles = false;

      		//bootstrap theme creator
      		var init = function(vars) {
				$rootScope.activePanel = 'themesCreator';		
      			$('.theme-creator-preview').append(iframe);

      			//get reference to some iframe window vars on load
      			iframe.load(function() {      			
      				$scope.doc   = $(iframe[0].contentWindow.document);
	      			$scope.less  = iframe[0].contentWindow.less;
	      			customStyles = $('<style id="custom-less"></style>').appendTo($scope.doc.find('head'));
	      			
	      			//apply any passed in less variables
	      			if (vars) { $scope.modifyVars(vars, true); };

	      			var bootstrapVars = localStorage.get('bootstrap-vars');
	      			
	      			if (bootstrapVars) {
	      				$scope.$apply(function() {
	      					$scope.bootstrap.defaultVars = $.extend(true, {}, bootstrapVars);
							$scope.bootstrap.currentTheme = $.extend(true, {}, bootstrapVars);

							//set first variable group as active one
							for (var name in $scope.bootstrap.currentTheme) {			
								$scope.bootstrap.activeGroup = name;
								$scope.bootstrap.activeVars = $scope.bootstrap.currentTheme[name];
								break;
							}
	      				});
	      			} else {
	      				$http.get('http://localhost/architect/pr-themes/bootstrap-vars').success(function(data) {

	      					localStorage.set('bootstrap-vars', data);

							$scope.bootstrap.defaultVars = $.extend(true, {}, data);
							$scope.bootstrap.currentTheme = $.extend(true, {}, data);

							//set first variable group as active one
							for (var name in $scope.bootstrap.currentTheme) {			
								$scope.bootstrap.activeGroup = name;
								$scope.bootstrap.activeVars = $scope.bootstrap.currentTheme[name];
								break;
							}		
						});
	      			}

					//initiate custom less editor
					$scope.lessEditor = ace.edit('custom-less-editor');
					$scope.lessEditor.setTheme('ace/theme/dawn');
					$scope.lessEditor.getSession().setMode('ace/mode/less');
					
					$scope.lessEditor.getSession().on('change', blDebounce(function() {
			            $scope.customLess = $scope.lessEditor.getValue();
			            	$scope.less.render($scope.customLess).then(function(css, error) {					
								if (css.css) {
									customStyles.html(css.css)
								}
							});
			                        
			        }, 500));

			        //make sure themes custom less gets rendered if it has any
					if ($scope.customLess) {
						$scope.lessEditor.setValue($scope.customLess, -1);
					}

					//change theme name in iframe so correct use is used for thumbnail
					$scope.$watch('editing.name', function(newName, oldName) {
						if (newName && newName.length > 3) {
							$scope.doc.find('#preview-screen h1').text(newName);
						}
					});

			        iframe.unbind('load');
	      		});

      			//initiate theme creator color picker
	      		var picker = $('#theme-creator-color-picker').spectrum({
			    	flat: true,
			    	showAlpha: true,
			    	showPaletteOnly: true,
   					togglePaletteOnly: true,
   					palette: colorsForPicker,
			    	appendTo: $('#theme-creator-color-picker'),
				});

				pickerCont = $('#bootstrap-theme-creator .sp-container');
	      			
	      		//hide picker on choose button click or outside it
	      		$('#bootstrap-theme-creator').add(pickerCont.find('.sp-choose')).on('click', function(e) {
	      			pickerCont.addClass('hidden');
	      		});

				//apply new color to less variable
				picker.on('dragstop.spectrum change.spectrum', function(e, color) {
					$scope.modifyVars(color.toRgbString());
				});

				button.on('click', function() {
					$scope.$apply(function() {
						$rootScope.activePanel = 'themesCreator';
					})
	      		});

	      		booted = true;
      		};

      		button.one('click', function(e) {
      			if ( ! booted) { init(); }; 
      		});

      		//on editing theme change in themes factory load it into the theme editor
      		$scope.$watch('themes.editing', function(newTheme) {
				if ( ! newTheme) return;

				$scope.editing.theme = newTheme;
				$scope.editing.name = newTheme.name;
				$scope.editing.type = newTheme.type;
				$scope.openPanel('themesCreator');
				$scope.customLess = newTheme.customLess;

				if ( ! booted) { 
					init(JSON.parse($scope.editing.theme.vars)); 
				} else {
					$scope.modifyVars(JSON.parse($scope.editing.theme.vars));

					if ($scope.customLess) {
						$scope.lessEditor.setValue($scope.customLess, -1);
					}
				}	
			});

      		$('.vars-values-list').on('click', 'input', function(e) {
      			e.stopPropagation();

      			//hide picker if clicked not on color input
      			if ( ! $scope.isColor(e.currentTarget.value)) {
      				return pickerCont.addClass('hidden');
      			}

      			var target = $(e.currentTarget),
      				offset = target.offset(),
      				width  = target.width(),
      				height = target.height();

      			//save this vars name so we know what to apply colors chosen via picker
      			$scope.activeVar = target.data('name');
 				
				$('#bootstrap-theme-creator .sp-container').css({
					top: offset.top - height + 10,
					left: offset.left + 15,
				}).removeClass('hidden').show();
      		});
      		
      		//on variable group name click set that group to actve one
      		list.on('click', 'li', function(e) {
      			var name = e.currentTarget.dataset.name;

      			$scope.$apply(function() {
      				$scope.bootstrap.activeGroup = name;
      				$scope.bootstrap.activeVars = $scope.bootstrap.currentTheme[name];
      			});
      		});

      		//on variables group change scroll preview iframe to that group
      		$scope.$watch('bootstrap.activeGroup', function(name, oldName) {
      			if (name && name != oldName) {
      				var offset = $scope.doc.find('#'+name.toLowerCase().trim().replace(/ /g, '-')).offset();

      				if (offset) {
      					$scope.doc.find('html, body').animate({ scrollTop: offset.top-20 });
      				}
      				
      			}	
      		});
      	}
    };
}])