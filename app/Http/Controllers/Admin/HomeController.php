<?php
/**
 * Created by PhpStorm.
 * User: Илья
 * Date: 27.01.2019
 * Time: 2:02
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Elasticsearch\ClientBuilder;

class HomeController extends Controller
{

    public function index()
    {

        return view('admin.home');
    }

}