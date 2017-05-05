angular.module('builder.editors')

.factory('libraries', ['$rootScope', '$http', 'project', function($rootScope, $http, project) {
	
	var libs = {

		/**
		* Libraries modal form model
		*
		* @type {Object}
		*/
		form: {
			name: '',
			path: '',
			type: 'create',
		},

		/**
		* All available libraries.
		*
		* @type {Array}
		*/
		all: [],

		/**
		* Return a library by given name.
		*
		* @param  string name
		* @return Object|Undefined
		*/
		get: function(name) {
			for (var i = 0; i < this.all.length; i++) {
				if (this.all[i].name == name) {
					return this.all[i];
				}
			};
		},

		/**
		 * Fetch all available libraries from server.
		 * 
		 * @return Promise
		 */
		fetchAll: function() {
			return $http.get('libraries').success(function(data) {
				libs.all = data;
			});
		},

		/**
		* Save new library to database.
		*
		* @return Promise
		*/
		save: function(payload) {
			return $http.post('libraries', payload).success(function(data) {
				libs.all.push(data);
			});
		},

		/**
		* Update existing library.
		*
		* @return Promise
		*/
		update: function(payload) {
			return $http.put('libraries/'+payload.id, payload).success(function(data) {
				for (var i = 0; i < libs.all.length; i++) {
					if (libs.all[i].id == data.id) {
						libs.all[i] = data;
					}
				};
			});
		},

		/**
		* Delete library with given name.
		*
		* @param  string name
		* @return Promise
		*/
		delete: function(name) {
			var lib = this.get(name);

			return $http.delete('libraries/'+lib.id).success(function(data) {
                console.log(data);
				for (var i = 0; i < libs.all.length; i++) {
					if (libs.all[i].id == lib.id) {
						libs.all.splice(i, 1);
					}
				};
			}).error(function (data) {
                console.log(data);
            });
		},

		/**
		* Return whether or not given library is attached to currently active page.
		*
		* @param  Object|String name
		* @return Boolean
		*/
		isAttached: function(name) {

			//get only name if we've got library object passed in
			if ( ! angular.isString(name)) {
				name = name.name;
			}

			if ( ! project.activePage.libraries) { return false; };

            for (var i = project.activePage.libraries.length - 1; i >= 0; i--) {
				var library = project.activePage.libraries[i];

				if (library == name || library.name == name) {
                    return true;
				}
			};
            //console.log(project.activePage.libraries[1].name);

        },

		/**
		* Attach library with given name to currently active page.
		*
		* @param  string  name
		* @param  integer id
		* @return Promise
		*/
		attach: function(name, id) {
			var pageId = id ? id : project.activePage.id;

			return $http.post('libraries/attach/'+pageId+'/'+name).success(function(data) {
				if (id) {
					for (var i = project.active.pages.length - 1; i >= 0; i--) {
						if (project.active.pages[i].id == id) {

							if ( ! project.active.pages[i].libraries) {
								project.active.pages[i].libraries = [];
							}

							project.active.pages[i].libraries.push(data);
						}
					};
				} else {
					project.activePage.libraries.push(data);
				}
			});
		},

		/**
		* Detach library with given name from currently active page.
		*
		* @param  string name
		* @return Promise
		*/
		detach: function(name) {
			$http.post('libraries/detach/'+project.activePage.id+'/'+name).success(function(data) {
				for (var i = 0; i < project.activePage.libraries.length; i++) {
					if (project.activePage.libraries[i].name == name) {
						project.activePage.libraries.splice(i, 1);
					}
				};
			});
		},

	};

	return libs;

}])

.directive('blJsLibraries', ['libraries', function(libraries) {
    return {
   		restrict: 'A',
      	link: function($scope, el) {
        	var modal = $('#new-library-modal');

        	//create new library
        	el.find('.new-library').on('click', function(e) {
        		modal.modal({backdrop: false});

        		$scope.$apply(function() {
        			libraries.form.name = '';
        			libraries.form.path = '';
        			libraries.form.id   = '';
        			libraries.form.type = 'create';
        		});
        	});

        	el.find('ul').on('click', function(e) {
        		var target = $(e.target),
        			name   = target.closest('li').data('name');
        		
        		//edit library
        		if (target.is('.edit-library'))
        		{
        			modal.modal({backdrop: false});
        			libraries.form.type = 'edit';
        			var lib = libraries.get(name);
                    //console.log(lib);
        			$scope.$apply(function() {
        				libraries.form.id   = lib.id;
        				libraries.form.name = lib.name;
        				libraries.form.path = lib.path;
        			});
        		}

        		//delete library
        		else if (target.is('.delete-library'))
        		{
        			alertify.confirm("این کار باعث حذف دائم کتابخانه می شود، در صورت ادامه بر روی دکمه تایید کلیک کنید.", function (e) {
        				if (e) { libraries.delete(name); }; 
        			});
        		}

        		//attach/detach library
        		else if (target.is('li') || target.parent().is('li'))
        		{
	        		if (libraries.isAttached(name)) {
	        			libraries.detach(name);
	        		} else {
	        			libraries.attach(name);
	        		}
        		}
        	});
      	}
    };
}])

.directive('blNewLibraryModal', ['libraries', function(libraries) {
    return {
   		restrict: 'A',
      	link: function($scope, el) {
      		var modal = $('#new-library-modal');

      		//close modal on close button click
      		el.find('.close-modal').on('click', function(e) {
      			modal.modal('hide');
      		});

            el.find('.close-modal, .close').on('click', function (e) {
                libraries.form.error = '';
            });

      		//save/update the library on save&close button click
        	el.find('.save-library').on('click', function(e) {
        		if (libraries.form.type == 'create') {
        			var promise = libraries.save({ name: libraries.form.name, path: libraries.form.path });
        		} else {
        			var promise = libraries.update({ id: libraries.form.id, name: libraries.form.name, path: libraries.form.path });
        		}

        		promise.success(function(data) {
        			modal.modal('hide');
        			libraries.form.error = '';
        		}).error(function(data) {
                    //data.forEach(function(value, index) { console.log( value) }, data);
                    //$.map(data, function (value, index) {
                    //    libraries.form.error.append(value);
                    //});
        			libraries.form.error = data;
        		});
        	});
      	}
    };
}])