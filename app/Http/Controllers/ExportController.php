<?php namespace App\Http\Controllers;


use App\Export;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller {


    /**
     * Exporter instance.
     *
     * @var Export
     */
    private $export;

    /**
     * Create new ExportsController isntance.
     *
     * @param Application $app
     * @param Request $request
     */
    public function __construct(Request $request, Export $exporter)
    {
//        $this->app = $app;
        $this->export = $exporter;
//        $this->request = $request;
//        $this->input = $request->request;
    }

    /**
     * Export a page with given id.
     *
     * @param  string /int $id
     * @return Response
     */
    public function exportPage($id)
    {
//        return $path = $this->export->page($id);
        if ($path = $this->export->page($id))
        {
            return Response()->download($path);
        }

        return new Response('pageExportProblem', 500);
    }

    /**
     * Export a project with given id.
     *
     * @param  string/int $id
     * @return Response
     */
    public function exportProject($id)
    {
//        return $this->export->project($id);
        if ($path = $this->export->project($id)) {
            return Response()->download($path);
        }

        return new Response('exportProblem', 500);
    }

    /**
     * Export image at given path.
     *
     * @param  string $path
     * @return Response
     */
    public function exportImage($path)
    {
        $url = $this->export->decodeImageUrl($path);
//        return $url;
        return Response()->download($url);
    }

}
