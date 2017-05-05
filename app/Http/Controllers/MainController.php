<?php namespace App\Http\Controllers;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use Blade;
use File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MainController extends Controller {

    /**
     * Symfonys finder.
     *
     * @var Symfony\Component\Finder\Finder
     */
    private $finder;

    public function home()
    {
        return view('main');
    }

    public function login(Request $t)
    {
        return view('pages.login');
    }

    /**
     * Parse and compile all custom elements in elements folder.
     *
     * @return Response
     */
    public function customElements()
    {
        $elements = array();

        $files = File::allFiles('elements');


        foreach ($files as $file) {
            $contents = $file->getContents();

            preg_match('/<script>(.+?)<\/script>/s', $contents, $config);
            preg_match('/<style.*?>(.+?)<\/style>/s', $contents, $css);
            preg_match('/<\/style.*?>(.+?)<script>/s', $contents, $html);

            if ( ! isset($config[1]) || ! isset($html[1])) {
                continue;
            }

            $elements[] = array(
                'css' => isset($css[1]) ? trim($css[1]) : '',
                'html' => trim($html[1]),
                'config' => trim($config[1])
            );
        }

        return new Response(json_encode($elements));
    }

}
