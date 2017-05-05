<div id="new-project-container" class="rtl">
	<div class="container">
		<div class="row">
			<div class="col-sm-3">
				<button class="btn btn-primary btn-block start-with-blank" ng-click="showNameModal()">ایجاد قالب خالی</button>

				<div class="colors-container" bl-new-project-color-selector>
					<h4>رنگ ها</span></h4>
					<ul class="list-unstyled">
                        <li title="{{ color.name }}" ng-repeat="color in templates.colors" ng-style="{ background: color.value }" data-color="{{ color.name }}"></li>
                    </ul>
					</ul>
				</div>

				<div class="categories-container">
					<h4>گروه ها</h4>
					<ul class="list-unstyled">
                        <li ng-class="{active: ! filters.category}" ng-click="filters.category = ''" data-cat="">تمام گروه ها</li>
                        <li ng-class="{active: category == filters.category}" ng-click="filters.category = category" data-cat="category" ng-repeat="category in templates.categories">{{ category }}</li>
					</ul>
				</div>
			</div>
			<div class="col-sm-9" ng-cloak>
				<figure ng-repeat="template in filteredTemplates = (templates.all | filter: { color: filters.color } | filter: { category: filters.category })" ng-click="showNameModal(template)" class="col-sm-4">
                    <img class="img-responsive" ng-src="{{ template.thumbnail }}">
                    <figcaption>{{ template.name }}</figcaption>
				</figure>
				<div class="no-results" ng-show="filteredTemplates && filteredTemplates.length == 0">قالبی یافت نشد.</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade rtl" id="project-name-modal">
  	<div class="modal-dialog">
    	<div class="modal-content">
    		<div class="modal-header">
	        	<button type="button" class="close pull-left" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
	        	<h4 class="modal-title"><i class="fa fa-pencil"></i> ایجاد پروژه جدید</h4>
	      	</div>
      		<div class="modal-body">
      			<div class="clearfix">
      				<div class="col-sm-2 pull-right"><label for="project-name">نام</label></div>
      				<div class="col-sm-10"><input type="text" class="form-control" name="project-name" ng-model="name"></div>
      			</div>
      			<p class="text-danger error"></p>
      		</div>
      		<div class="modal-footer">
      			<button type="button" class="btn btn-danger" data-dismiss="modal">انصراف</button>
      			<button type="button" class="btn btn-success pull-left" ng-click="createNewProject()">ایجاد</button>
      		</div>
    	</div>
  	</div>
</div>