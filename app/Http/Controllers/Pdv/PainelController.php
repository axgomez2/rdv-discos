<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PainelController extends Controller
{
    public function index()
    {
        return view('pdv.index');
    }
}
