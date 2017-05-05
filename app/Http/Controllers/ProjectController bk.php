<?php namespace App\Http\Controllers;


use App\Http\Requests\ProjectRequest;
use App\Image;
use App\Library;
use App\Page;
use App\Project;
use App\Http\Controllers\Controller;
use App\Template;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Filesystem\Filesystem;

class Project2Controller extends Controller {

    public function store(ProjectRequest $request)
    {

        if (Project::query()->where('name', '=', $request->get('name'))->first())
        {
            $error = ['name' => [trans('validation.projectNameExists')]];

            return new Response(json_encode($error), 400);
        }

        $project = Auth::user()->projects()->create($request->all());

//        $public = $request->has('public') ? (int) $request->get('public') : 0 ;

        if ($request->has('templateId'))
        {
            $template = Template::with('pages.libraries')->find($request->get('templateId'));

            $pages = array();

            foreach ($template->pages as $page) {
                $pages[] = new Page(array_except($page->toArray(), array('id', 'pageable_id', 'pageable_type', 'created_at', 'updated_at')));
            }

            $project->pages()->saveMany($pages);
        }

        $project->pages = $pages;
        $project->pages->libraries = $template->pages->libraries;

        $this->attachLibraries($project);

        return new Response($project, 200);

    }

    /**
     * Attach libraries from given template pages to given projects pages.
     *
     * @param  Project  $project
     * @param  Template $template
     *
     * @return void
     */
    private function attachLibraries(Project $project)
    {
        foreach ($project->pages as $page) {
            if ($page->libraries && $libs = json_decode($page->libraries, true)) {
                $ids = Library::whereIn('name', $libs)->lists('id');

                $page->libraries()->attach($ids);
            }
        }
    }

    public function lib(Request $request, $id)
    {
        $project = Project::find($id);
        foreach ($project->pages as $page) {
            if ($page->libraries && $libs = json_decode($page->libraries, true)) {
                $ids = Library::whereIn('name', $libs)->lists('id');

                $page->libraries()->attach($ids);
            }
        }
    }

    public function saveImage(Request $request,$id)
    {
        Image::create($request->getContent())->save();
        $request->file()->move(public_path('images').'/projects/project-'.$id.'.png', $request->file()->getClientOriginalName());
//        $this->app['imagesSaver']->saveFromString(
//             ,
//            'assets/images/projects/project-'.$id.'.png',
//            false
//        );

        return new Response('images/projects/project-'.$id.'.png', 200);
    }

    /**
     * Update an existing project.
     *
     * @param  sting/int $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
        return new Response($id, 200);
        $project = Project::find($id);

        if(!$project)
            return new Response('error', 400);

        if($request->has('template'))
        {
            $this->deletePages($project->id);

            $project = $this->useTemplate(Project::find($project->id), $input['template']);
        } else {
            $this->updatePages($project);
        }

        return $this->project->with('pages.libraries')->find($project->id)->toArray();

        $project->update($request->all());
        return $project;





//        return new Response($id, 200);
//        $p = Project::update($request->all());
//
//        if ( ! $p) {
//            return new Response('problemUpdatingProject', 500);
//        }
//
//        return new Response(json_encode($p), 200);
    }

    /**
     * Delete projects pages and any attached libraries.
     *
     * @param  int|string $id
     * @return void
     */
    private function deletePages($id)
    {
        $pages = Page::where('pageable_id', '=' , $id)->where('pageable_type','=', 'Project')->get(array('id'));

        foreach ($pages as $page) {
            $page->libraries()->detach();
            $page->delete();
        }
    }

    public function findPage($id){
        $project = Project::find($id);
        return $project->id;
//        return Page::where('pageable_id', '=' , $id)->where('pageable_type','=', 'Project')->get(array('id'));
    }

    public function store_test(Request $request, $name, $template)
    {

//        if (Project::query()->where('name', '=', $name)->first())
//        {
//            $error = ['name' => [trans('validation.projectNameExists')]];
//
//            return new Response(json_encode($error), 400);
//        }

//        $public = $request->has('public') ? (int) $request->get('public') : 0 ;
//        return $name;
//        $project = Auth::user()->projects()->create(['user_id' => 1, 'name' => $name]);
        $project = Auth::user()->projects()->find(2);
        if (!is_null($template))
        {
            $template = Template::with('pages.libraries')->find($template);
//            $project = $this->project->find(9); ->where('pageable_id', '=', $template)
//            $template_pages = Template::find($template)->pages()->get();
            $pages = array();

            foreach ($template->pages as $page) {
//                return $page;
//                $pages[] = new Page(array_except($page->toArray(), array('name', 'html', 'css', 'js', 'theme', 'description', 'tags', 'title', 'libraries')));
                $pages[] = new Page(array_except($page->toArray(), array('id', 'pageable_id', 'pageable_type', 'created_at', 'updated_at')));
            }
//            return $pages[1]->libraries;
//            $project->pages = $pages;
//            return $project->pages[0]->libraries;
//            return $template;
//            return $i;
//            return $template;
//            return $template;
//            $project = Project::create()create();

            $project->pages()->saveMany($pages);

            $this->attachLibraries($project);

            //copy thumbnail from template to project
//            $path = asset($template->thumbnail);
//            $fs = new Filesystem();
//            return $project->id;
//            $fs->copy($path, '/images/projects/project-'.$project->id.'.png', true);

//            return $project->pages()->get();
//            return Project::find(2)->get();

        }
        $project->pages = $pages;
        return $project;
//        return Auth::user()->projects()->find($project->id)->with('pages')->get();
        //return Auth::user()->projects()->create($request->all());
    }

    public function showProject(Request $request, $id){
        $project = Project::with('pages.libraries')->find($id);

        return $project;
    }

}
