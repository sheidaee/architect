<section id="viewport" class="clearfix">
	<div class="flyout-from-right" ng-class="flyoutOpen ? 'open' : 'closed'">
        <div class="flyout-content" ng-class="activePanel == 'themes' ? 'open' : 'closed'" id="themes-panel" ng-controller="ThemesController" bl-render-themes>
            <div bl-close-flyout-panel></div>

            <section class="row inner-content">
				<div class="col-sm-6" id="themes-list"></div>
				<div class="col-sm-6" id="themes-preview"></div>
			</section>

			<section class="row flyout-panel-toolbar">
				<div class="inner">
					<button class="btn btn-success" id="create-new-theme-button">createNew</button>

					<select>
						<option value="">all</option>
						<option value="bootswatch">bootswatch</option>
						<option value="yours">yours</option>
						<option value="public">architectPublic</option>
					</select>

					<input type="text" placeholder="Search...">

					<button class="btn btn-info">export</button>

					<div class="error-message"><span class="text-danger"></span></div>
				</div>
			</section>
		</div>

        <div class="flyout-content" ng-class="activePanel == 'pages' ? 'open' : 'closed'" ng-controller="PagesController" id="pages-panel">
            <div bl-close-flyout-panel></div>
            <div class="row inner-content">
                <section class="col-lg-2 col-xs-4 pull-right rtl" id="pages-list">
                    <div class="header">
                        <div class="pull-right">صفحات</div>
                        <button class="btn btn-primary pull-left" ng-click="createNewPage()">افزودن <i class="fa fa-plus"></i></button>
                    </div>
                    <div class="body">
                        <ul class="list-unstyled">
                            <li ng-repeat="page in project.active.pages" ng-click="project.changePage(page.name)" ng-class="{active: page.name == project.activePage.name}">
                                <span class="name">{{ page.name }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="footer clearfix">
                        <button ng-click="emptyProject()" class="btn btn-danger pull-left"><i class="fa fa-trash"></i> خالی کردن پروژه</button>
                        <div ng-if="loading" class="spinner pull-right"><i class="fa fa-spinner fa-spin"></i></div>
                    </div>
                </section>
                <section class="col-lg-10 col-xs-8" id="edit-page">
                    <div class="inner" ng-class="{ hidden: !project.activePage }">
                        <div class="page-settings">
                            <h2 class="rtl">ویرایش صفحه <span>{{ project.activePage.name }}</span></h2>

                            <div class="form-group rtl">
                                <label for="page-name">نام صفحه</label>
                                <input type="text" ng-disabled="project.activePage.name == 'Index'" class="form-control" ng-model="project.activePage.name" name="page-name">
                            </div>

                            <div class="form-group rtl">
                                <label for="page-title">عنوان</label>
                                <input type="text" class="form-control" ng-model="project.activePage.title" name="page-title">
                            </div>

                            <div class="form-group rtl">
                                <label for="page-description">توضیحات</label>
                                <textarea ng-model="project.activePage.description" name="page-description" rows="5" class="form-control"></textarea>
                            </div>

                            <div class="form-group rtl">
                                <label for="page-tags">برچسب ها</label>
                                <textarea ng-model="project.activePage.tags" name="page-tags" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="page-preview" bl-render-page-preview>

                        </div>
                    </div>
                    <div class="footer">
                        <button class="btn btn-default btn-lg" ng-disabled="project.activePage.name.toLowerCase() === 'index'" ng-click="deletePage()">حذف</button>
                        <button class="btn btn-default btn-lg" ng-click="copyPage()">کپی</button>
                        <button class="btn btn-success btn-lg" ng-click="savePage()">ذخیره</button>
                    </div>
                    <div ng-class="{ hidden: project.activePage }" class="no-page-selected">صفحه ای یافت نشد.</div>
                </section>
            </div>
        </div>

        <div class="flyout-content" ng-class="activePanel == 'export' ? 'open' : 'closed'" ng-controller="ExportController" id="export-panel" bl-render-export-panel>
            <div bl-close-flyout-panel></div>

            <div class="row inner-content" ng-class="{demo: isDemo}">

                <div class="images-column" ng-class="images.length ? 'col-md-2' : 'hidden'" bl-export-images>
                    <h2><span>Images</span></h2>
                    <ul class="list-unstyled">
                        <li ng-repeat="url in images" ng-style="{'background-image': 'url('+url+')'}"></li>
                    </ul>
                </div>
                <div class="html-column" ng-class="images.length ? 'col-md-5' : 'col-md-6'">
                    <h2><span>Html</span> <button id="copy-html" class="btn btn-default">Copy</button></h2>
                    <div id="html-export-preview"></div>
                </div>
                <div class="css-column" ng-class="images.length ? 'col-md-5' : 'col-md-6'">
                    <h2><span>Css</span> <button id="copy-css" class="btn btn-default">Copy</button></h2>
                    <div id="css-export-preview"></div>
                </div>
            </div>

            <div class="row flyout-panel-toolbar" ng-class="{demo: isDemo}">
                <div class="center-block">
                    <select ng-model="activePage.name" ng-change="changeExportPage()">
                        <option value="{{ page.name }}" ng-repeat="page in project.active.pages">{{ page.name }}</option>
                    </select>

                    <button ng-disabled="!activePage" bl-export-page class="btn btn-primary"><i class="fa fa-download"></i> خروجی صفحه</button>
                    <button bl-export-project class="btn btn-success"><i class="fa fa-download"></i> خروجی پروژه</button>
                </div>
            </div>
        </div>
	</div>
	<div id="description-container"></div>
	<aside id="elements-container" ng-controller="ElementsPanelController">
		<section id="elements-panel" bl-el-panel-filterable bl-el-panel-searchable>
			<div class="panel-inner">
				<section id="el-panel-top">
					<h2 class="panel-title clearfix">
						<div class="panel-name open" id="el-panel-name">
							<span class="pull-right">المان ها</span>
							<i bl-toggle-el-search-bar class="fa fa-search pull-left"></i>
						</div>
						<div id="panel-search" class="panel-heading-input">
							<input type="text" class="pull-right rtl" id="el-search" placeholder="جستجوی المان ها" ng-model="query" ng-model-options="{ debounce: 300 }">
							<span bl-toggle-el-search-bar class="fa-stack fa-lg pull-left">
							  <i class="fa fa-circle fa-stack-2x"></i>
							  <i class="fa fa-times fa-stack-1x fa-inverse"></i>
							</span>
						</div>
					</h2>
					<div id="el-preview-container" bl-element-preview>
						<iframe frameborder="0"></iframe>
					</div>
				</section>
				<div id="el-panel-bot" bl-pretty-scrollbar bl-panels-collapsable>
					<div id="elements-list">
						<div class="elements-box" id="layout">
							<h3 class="bl-panel-heading">layout <i class="fa fa-minus"></i></h3>
							<ul class="list-unstyled"></ul>
						</div>
						<div class="elements-box" id="media">
							<h3 class="bl-panel-heading">media <i class="fa fa-minus"></i></h3>
							<ul class="list-unstyled"></ul>
						</div>
						<div class="elements-box" id="typography">
							<h3 class="bl-panel-heading">typography <i class="fa fa-minus"></i></h3>
							<ul class="list-unstyled"></ul>
						</div>
						<div class="elements-box" id="buttons">
							<h3 class="bl-panel-heading">buttons <i class="fa fa-minus"></i></h3>
							<ul class="list-unstyled"></ul>
						</div>
						<div class="elements-box" id="components">
							<h3 class="bl-panel-heading">components <i class="fa fa-minus"></i></h3>
							<ul class="list-unstyled"></ul>
						</div>
						<div class="elements-box" id="forms">
							<h3 class="bl-panel-heading">forms <i class="fa fa-minus"></i></h3>
							<ul class="list-unstyled"></ul>
						</div>
					</div>
				</div>
			</div>
		</section>
        <div class="sidebar-toggler left" bl-toggle-sidebar> <div class="toggler-carret"></div></div>
	</aside>
	<div id="middle">
		<section id="breadcrumbs">
			<ol class="breadcrumb">
                <li ng-repeat="el in selected.path" ng-class="{ active: $last }" ng-click="selectNode($index)"><a href="">{{ el.name }}</a></li>
			</ol>

            <div id="context-menu" ng-controller="ContextMenuController" ng-show="contextMenuOpen">
                <h5 class="clearfix"><span class="name">{{ selected.element ? selected.element.name : '' }}</span><i ng-click="closeContextMenu()" class="fa fa-times"></i> </h5>
                <ul class="list-unstyled">
                    <li ng-if="selected.isTable" ng-click="executeCommand('addRowBefore')"><div class="command-name">addRowBefore</div></li>
                    <li ng-if="selected.isTable" ng-click="executeCommand('addRowAfter')"><div class="command-name">addRowAfter</div></li>
                    <li ng-if="selected.isTable" ng-click="executeCommand('addColumnBefore')"><div class="command-name">addColumnBefore</div></li>
                    <li ng-if="selected.isTable" ng-click="executeCommand('addColumnAfter')"><div class="command-name">AddColumnAfter</div></li>
                    <li ng-if="selected.isTable" class="separator"></li>
                    <li ng-click="executeCommand('selectParent')"><div class="command-name"><i class="fa fa-cut"></i> SelectParent</div></li>
                    <li ng-click="executeCommand('wrapInTransparentDiv')"><div class="command-name"><i class="fa fa-bullseye"></i> WrapInTransparentDiv</div></li>
                    <li class="separator"></li>
                    <li ng-click="executeCommand('cut')"><div class="command-name"><i class="fa fa-cut"></i> cut</div><div class="command-keybind">Ctrl+X</div></li>
                    <li ng-click="executeCommand('copy')"><div class="command-name"><i class="fa fa-copy"></i>Copy</div><div class="command-keybind">Ctrl+C</div></li>
                    <li ng-click="executeCommand('paste', $event)" ng-class="!dom.copiedNode ? 'disabled' : ''"><div class="command-name"><i class="fa fa-paste"></i> paste</div><div class="command-keybind">Ctrl+V</div></li>
                    <li ng-click="executeCommand('delete')"><div class="command-name"><i class="fa fa-trash-o"></i> delete</div><div class="command-keybind">Del</div></li>
                    <li ng-click="executeCommand('clone')"><div class="command-name"><i class="fa fa-database"></i> duplicate</div></li>
                    <li class="separator"></li>
                    <li ng-click="executeCommand('moveSelected', 'up')"><div class="command-name"><i class="fa fa-chevron-up"></i> moveUp</div><div class="command-keybind">&#8593;</div></li>
                    <li ng-click="executeCommand('moveSelected', 'down')"><div class="command-name"><i class="fa fa-chevron-down"></i> moveDown</div><div class="command-keybind">&#8595;</div></li>
                    <li class="separator"></li>
                    <li ng-click="executeCommand('undo')" ng-class="!undoManager.canUndo ? 'disabled' : ''"><div class="command-name"><i class="fa fa-undo"></i> undo</div><div class="command-keybind">Ctrl+Z</div></li>
                    <li ng-click="executeCommand('redo')" ng-class="!undoManager.canRedo ? 'disabled' : ''"><div class="command-name"><i class="fa fa-repeat"></i> redo</div><div class="command-keybind">Ctrl+Z</div></li>
                    <li ng-class="{hidden: codeEditors.currentlyOpen == 'html'}" class="separator"></li>
                    <li ng-class="{hidden: codeEditors.currentlyOpen == 'html'}" ng-click="executeCommand('viewSource')"><div class="command-name">viewSource</div></li>
                </ul>
            </div>
		</section>
<!--        bl-resizable bl-iframe-nodes-sortable bl-iframe-nodes-selectable bl-iframe-text-editable bl-iframe-themes-switchable bl-iframe-context-menu-->
		<div id="frame-wrapper" bl-builder bl-resizable bl-iframe-nodes-sortable bl-iframe-nodes-selectable bl-iframe-text-editable bl-iframe-context-menu> <!-- bl-iframe-context-menu> -->
			<section id="highlights">
                <div id="linker" class="hidden" ng-controller="LinkerController">
                    <h3>linkFor <span>{{ linker.label }}</span> <i ng-click="hideLinker()" class="fa pull-right fa-times"></i></h3>
                    <ul>
                        <li ng-class="{ disabled: linker.radio !== 'url' }">
                            <label class="pull-left">
                                <div class="radio-input"><input value="url" ng-model="linker.radio" type="radio"></div>
                            </label>
                            <div class="pull-right">
                                <div class="title">websiteUrl</div>
                                <div class="body">
                                    <input class="form-control" ng-model="linker.url" ng-change="applyUrl()" type="text" placeholder="http://www.google.com">
                                </div>
                            </div>
                        </li>
                        <li ng-class="{ disabled: linker.radio !== 'page' }">
                            <label class="pull-left">
                                <div class="radio-input"><input value="page" ng-model="linker.radio" type="radio"></div>
                            </label>
                            <div class="pull-right">
                                <div class="title">page</div>
                                <div class="body">
                                    <select ng-model="linker.page" ng-change="applyPage()" class="form-control">
                                        <option value="">selectAPage</option>
                                        <option value="{{page.name}}" ng-repeat="page in project.active.pages">{{ page.name.ucFirst() }}</option>
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li ng-class="{ disabled: linker.radio !== 'download' }">
                            <label class="pull-left">
                                <div class="radio-input"><input value="download" ng-model="linker.radio" type="radio"></div>
                            </label>
                            <div class="pull-right">
                                <div class="title">download</div>
                                <div class="body">
                                    <input class="form-control" ng-model="linker.download" ng-change="applyDownload()" type="text" placeholder="https://www.google.com/images/srpr/logo11w.png">
                                </div>
                            </div>
                        </li>
                        <li ng-class="{ disabled: linker.radio !== 'email' }">
                            <label class="pull-left">
                                <div class="radio-input"><input value="email" ng-model="linker.radio" type="radio"></div>
                            </label>
                            <div class="pull-right">
                                <div class="title">emailAddress</div>
                                <div class="body">
                                    <input class="form-control" ng-model="linker.email" ng-change="applyEmail()" type="text" placeholder="vebtolabs@gmail.com">
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div bl-hover-box></div>
                <div id="select-box" ng-hide="dragging" bl-columns-resizable>
                    <div id="select-box-actions" bl-context-box-actions ng-hide="rowEditorOpen">
                        <span class="element-tag"></span>
                        <i class="fa fa-pencil" data-action="edit"></i>
                        <i class="fa" ng-class="selected.locked ? 'fa-lock' : 'fa-unlock'" bl-toggle-element-lock data-action="lock"></i>
                        <i class="fa fa-trash-o" data-action="delete"></i>
                    </div>
                    <div id="column-resizers"></div>
                    <div id="resize-handles" ng-show="!selected.isColumn && settings.get('showWidthAndHeightHandles') && !rowEditorOpen">
                        <span data-direction="nw" class="drag-handle nw-handle"></span>
                        <span data-direction="n" class="drag-handle n-handle"></span>
                        <span data-direction="ne" class="drag-handle ne-handle"></span>
                        <span data-direction="e" class="drag-handle e-handle"></span>
                        <span data-direction="se" class="drag-handle se-handle"></span>
                        <span data-direction="s" class="drag-handle s-handle"></span>
                        <span data-direction="sw" class="drag-handle sw-handle"></span>
                        <span data-direction="w" class="drag-handle w-handle"></span>
                    </div>
                </div>
                <div id="edit-columns" bl-toggle-row-editor><i class="fa fa-gear"></i> <span>editColumns</span></div>
                <div id="row-editor" bl-row-editor>
                    <div class="column-controls"></div>
                    <div class="row-presets clearfix">
                        <button class="btn btn-sm btn-default equalize-columns pull-left">makeColsEqual</button>
                        <div class="pull-right" bl-row-presets></div>
                    </div>
                    <div class="column-controls-footer clearfix">
                        <i class="fa fa-times close-row-editor"></i>
                        <button class="btn btn-sm btn-primary pull-right save-and-close-row-editor">saveAndClose</button>
                    </div>
                </div>
				<div id="text-toolbar" ng-controller="ToolbarController" bl-floating-toolbar>

                    <select id="toolbar-size" bl-pretty-select="font.size" data-width="120">
                        <option value="">Size</option>
                        <option ng-repeat="num in fontSizes" data-font-size="{{ num }}">{{ num }}</option>
                    </select>
                    <select id="toolbar-font" bl-pretty-select="font.family" data-width="150">
                        <option value="">Font</option>
                        <option ng-repeat="font in baseFonts" data-font-family="{{ font.css }}">{{ font.name }}</option>
                    </select>
					<div id="toolbar-style">
						<div class="bold">B</div>
						<div class="italic">I</div>
						<div class="underline">U</div>
						<div class="strike">S</div>

						<div class="wrap-link"><i class="fa fa-link"></i></div>

						<div class="align-left"><i class="fa fa-align-left"></i> </div>
						<div class="align-center"><i class="fa fa-align-center"></i> </div>
						<div class="align-right"><i class="fa fa-align-right"></i> </div>

						<div class="show-icons-list" bl-toggle-icon-list><i class="fa fa-smile-o"></i> </div>
						<section id="icons-list">
							<div class="arrow-up"></div>
							<input type="text" class="rtl" placeholder="جستجوی آیکن" ng-model="iconSearch">
							<ul class="list-unstyled list-inline">
                                <li ng-repeat="icon in icons | filter:iconSearch" class="icon" data-icon-class="{{ icon }}"><i class="{{ icon }}"></i></li>
							</ul>
						</section>
					</div>
                    <div id="link-details" class="hidden">
                        <input type="text" class="form-control" ng-model="href" placeholder="Url...">
                        <input type="text" class="form-control" ng-model="title" placeholder="Title...">
                        <button type="button" class="btn btn-success" id="wrap-with-link" ng-disabled="href == 'http://'">Go</button>
                    </div>
				</div>
			</section>
			<iframe id="iframe" frameborder="0" class="full-width"></iframe>
			<div id="frame-overlay" class="hidden"></div>
			<div id="theme-loading" class="hidden"><span>در حال بار گذاری.</span></div>
			<div id="code-editor-wrapper" ng-controller="CodeEditorController" bl-render-editors>
				<div id="editor-header" class="clearfix">
					<div class="pull-left">
						<select class="pretty-select" ng-model="editors.theme" ng-options="name for name in themes" ng-change="editors.changeTheme()"></select>
					</div>
					<div class="pull-right" id="editor-icons">
						<button class="btn btn-success btn-sm" type="button" bl-show-editor="html">html</button>
						<button class="btn btn-success btn-sm" type="button" bl-show-editor="css">css</button>
						<button class="btn btn-success btn-sm" type="button" bl-show-editor="js">js</button>
						<button class="btn btn-sm with-icon expand-editor"><i class="fa fa-expand"></i> </button>
						<button class="btn btn-sm with-icon close-editor"><i class="fa fa-times"></i> </button>
					</div>
				</div>
				<div class="code-editor" id="html-code-editor"></div>
				<div class="code-editor hidden" id="css-code-editor"></div>
				<div class="code-editor hidden clearfix" id="js-code-editor">
					<div class="col-sm-10 editor-column" id="jscript-code-editor"></div>
					<div class="col-sm-2 libraries-column rtl">
						<div class="body" bl-js-libraries>
							<h4 class="clearfix"><i class="fa fa-plus pull-left new-library"></i> <span>کتابخانه ها</span></h4>

							<input type="text" class="form-control" placeholder="جستجو..." ng-model="libraryQuery">
							<div class="checkbox">
                                <label class="rtl">
                                    <input type="checkbox" ng-model="onlyShowAttachedLibs"> فقط پیوست شده ها
                                </label>
                            </div>

                            <ul class="libraries-list">
                                <li ng-class="{ attached: libraries.isAttached(lib) }"
                                    data-name="{{ lib.name }}"
                                    ng-if="! onlyShowAttachedLibs || libraries.isAttached(lib)"
                                    ng-repeat="lib in libraries.all | filter:libraryQuery">
                                    <i class="fa fa-link"></i>
                                    <span class="name">{{ lib.name }}</span>
                                    <div class="lib-actions">
                                        <i title="ویرایش کتابخانه" class="fa fa-gears edit-library"></i>
                                        <i title="حذف کتابخانه" class="fa fa-trash-o delete-library"></i>
                                    </div>
                                </li>
                            </ul>

						</div>
					</div>

					<div class="modal fade rtl" id="new-library-modal" bl-new-library-modal>
						<div class="modal-dialog">
					    	<div class="modal-content">
					      		<div class="modal-header">
					        		<button type="button" class="close pull-left" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					        		<h4 class="modal-titlet">ایجاد کتابخانه جدید</h4>
					      		</div>
					      		<div class="modal-body">
					        		<div class="form-group">
					        			<label for="name">نام کتابخانه</label>
					        			<input type="text" class="form-control ltr" name="name" ng-model="libraries.form.name">
					        		</div>
					        		<div class="form-group">
					        			<label for="path">آدرس کتابخانه</label>
					        			<input type="text" class="form-control ltr" name="path" ng-model="libraries.form.path">
					        			<p class="help-block">لینک به فایل کتابخانه، آدرس کتابخانه در اینترنت.</p>
					        		</div>
					        		<div class="text-danger error" id="showErrors">
                                        <p ng-repeat="errorMessege in libraries.form.error">{{errorMessege[0]}}</p>
                                    </div>
					      		</div>
					      		<div class="modal-footer">
					      			<button class="btn btn-danger close-modal">بستن</button>
					      			<button class="btn btn-success pull-left save-library">ذخیره و بستن</button>
					      		</div>
					    	</div>
					  	</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<aside id="inspector" ng-controller="InspectorController" bl-color-picker >
		<div id="inspector-overlay" ng-show="!selected.node"><span class="rtl">برای ویرایش استایل، لطفا المان را انتخاب کنید.</span></div>
		<section id="inspector-inner" bl-pretty-scrollbar bl-panels-collapsable>
			<div bl-inspector-header>
				<h2 class="panel-title clearfix">
					<div class="panel-name open">
                        <span class="pull-left">{{ selected.element.name.ucFirst() }}</span>
						<i class="fa fa-filter pull-right" data-toggle="tooltip" data-placement="left" title="Filter Categories"></i>
						<i class="fa fa-css3 pull-right" data-toggle="tooltip" data-placement="left" title="Edit CSS Selector"></i>
					</div>
                    <div class="panel-heading-input">
                        <input type="text" class="pull-left" id="css-selector" ng-model="selected.selector" placeholder="Search Elements..." ng-model-options="{ debounce: 300 }">
						<span class="fa-stack fa-lg pull-right">
						  <i class="fa fa-circle fa-stack-2x"></i>
						  <i class="fa fa-times fa-stack-1x fa-inverse"></i>
						</span>
                    </div>
				</h2>

                <select id="inspector-filter-select" class="form-control hidden" ng-model="filter.query" ng-options="name for name in categories"></select>
            </div>

            <div ng-show="canEdit('attributes')" ng-controller="AttributesController" class="inspector-panel" id="attributes-panel">

                <div id="visibility">
                    <ul class="list-unstyled list-inline" bl-element-visibility-controls>
                        <li data-size="xs" data-toggle="tooltip" data-placement="top" title="visible on mobile"><i class="fa fa-fw fa-mobile-phone"></i></li>
                        <li data-size="sm" data-toggle="tooltip" data-placement="top" title="visible on tablet"><i class="fa fa-fw fa-tablet"></i></li>
                        <li data-size="md" data-toggle="tooltip" data-placement="top" title="visible on laptop"><i class="fa fa-fw fa-laptop"></i></li>
                        <li data-size="lg" data-toggle="tooltip" data-placement="top" title="visible on desktop"><i class="fa fa-fw fa-desktop"></i></li>
                    </ul>
                </div>

				<h4 class="bl-panel-heading">attributes <i class="fa fa-minus"></i></h4>
				<div class="panel-box">
					<div id="custom-attributes">
                        <div class="form-group" ng-repeat="(name, config) in customAttributes">
                            <label for="el-{{ name }}">{{ name }}</label>

                            <!-- Render custom text input option -->
                            <input ng-class="name.length > 7 ? 'long-name' : ''" ng-if="config.text" type="text" id="el-{{ name }}" ng-model="config.value" ng-model-options="{ debounce: 300 }">
                            <!-- Render custom select input option -->
                            <select ng-if="config.list"id="el-{{ name }}" class="pretty-select" ng-model="config.value" ng-options="item.name for item in config.list"></select>
                            <!-- /end custom options -->
                        </div>
					</div>

                    <div class="form-group" ng-show="canEdit('float')">
                        <label for="el-id">float</label>
                        <select id="el-float" class="pretty-select" ng-model="attributes.float">
                            <option value="none">none</option>
                            <option value="pull-left">left</option>
                            <option value="center-block">center</option>
                            <option value="pull-right">right</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="el-id">id</label>
                        <input class="pull-right" type="text" id="el-id" ng-model="attributes.id" ng-model-options="{ debounce: 300 }">
                    </div>

                    <div class="clearfix">
                        <label for="el-id">class</label>
                        <div id="el-class" bl-add-class-panel>
                            <ul class="list-unstyled list-inline">
                                <li class="label" ng-repeat="class in attributes.class" ng-if="class">{{ class }} <i class="fa fa-times" ng-click="removeClass(class); $event.stopPropagation();"></i></li>
                            </ul>
                            <div id="addclass-flyout" class="hidden">
                                <input type="text" id="addclass-input" ng-model="classInput">
                                <button class="btn btn-sm btn-success add-class"> <i class="fa fa-check"></i></button>
                            </div>
                        </div>
                    </div>

                    <button ng-if="selected.isImage" type="button" open-media-manager="src" class="btn btn-primary btn-block">mediaManager</button>

                </div>
			</div>

            <section id="background-panel" class="inspector-panel" ng-controller="BackgroundController" ng-show="canEdit('background')">
                <h4 class="bl-panel-heading">background <i class="fa fa-minus"></i></h4>

                <div class="panel-box">
                    <div data-controls="properties.color" class="color-picker-trigger" id="fill-color"><div class="background-box"><i class="fa fa-paint-brush"></i></div><div class="background-name">color</div></div>
                    <div bl-show-img-container id="image"><div class="background-box"><i class="fa fa-image"></i></div><div class="background-name">image</div></div>
                    <div bl-show-img-container id="gradient"><div class="background-box"><i class="fa fa-eyedropper"></i></div><div class="background-name">gradient</div></div>
                </div>

                <div id="background-flyout-panel" class="hidden">
                    <div class="bl-panel-header clearfix"><div class="name">background</div><div class="bl-panel-btns" ng-click="closePanel"><i class="fa fa-times"></i></div></div>
                    <button type="button" open-media-manager="background" class="btn btn-primary btn-block">mediaManager</button>
                    <div id="texturePresets">
                        <h5>textures</h5>
                        <ul class="img-presets-list" bl-pretty-scrollbar>
                            <li ng-repeat="texture in textures track by $index">
                                <div ng-click="selectPreset($event)" class="preset" ng-style="{ 'background-image': 'url(images/textures/'+$index+'.png)' }">
                            </li>
                        </ul>
                        <div id="image-properties">
                            <h5>imageProperties</h5>
                            <div id="img-positioning" class="clearfix">
                                <div class="pull-left">
                                    <h6>repeat</h6>
                                    <ul class="list-unstyled">
                                        <li><div class="radio"><label><input value="no-repeat" ng-checked="properties.repeat == 'no-repeat'" ng-model="properties.repeat" type="radio">none</label></div></li>
                                        <li><div class="radio"><label><input value="repeat-x" ng-checked="properties.repeat == 'repeat-x'" ng-model="properties.repeat" type="radio">horizontal</label></div></li>
                                        <li><div class="radio"><label><input value="repeat-y" ng-checked="properties.repeat == 'repeat-y'" ng-model="properties.repeat" type="radio">vertical</label></div></li>
                                        <li><div class="radio"><label><input value="repeat" ng-checked="properties.repeat == 'repeat'" ng-model="properties.repeat" type="radio">all</label></div></li>
                                    </ul>
                                </div>
                                <div class="pull-right">
                                    <h6>alignment</h6>
                                    <div id="alignment" bl-img-alignment-grid></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="gradientPresets">
                        <h5>gradients</h5>
                        <ul class="img-presets-list" bl-pretty-scrollbar>
                            <li ng-repeat="gradient in gradients track by $index">
                                <div class="preset" ng-click="selectPreset($event)" ng-style="{ 'background-image': gradient }"></div>
                            </li>
                        </ul>
                    </div>

                </div>
            </section>

            <section id="shadows-panel" class="inspector-panel" ng-controller="ShadowsController">
                <div ng-show="canEdit('shadows')">
                    <h4 class="bl-panel-heading">shadows <i class="fa fa-minus"></i></h4>
                    <div class="panel-box clearfix">
                        <div id="shadow-knob-container">
                            <input type="text" value="0" bl-knob>
                            <div data-toggle="tooltip" data-placement="top" title="Shadow Color" data-controls="props.color" class="color-picker-trigger" bl-shadow-color-preview></div>
                        </div>
                        <div class="slider-group">
                            <div class="range-slider">
                                <div class="slider-label">distance</div>
                                <div bl-range-slider="props.distance" max="20"></div>
                            </div>
                            <input type="text" ng-model="props.distance" class="pretty-input">
                        </div>
                        <div class="slider-group">
                            <div class="range-slider">
                                <div class="slider-label">blur</div>
                                <div bl-range-slider="props.blur" max="20"></div>
                            </div>
                            <input type="text" ng-model="props.blur" class="pretty-input">
                        </div>
                        <div class="slider-group" ng-if="props.type == 'boxShadow'">
                            <div class="range-slider">
                                <div class="slider-label">spread</div>
                                <div bl-range-slider="props.spread" max="20"></div>
                            </div>
                            <input type="text" ng-model="props.spread" class="pretty-input">
                        </div>
                        <select bl-pretty-select="props.type" data-width="100%">
                            <option value="boxShadow">box</option>
                            <option value="textShadow">text</option>
                        </select>
                        <select ng-if="props.type == 'boxShadow'" bl-pretty-select="props.inset" data-width="100%">
                            <option value="">outter</option>
                            <option value="inset">inner</option>
                        </select>
                    </div>
                </div>
            </section>

            <section ng-controller="MarginPaddingController">
                <div id="padding-panel" class="inspector-panel" ng-show="canEdit('padding')">
                    <h4 class="bl-panel-heading">padding <i class="fa fa-minus"></i></h4>
                    <div class="clearfix panel-box" >
                        <div class="checkboxes clearfix" bl-checkboxes="padding"></div>
                        <div bl-range-slider="padding"></div>
                        <div bl-input-boxes="padding" class="clearfix input-boxes"></div>
                    </div>
                </div>

                <div id="margin-panel" class="inspector-panel" ng-show="canEdit('margin')">
                    <h4 class="bl-panel-heading">margin <i class="fa fa-minus"></i></h4>
                    <div class="panel-box clearfix" >
                        <section class="checkboxes clearfix" bl-checkboxes="margin"></section>
                        <div bl-range-slider="margin"></div>
                        <div bl-input-boxes="margin" class="clearfix input-boxes"></div>
                    </div>
                </div>
            </section>

            <!-- text style box starts -->
            <section ng-show="canEdit('text')" class="inspector-panel" id="text-panel" ng-controller="TextController">
                <h4 class="bl-panel-heading">textStyle <i class="fa fa-minus"></i></h4>
                <div id="text-box" class="clearfix panel-box">
                    <div class="clearfix">
                        <select id="el-font-family" bl-pretty-select="inspector.styles.text.fontFamily" data-width="149" class="pull-left">
                            <option value="">font</option>
                            <option ng-repeat="font in textStyles.baseFonts" data-font-family="{{ font.css }}" value="{{ font.css }}">{{ font.name }}</option>
                        </select>
<!--                        <div id="more-fonts" class="pull-right" data-toggle="modal" data-target="#fonts-modal"><i class="fa fa-google"></i></div>-->
                    </div>

                    <div class="clearfix">
                        <div class="icon-box italic" bl-toggle-text-style="fontStyle|italic">I</div>
                        <div class="icon-box underline" bl-toggle-text-decoration="underline" ng-class="inspector.styles.text.textDecoration.indexOf('underline') > -1 ? 'active' : ''">U</div>
                        <div class="icon-box strike" bl-toggle-text-decoration="line-through" ng-class="inspector.styles.text.textDecoration.indexOf('line-through') > -1 ? 'active' : ''">S</div>
                        <div class="icon-box overline" bl-toggle-text-decoration="overline" ng-class="inspector.styles.text.textDecoration.indexOf('overline') > -1 ? 'active' : ''">O</div>
                        <select id="el-font-weight" class="form-control" bl-pretty-select="inspector.styles.text.fontWeight" data-width="65">
                            <option ng-repeat="weight in textStyles.fontWeights" data-font-weight="{{ weight }}" value="{{ weight }}">{{ weight }}</option>
                        </select>
                    </div>

                    <div id="el-font-style-box" class="clearfix">
                        <div class="pull-left">
                            <input type="text" id="el-font-size" ng-model="inspector.styles.text.fontSize" ng-model-options="{ debounce: 300 }" class="form-control pull-left">

                            <div class="pull-right">
                                <div class="icon-box" bl-toggle-text-style="textAlign|left"><i class="fa fa-align-left"></i> </div>
                                <div class="icon-box" bl-toggle-text-style="textAlign|center"><i class="fa fa-align-center"></i> </div>
                                <div class="icon-box" bl-toggle-text-style="textAlign|right"><i class="fa fa-align-right"></i> </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix">
                        <div data-controls="inspector.styles.text.color" class="color-picker-trigger" style="background: {{ inspector.styles.text.color }}"></div>
                    </div>
                </div>
            </section>
			<!-- text style box ends -->

			<!-- border box starts -->
            <section id="border-box" ng-show="canEdit('box')" ng-controller="BorderController">

                <div id="border-panel" class="inspector-panel">
                    <h4 class="bl-panel-heading">border <i class="fa fa-minus"></i></h4>
                    <div class="panel-box">
                        <section class="checkboxes clearfix" bl-checkboxes="borderWidth"></section>
                        <div bl-range-slider="borderWidth" max="20"></div>
                        <div class="clearfix">
                            <div data-controls="inspector.styles.border.color" class="color-picker-trigger" bl-border-color-preview></div>
                            <select id="border-style" ng-model="borderStyle">
                                <option value="none">none</option>
                                <option value="solid">solid</option>
                                <option value="dashed">dashed</option>
                                <option value="dotted">dotted</option>
                                <option value="double">double</option>
                                <option value="groove">groove</option>
                                <option value="ridge">ridge</option>
                                <option value="inset">inset</option>
                                <option value="outset">outset</option>
                            </select>
                        </div>
                        <div bl-input-boxes="border.width" class="clearfix input-boxes"></div>
                    </div>
                </div>

                <div id="border-radius-panel" class="inspector-panel">
                    <h4 class="bl-panel-heading">borderRoundness <i class="fa fa-minus"></i></h4>
                    <div id="borderRadius-box" class="panel-box clearfix">

                        <section class="checkboxes clearfix">
                            <div class="pretty-checkbox pull-left">
                                <input type="checkbox" id="borderRadius.all" ng-click="inspector.toggleStyleDirections('borderRadius', 'all')">
                                <label for="borderRadius.all"><span class="ch-all"></span><span class="unch-all"></span></label>
                            </div>
                            <div class="pull-right">
                                <div class="pretty-checkbox">
                                    <input type="checkbox" id="borderRadius.top" ng-click="inspector.toggleStyleDirections('borderRadius', 'topLeft')" ng-checked="inspector.checkboxes.borderRadius.indexOf('topLeft') !== -1">
                                    <label for="borderRadius.top"><span class="ch-top border-top-left"></span><span class="unch-top border-top-left"></span></label>
                                </div>
                                <div class="pretty-checkbox">
                                    <input type="checkbox" id="borderRadius.bottom" ng-click="inspector.toggleStyleDirections('borderRadius', 'bottomLeft')" ng-checked="inspector.checkboxes.borderRadius.indexOf('bottomLeft') !== -1">
                                    <label for="borderRadius.bottom"><span class="ch-bottom border-bottom-left"></span><span class="unch-bottom border-bottom-left"></span></label>
                                </div>
                                <div class="pretty-checkbox">
                                    <input type="checkbox" id="borderRadius.right" ng-click="inspector.toggleStyleDirections('borderRadius', 'topRight')" ng-checked="inspector.checkboxes.borderRadius.indexOf('topRight') !== -1">
                                    <label for="borderRadius.right"><span class="ch-right border-top-right"></span><span class="unch-right border-top-right"></span></label>
                                </div>
                                <div class="pretty-checkbox">
                                    <input type="checkbox" id="borderRadius.left" ng-click="inspector.toggleStyleDirections('borderRadius', 'bottomRight')" ng-checked="inspector.checkboxes.borderRadius.indexOf('bottomRight') !== -1">
                                    <label for="borderRadius.left"><span class="ch-left border-bottom-right"></span><span class="unch-left border-bottom-right"></span></label>
                                </div>
                            </div>
                        </section>
                        <div bl-range-slider="borderRadius" max="20"></div>

                        <div class="clearfix input-boxes radius-boxes">
                            <div class="big-box col-sm-6">
                                <input ng-model="radiusAll" ng-model-options="{ debounce: 300 }">
                            </div>
                            <div class="small-boxes col-sm-6">
                                <input ng-model="inspector.styles.border.radius.topLeft" ng-model-options="{ debounce: 300 }">
                                <input ng-model="inspector.styles.border.radius.topRight" ng-model-options="{ debounce: 300 }">
                                <input ng-model="inspector.styles.border.radius.bottomLeft" ng-model-options="{ debounce: 300 }">
                                <input ng-model="inspector.styles.border.radius.bottomRight" ng-model-options="{ debounce: 300 }">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
			<!-- /border box ends -->

			<div class="arrow-right" id="color-picker-arrow"></div>
			<div class="arrow-right" id="background-arrow"></div>
		</section>
		<div class="sidebar-toggler right" bl-toggle-sidebar> <div class="toggler-carret"></div></div>
	</aside> <!-- /inspector -->

    <div class="modal fade" id="fonts-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header clearfix">
                    <div title="Close Modal" class="fa-stack fa-lg close-modal" data-dismiss="modal">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-times fa-stack-1x fa-inverse"></i>
                    </div>

                    <h4 class="modal-title pull-left">selectOneOf {{ fonts.paginator.sourceItems.length }} googleFonts</h4>
                    <div class="pagi-container pull-right"><ul class="pagination" bl-fonts-pagination></ul></div>
                </div>

                <div class="modal-body">
                    <ul class="fonts-list">
                        <li ng-repeat="font in fonts.paginator.currentItems" style="font-family: {{ font.family }}" ng-click="fonts.apply(font)">
                            <div class="font-preview">fontsLorem</div>
                            <div class="font-details clearfix">
                                <div class="pull-left">{{ font.family+', '+font.category }}</div>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

	<div class="modal fade" id="images-modal">
        <div class="modal-dialog">
            <div class="modal-content" ng-controller="MediaManagerController">
                <div class="modal-header clearfix">
                    <button type="button" class="close pull-right" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title pull-left">mediaManager</h4>
                </div>
                <div class="modal-body">

                    <div class="modal-under-header clearfix">
                        <div class="pull-left">
                            <p class="info">uploadAndMangeImages</p>
                            <p class="info">You can upload images up to 20mb in size and JPEG, PNG or GIF formats.</p>
                        </div>
                        <div class="pull-right">
                            <button class="btn btn-primary btn-lg" ng-file-select="onFileSelect($files)" data-multiple="true"><i class="fa fa-upload"></i> uploadImages</button>
                        </div>
                    </div>

                    <ul class="nav nav-tabs" bl-media-manager-tabs>
                        <li ng-class="activeTab == 'my-images' ? 'active' : ''" ng-click="activeTab = 'my-images'"><a href="">myImages</a></li>
                        <li ng-class="activeTab == 'url' ? 'active' : ''" ng-click="activeTab = 'url'"><a href="">imageUrl</a></li>
                    </ul>

                    <div class="tab-content">
                        <div ng-class="activeTab == 'my-images' ? 'active' : ''" class="tab-pane clearfix" id="my-images">
                            <div id="images-filter-bar" class="clearfix">
                                <div class="pull-right">
                                    <div class="checkbox-btn btn btn-gradient-gray" ng-class="{ active: selectAll }">
                                        <input type="checkbox" ng-model="selectAll" value="true">
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" ng-class="{ active: sorting.prop == 'created_at' }" ng-click="changeSorting('created_at')" class="btn btn-gradient-gray btn-sm">date</button>
                                        <button type="button" ng-class="{ active: sorting.prop == 'display_name' }" ng-click="changeSorting('display_name')" class="btn btn-gradient-gray btn-sm">aZ</button>
                                    </div>
                                    <button ng-click="deleteSelectedImages()" class="btn btn-gradient-gray btn-sm"><i class="fa fa-trash"></i> </button>
                                    <input type="text" ng-model="searchQuery">
                                </div>
                            </div>
                            <div class="col-sm-2" id="folders-cont">
                                <div class="row">
                                    <ul class="list-unstyled" bl-image-folder-selectable>

                                        <li ng-repeat="folder in folders" ng-class="(selectedFolder.name == folder.name || folder.creating) ? 'active' : ''"  data-id="{{ folder.id }}" data-name="{{ folder.name }}">
                                            <div ng-if="!folder.creating" class="clearfix">
                                                <span class="pull-left">{{ folder.name }}</span>
                                                <i ng-click="deleteFolder(folder.id);$event.stopPropagation();" ng-if="folder.id" data-toggle="tooltip" data-placement="top" title="delete folder" class="fa fa-trash delete-folder pull-right"></i>
                                            </div>
                                            <div ng-if="folder.creating" class="creating-folder">
                                                <input ng-model="newFolder.name" type="text" name="name" class="form-control">
                                                <button class="btn btn-sm btn-success" ng-click="createFolder()"> <i class="fa fa-check"></i></button>
                                                <button class="btn btn-sm btn-danger" ng-click="cancelFolderCreation()"> <i class="fa fa-times"></i></button>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="add-folder-cont">
                                        <button class="btn btn-primary btn-block" bl-new-image-folder><i class="fa fa-folder-open-o"></i> addFolder</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-10" id="images-cont">
                                <ul class="list-unstyled row" bl-images-selectable>
                                    <li ng-class="{ selected: isSelected(image.id) }"
                                        ng-repeat="image in images | filter: selectedFolder.name == 'All Images' ? '' : selectedFolder.name | filter: searchQuery | orderBy:sorting.prop:sorting.reverse"
                                        class="col-sm-2"
                                        data-id="{{ image.id }}">

                                        <div class="img-wrapper" style="background-image: url({{ baseUrl }}/images/uploads/{{image.file_name}})"></div>
                                        <div class="img-caption">
                                            <span>{{ image.display_name.substring(0,12) }}</span>

                                            <div class="img-actions" bl-image-actions>
                                                <i data-toggle="tooltip" data-placement="top" title="Edit" class="fa fa-fw fa-pencil edit-image"></i>
                                                <i data-toggle="tooltip" data-placement="top" title="Delete" class="fa fa-fw fa-trash-o delete-image"></i>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                                <div ng-show="images.length < 1" id="upload-container" ng-file-drop="onFileSelect($files)">
                                    <img class="img-responsive" src="images/filedrop.png">
                                    <h2>dropImageToUpload</h2>
                                    <div class="separator"><span>or</span></div>
                                    <button class="btn btn-success btn-lg" ng-file-select="onFileSelect($files)" data-multiple="true">selectImageFrom</button>
                                </div>
                            </div>
                        </div>
                        <div ng-class="activeTab == 'url' ? 'active' : ''" class="tab-pane" id="url">
                            <h2>Enter the URL of an image somewhere on the web</h2>
                            <input type="text" ng-model="webImageUrl" class="form-control">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="downloadLocally" value="true"> downloadLocally
                                </label>
                            </div>
                            <p>It must be a direct link to an image file.</p>
                            <p>Example: http://mywebsite.com/image.jpg</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <p class="pull-left" ng-if="activeTab == 'my-images'">useImageExpl</p>
                    <button class="btn btn-primary btn-lg" ng-click="useImage()">useImage</button>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="preview-closer" class="hidden" ng-click="closePreview()">
	<i class="fa fa-times"></i>
</div>