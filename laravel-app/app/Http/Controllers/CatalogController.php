<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;

class CatalogController extends Controller
{
    public function index()
    {
        return view('catalog.index');
    }
}
