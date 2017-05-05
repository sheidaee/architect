<?php namespace App;

use File;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class Export extends Model {


    /**
     * Exports folder path.
     *
     * @var string
     */
    private $exportsPath;

    /**
     * Bootstrap css file path.
     *
     * @var string
     */
    private $bootstrapDir;

    /**
     * Create new Exporter isntance.
     *
     * @param Application $app
     * @param Request     $request
     */
    public function __construct()
    {
        $this->exportsPath = public_path('storage/exports/');
//        $this->bootstrapDir = $app['base_dir'].'/themes/default/stylesheet.css';

        $this->fs = new Filesystem;

//        $this->url = $this->app['base_url'];
    }

    /**
     * Create export zip file for given page.
     *
     * @param  string/int $id
     * @return boolean/string
     */
    public function page($id)
    {
        $page = page::find($id);

        //fix eloquent morphTo issue with namespaced models
        if ($page) {
            $page->pageable_type = 'App\Project';
        }

        //basic access check
//        || ! $page->pageable->users()->where('users.id', \Auth::user()->id)->first()
        if ( ! $page ) {
            return false;
        }

        if ($path = $this->createFolders($page->pageable)) {

            //create a fake project with given page so we can
            //reuse same methods as when exporting a project
            $project = new \stdClass();
            $project->pages = array($page);

//            return $project->pages;

//            return $this->createFiles($path, $project);
            $this->createFiles($path, $project);

            return $this->zip($path, $page->name);
        }
    }

    /**
     * Create a folder structure to hold export files.
     *
     * @param ProjectModel $project
     * @return string
     */
    private function createFolders(Project $project)
    {
        $name = $project->id.'-'.$project->name;

        if (is_dir($this->exportsPath.$name)) {
            return $this->exportsPath.$name.'/';
        }

        if (@mkdir($this->exportsPath.$name)) {
            @mkdir($this->exportsPath.$name.'/css');
            @mkdir($this->exportsPath.$name.'/js');
            @mkdir($this->exportsPath.$name.'/images');
            @mkdir($this->exportsPath.$name.'/mail');

            return $this->exportsPath.$name.'/';
        }
    }

    /**
     * Create css and html files from pages in given project.
     *
     * @param  string $path
     * @param  Object $project
     *
     * @return void
     */
    public function createFiles($path, $project)
    {
        //create mail file
        $mail = @file_get_contents('templates/contact_me.php');
        file_put_contents($path.'mail/contact_me.php', $mail);

        $cssPaths = array();

        //get a list of custom elements
        $elems = scandir('elements');

        //create html, css and js files for each page in the project
        foreach ($project->pages as $key => $page) {

            //create a file with user custom css
            if ($page->css) {
                @unlink($path."css/{$page->name}-stylesheet.css");

                $css = $this->handleImages($page->css, $path, 'css');

                if (@file_put_contents($path."css/{$page->name}-stylesheet.css", $css)) {
                    $cssPaths[$page->name] = "css/{$page->name}-stylesheet.css";
                }
            }

            //create a file with user custom js
            if ($page->js) {
                $scripts = $this->handleLibraries($page->libraries, $path);

                if (@file_put_contents($path."js/{$page->name}-scripts.js", $page->js)) {
                    $jsPaths[$page->name] = "js/{$page->name}-scripts.js";
                }
            }

            //create html files
            //TODO: use DOM instead of regex
            if ($page->html) {

                $cssPaths = $this->handleCustomElementsCss($elems, $cssPaths, $page, $path);

                //bootstrap
                $page->html = preg_replace('/<link id="main-sheet".+?>/', '', $page->html);
                $page->html = preg_replace('/<link.+?font-awesome.+?>/', '', $page->html);

                $theme = strtolower($page->theme ? $page->theme : 'default');

                $bs = @file_get_contents('themes/'.$theme.'/stylesheet.css');
                @file_put_contents($path.'css/bootstrap.css', $bs);

                $page->html = preg_replace('/(<\/head>)/ms', "\n\t<link rel=\"stylesheet\" href=\"css/bootstrap.css\">\n$1", $page->html);

                //font-awesome
//                $page->html = preg_replace('/(<\/head>)/ms', "\n\t<link rel=\"stylesheet\" href=\"//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css\">\n$1", $page->html);
                $page->html = preg_replace('/(<\/head>)/ms', "\n\t<link rel=\"stylesheet\" href=\"css/font-awesome.css\">\n$1", $page->html);

                if (isset($cssPaths[$page->name])) {
                    $page->html = preg_replace('/(<\/head>)/ms', "\n\t<link rel=\"stylesheet\" href=\"".$cssPaths[$page->name]."\">\n$1", $page->html);
                }

                if (isset($jsPaths[$page->name])) {
                    $page->html = preg_replace('/(<\/body>)/ms', "<script id=\"main-script\" src=\"".$jsPaths[$page->name]."\"></script>\n$1", $page->html);
                }

                if (isset($scripts) && $scripts) {
                    $page->html = preg_replace('/(<script id="main-script".+?<\/script>)/ms', "$scripts$1", $page->html);
                }

                $page->html = $this->handleImages($page->html, $path, 'html');

                $page->html = $this->handleMeta($page->html, $page);
                $page->html = preg_replace('/(<base.+?>)/ms', '', $page->html);

                file_put_contents($path."{$page->name}.html", $page->html);
            }
        }
    }

    /**
     * Copy any local images used in html/css to export folder.
     *
     * @param  string $string
     * @param  string $path
     * @param  string $type
     *
     * @return string
     */
    private function handleImages($string, $path, $type)
    {
        preg_match_all('/url\((.+?)\)/ms', $string, $matches1);
        preg_match_all('/<img.*?src="(.+?)".*?>/ms', $string, $matches2);

        $matches = array_merge($matches1[1], $matches2[1]);

        //include any local images used in css or html in the zip
        if ( ! empty($matches)) {
            foreach ($matches as $url) {

                if (str_contains($url, url()) || ! str_contains($url, '//')) {
                    $absolute = $this->relativeToAbsolute($url);

                    try {
                        @$this->fs->copy($absolute, $path.'images/'.basename($absolute));
                    } catch (\Exception $e) {
                        continue;
                    }
                    if ($type == 'css') {
                        $string = str_replace($url, '../images/'.basename($absolute), $string);
                    } else {
                        $string = str_replace($url, 'images/'.basename($absolute), $string);
                    }

                }
            }
        }

        return $string;
    }

    /**
     * Convert local relative path to absolute one.
     *
     * @param  string $path
     * @return string
     */
    private function relativeToAbsolute($path)
    {
        if (str_contains($path, '//')) {
            return $path;
        }

        $path = str_replace('"', "", $path);
        $path = str_replace("'", "", $path);
        $path = str_replace("../", "", $path);

        return url().'/'.$path;
    }

    /**
     * Add meta tags to head html.
     *
     * @param  string $html
     * @param  PageModel $page
     *
     * @return string
     */
    private function handleMeta($html, $page) {
        $meta = '';

        if ($page->title) {
            $meta .= "\n\t<title>{$page->title}</title>\n";
            $meta .= "\t<meta name=\"title\" content=\"{$page->title}\">\n";
        }

        if ($page->tags) {
            $meta .= "\t<meta name=\"tags\" content=\"{$page->tags}\">\n";
        }

        if ($page->description) {
            $meta .= "\t<meta name=\"description\" content=\"{$page->description}\">\n";
        }

        return preg_replace('/(<meta name="viewport" content="width=device-width, initial-scale=1">)/', "$1$meta", $page->html);
    }

    /**
     * Zip files and folders at the given path.
     *
     * @param  string $path
     * @param  string $name archive name
     *
     * @return boolean/string
     */
    private function zip($path, $name)
    {
        $realPath = realpath($path);
        $absolute = $realPath.DIRECTORY_SEPARATOR;
        $ignore   = array(realpath($this->exportsPath), $realPath);

        //delete old zip if it exists
        if (is_file($absolute.$name.'.zip')) {
            unlink($absolute.$name.'.zip');
        }

        $zip = new ZipArchive();
        $zip->open($absolute.$name.'.zip', ZipArchive::CREATE);

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($realPath), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $path = $file->getRealPath();

            if ( ! in_array($file->getRealPath(), $ignore)) {
                if (is_dir($file))
                {
                    $zip->addEmptyDir(str_replace($absolute, '', $path));
                }
                else if (is_file($file))
                {
                    $zip->addFromString(str_replace($absolute, '', $path), file_get_contents($file));
                }
            }
        }

        if ($zip->close()) {
            return $absolute.$name.'.zip';
        }
    }

    /**
     * Cope any local js libraries to export folder.
     *
     * @param  string  $libraries
     * @param  string  $path
     *
     * @return string  scripts html to insert before closing body tag
     */
    private function handleLibraries($libraries, $path)
    {

        $scripts = "<script src=\"js/jquery.js\"></script>\n<script src=\"js/bootstrap.js\"></script>\n";

        @$this->fs->copy('lib/jquery/dist/jquery.js', $path.'js/jquery.js');
        @$this->fs->copy('lib/bootstrap/dist/js/bootstrap.min.js', $path.'js/bootstrap.js');

        // font-awesome
        @$this->fs->copy('lib/font-awesome/css/font-awesome.min.css', $path.'css/font-awesome.css');
        File::copyDirectory(public_path().'/lib/font-awesome/fonts', $path.'fonts');

        $libraries = json_decode($libraries);
        if ($libraries) {

            foreach ($libraries as $library) {

                if (is_string($library)) {
                    $library = Library::where('name','=', $library)->first();
                }

                if ( ! str_contains($library->path, '//')) {
                    $absolute = $this->relativeToAbsolute($library->path);

                    try {
                        @$this->fs->copy($absolute, $path.'js/'.basename($absolute));
                    } catch (\Exception $e) {
                        continue;
                    }

                    $scripts .= '<script src="js/'.basename($library->path)."\"></script>\n";
                } else {
                    $scripts .= '<script src="'.$library->path."\"></script>\n";
                }
            }
        }

        return $scripts;
    }

    /**
     * Append any used custom elements css to css file.
     *
     * @param  array      $elems
     * @param  array      $paths
     * @param  PageModel  $page
     * @param  string     $path
     *
     * @return array
     */
    private function handleCustomElementsCss(array $elems, array $paths, $page, $path)
    {
        $elemsCss = '';

        //search for any custom elements in the page html
        foreach ($elems as $element) {
            if ($element !== '.' && $element !== '..') {

                //convert element to dash-case, remove .html and remove last s to convert to singular
                $name = rtrim(str_replace('.html', '', strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $element))), 's');

                if (str_contains($page->html, $name)) {

                    //get css from the element
                    $elemContents = file_get_contents(public_path()."/elements/$element");
                    preg_match('/<style.*?>(.+?)<\/style>/s', $elemContents, $css);

                    //if custom element has any css append it to the already existing page css.
                    if (isset($css[1]) && $css = trim($css[1])) {
                        $elemsCss .= "\n\n$css";
                    }
                }
            }
        }

        if ($elemsCss) {
            @file_put_contents($path."css/{$page->name}-stylesheet.css", trim($elemsCss), FILE_APPEND);
            $paths[$page->name] = "css/{$page->name}-stylesheet.css";
        }

        return $paths;
    }

    /**
     * Create export zip file for given project.
     *
     * @param  string/int $id
     * @param  boolean $zip
     *
     * @return boolean/string
     */
    public function
    project($id, $zip = true)
    {
        $project = Project::find($id);

        //bail if no project found
        if ( ! $project) return false;

        //bail if project not public and doesn't belong to current user
//        if ( ! $project->public) {
//            if ( ! $project->users()->where('users.id', $this->app['sentry']->getUser()->id)->first()) {
//                return false;
//            }
//        }

        if ($path = $this->createFolders($project)) {
            $this->createFiles($path, $project);

            if ($zip) {
                return $this->zip($path, $project->name);
            }

            return $path;
        }
    }

    /**
     * Compile absolute url to local image from url encoded string.
     *
     * @param  string $url
     * @return string
     */
    public function decodeImageUrl($url)
    {
        return public_path().str_replace(';', '/', urldecode($url));
    }

}
