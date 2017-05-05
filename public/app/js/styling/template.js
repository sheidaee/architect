angular.module('builder.styling', [])
    .controller('TemplatesController', ['$scope', '$http', '$state', 'templates', '$upload', function($scope, $http, $state, templates, $upload) {

        $scope.templates = templates;

        //$scope.templateData = {
        //name: '',
        //    type: 'create',
        //    color: 'blue',
        //    eplaceContents: false,
        //    category: 'Landing Page',
        //
        //    clear: function() {
        //    	this.name = '';
        //    	this.type = 'create';
        //    	this.replaceContents = false;
        //    },
        //
        //    getUpdateData: function() {
        //    	var data = {
        //			name: this.name,
        //			color: this.color,
        //			category: this.category,
        //		};
        //
        //		if (this.replaceContents) {
        //			data.pages = this.getPagesContent();
        //		}
        //
        //		return data;
        //    },
        //
        //    getCreateData: function() {
        //
        //    	var data = {
        //    		pages: [],
        //    		name: this.name,
        //    		color: this.color,
        //    		category: this.category,
        //    		theme: themes.active.name ? themes.active.name : 'default',
        //    	};
        //
        //    	data.pages = this.getPagesContent();
        //
        //    	return data;
        //    },
        //
        //    getPagesContent: function() {
        //    	var content = [];
        //
        //    	//if we have more then 1 page then send css/html of each page to server
        //    	if (project.active.pages.length > 1) {
        //    		for (var i = 0; i < project.active.pages.length; i++) {
        //    			var page = project.active.pages[i];
        //
        //    			content.push({
        //    				js: page.js,
        //	   				css: page.css,
        //    				html: page.html,
        //    				tags: page.tags,
        //    				name: page.name,
        //    				title: page.title,
        //    				theme: page.theme,
        //    				description: page.description,
        //    				libraries: $.map(page.libraries, function(lib) { return lib.name } ),
        //    			});
        //    		};
        //
        //    	//otherwise just grab the current html/css
        //    	} else {
        //    		content.push({
        //    			css: css.compile(),
        //    			html: dom.getHtml(),
        //    			js: project.activePage.js,
        //    			libraries: $.map(project.activePage.libraries, function(lib) { return lib.name } ),
        //    			name: 'index',
        //    		});
        //    	}
        //
        //    	return content;
        //    }
        //};
    }])
    .factory('templates', ['$rootScope', '$http', function($rootScope, $http) {

        var templates = {

            /**
             * All available templates.
             *
             * @type {Array}
             */
            all: [],

            colors: [
                {name: 'سیاه', value: 'black'},{name: 'آبی', value: '#84BDDB'},{name: 'خاکستری', value: '#eee'},{name: 'سبز', value: '#18BB9B'},
                {name: 'قهوه ای', value: '#5A4A3A'},{name: 'پرتغالی', value: '#DA5C4A'},{name: 'قرمز', value: 'red'}, {name: 'زرد', value: '#FFDF60'},
                {name: 'سفید', value: '#FAFAFA'},{name: 'بنفش', value: '#B84B61'}

            ],

            categories: ['صفحات عریض', 'وبلاگ', 'فوتوبلاگ'],

            loading: false,
            //
            //save: function(template) {
            //    this.loading = true;
            //
            //    return $http.post('http://localhost/architect/pr-templates/', template).success(function(data) {
            //
            //        project.createThumbnail().then(function(canvas) {
            //            $http({
            //                url: 'pr-templates/'+data.thumbId+'/save-image',
            //                dataType: 'text',
            //                method: 'POST',
            //                data: canvas.toDataURL('image/png', 1),
            //                headers: { "Content-Type": false }
            //            }).success(function() {
            //                templates.all.push(data);
            //                templates.loading = false;
            //            }).error(function() {
            //                templates.loading = false;
            //            })
            //
            //            //remove iframe left behind by html2canvas
            //            $rootScope.frameBody.find('iframe').remove();
            //        });
            //    });
            //},
            //
            //update: function(template, id) {
            //    if ( ! id) return false;
            //
            //    this.loading = true;
            //
            //    return $http.put('http://localhost/architect/pr-templates/'+id, template).success(function(data) {
            //
            //        if (data.thumbId) {
            //            project.createThumbnail().then(function(canvas) {
            //                $http({
            //                    url: 'http://localhost/architect/pr-templates/'+data.thumbId+'/save-image',
            //                    dataType: 'text',
            //                    method: 'POST',
            //                    data: canvas.toDataURL('image/png', 1),
            //                    headers: { "Content-Type": false }
            //                }).success(function() {
            //                    for (var i = templates.all.length - 1; i >= 0; i--) {
            //                        if (templates.all[i].id == data.id) {
            //                            templates.all[i] = data;
            //                        }
            //                    };
            //                    templates.loading = false;
            //                }).error(function() {
            //                    templates.loading = false;
            //                });
            //
            //                //remove iframe left behind by html2canvas
            //                $rootScope.frameBody.find('iframe').remove();
            //            });
            //        }
            //    });
            //},
            //
            //delete: function(id) {
            //    return $http.delete('http://localhost/architect/pr-templates/'+id).success(function(data) {
            //
            //        for (var i = templates.all.length - 1; i >= 0; i--) {
            //            if (templates.all[i].id == id) {
            //                templates.all.splice(i, 1);
            //            }
            //        };
            //
            //    });
            //},
            //
            /**
            * Fetch all available templates from server.
            *
            * @return Promise|undefined
            */
            getAll: function() {
                if ( ! templates.all.length) {
                    return $http.get('/pr-templates/').success(function(data) {
                        templates.all.push.apply(templates.all, data);
                    });
                }
            },
            //
            ///**
            // * Return a template matching given id.
            // *
            // * @param  string/int id
            // * @return undefined/object
            // */
            //get: function(id) {
            //    for (var i = templates.all.length - 1; i >= 0; i--) {
            //        if (templates.all[i].id == id) {
            //            return templates.all[i];
            //        }
            //    };
            //},

        };

        return templates;
    }]);