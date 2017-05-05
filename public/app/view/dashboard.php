<section id="filter-bar" class="rtl">
    <div class="container">

        <div class="pull-left">
            <div class="form-group">
                <label for="name">جستجو</label>
                <input name="name" ng-model="filters.query" class="form-control">
            </div>

            <div class="form-group">
                <label for="status">وضعیت</label>
                <select name="status" ng-model="filters.status" class="form-control">
                    <option value="">همه</option>
                    <option value="1">منتشر شده</option>
                    <option value="0">منتشر نشده</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">مرتب سازی</label>
                <select name="status" ng-model="filters.sort" ng-change="filters.setSortProp()" class="form-control">
                    <option value="newest">جدید ترین</option>
                    <option value="oldest">قدیمی ترین</option>
                </select>
            </div>
        </div>

        <div class="pull-right">
            <a ui-sref="new" class="btn btn-success">ایجاد پروژه جدید</a>
        </div>

    </div>
</section>

<section id="dashboard-container">

    <div class="container">

        <ul id="projects-list" class="row" ng-if="projects.all.length">
            <li class="col-sm-4 col-lg-3 pull-right" ng-repeat="project in projects.all | filter:{name:filters.query} | filter:{published:filters.status} | orderBy:filters.order:filters.reverse">
                <figure data-name="{{ project.name }}">
                    <div class="header clearfix">
                        <a title="پیش نمایش پروژه" target="_blank" href="" class="url">نمایش پروژه</a>
                    </div>
                    <img bl-open-in-builder class="img-responsive" ng-src="images/projects/project-{{project.id}}.png">
                    <figcaption class="clearfix">
                        <div class="pull-left name">{{ project.name }}</div>
                        <div class="pull-right">
                            <i title="حذف پروژه" ng-click="deleteProject(project)"  class="delete-project fa fa-trash-o"></i>
                            <i title="ویرایش پروژه" bl-open-in-builder class="edit-project fa fa-pencil"></i>
                        </div>
                    </figcaption>
                    <div class="spinner hidden">
                        <i class="fa fa-spinner fa-spin"></i>
                    </div>
                </figure>
            </li>
        </ul>

        <div id="no-projects" class="text-center rtl" ng-if="!projects.all.length">
            <h3>پروژه ای یافت نشد.</h3>
            <a ui-sref="new" class="btn btn-success">یک پروژه ایجاد کنید</a>
        </div>

    </div>

</section>