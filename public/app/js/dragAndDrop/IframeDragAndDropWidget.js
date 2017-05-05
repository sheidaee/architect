angular.module('dragAndDrop')

.directive('blIframeNodesSortable', ['$rootScope', 'dom', 'undoManager', 'iframeScroller', function($rootScope, dom, undoManager, iframeScroller) {

	/**
	 * Makes DOM nodes inside the iframe sortable.
	 * Extends Jquery UI base mouse widget.
	 * 
	 */
	$.widget('ui.iframeNodesSortable', $.ui.mouse, {

		 /**
		  * Handle dragging start in the iframe.
		  * 
		  * @param  Object e
		  * @return boolean
		  */
	  	_mouseStart: function(e) {
	  		$rootScope.$apply(function() { $rootScope.dragging = true; });

	  		$rootScope.frameBody.addClass('dragging');
	    	            	
	       	//start a new undo command
	       	this.params = {
	       		undoIndex: utils.getNodeIndex(this.active.parentContents, this.active.node),
	       		node: this.active.node,
	       		parent: this.active.parent,
	       		parentContents: this.active.parentContents
	       	};

	       	//create the visual helper by cloning the active node
	        this.active.helper = $(this.active.node).clone().css({
	        	'position': 'absolute', 
	        	'pointer-events': 'none', 
	        	'margin': '0', 
	        	'padding': '0',
	        }).addClass('helper');

	        //throw helper into the DOM
	       	$rootScope.frameBody.append(this.active.helper);

	        this._mouseDrag(e, true);	

	        return true;
	  	},
	  	_mouseDrag: function(e) {
	  		var self  = this,
	  			scrollTop = $rootScope.frameBody.scrollTop(),
		   		point = { x: e.pageX, y: e.pageY - scrollTop };
		   		
		   	var el = $rootScope.elementFromPoint(point.x, point.y);

		   	iframeScroller.scroll(e.pageY - scrollTop + $rootScope.frameOffset.top * 2);

		   	self.active.helper.css('left', (point.x + 10)).css('top', (point.y + scrollTop - 20));
  
            //only reposition hover box during drag on webkit browsers
            //as it will cause fairly significant lag on IE and Firefox                  
            if ($rootScope.isWebkit) {
            	$rootScope.repositionBox('hover', el);
            }
            
		   	var classes = $rootScope.selected.node.className;
		   
		   	if (classes && classes.match('col-')) {
	            return this._sortColumns(el, point);
	        } else {
	        	return dom.appendSelectedTo(el, point);
	        }
		 },
	   	_mouseStop: function(e) {
	   		iframeScroller.stopScrolling();
	    	this.active.helper.remove();

	    	$rootScope.$apply(function() {
	    		$rootScope.dragging = false;   	
	    	})
	    	
	    	//store index of active on last command so we can redo it
	    	this.params.redoIndex = utils.getNodeIndex(this.active.node.parentNode.childNodes, this.active.node);
	    	undoManager.add('reorderElement', this.params);

	    	$rootScope.repositionBox('select');

	    	$rootScope.frameBody.removeClass('dragging');

	    	$rootScope.$broadcast('builder.html.changed');
	  	},
	  	_mouseCapture: function(e) {
	  		this.active = $rootScope.selected;

	  		if ( ! this.active.node || 
	  			this.active.node.className.indexOf('locked') > -1 ||
	  			this.active.node.contenteditable ||
	  			$rootScope.rowEditorOpen
	  		)
	  		{
	  			return false;
	  		}
	        
	  		return true;
	  	},
		_init: function() {
		    return this._mouseInit();
		},
		_destroy: function() {
		    return this._mouseDestroy();
		},
		_sortColumns: function(node, point) {
	        var className = $(node).parent().attr('class');

	        //constrain column ordering withing row
	        if (className && className.match('row')) {

	            //switch column positions 
	            if (this.oldPoint && point.x > this.oldPoint.x) {
	                $($rootScope.selected.node).before(node);
	            } else {
	                $($rootScope.selected.node).after(node);
	            }
	        }
	        
	        this.oldPoint = point;   
	    },
	});

	return {
		restrict: 'A',
		link: function($scope, el) {
			$scope.$on('builder.dom.loaded', function(e) {
				$scope.frameBody.iframeNodesSortable({
					//prevent nodes whose text is being edited from being dragged
					cancel: '[contenteditable="true"]',
				});
			});
		},
	}

}]);