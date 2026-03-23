<?php

namespace Modules\LiderancaPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LiderancaPanelController extends Controller
{
    public function index()
    {
        return view('liderancapanel::index');
    }

    public function create()
    {
        return view('liderancapanel::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('liderancapanel::show');
    }

    public function edit($id)
    {
        return view('liderancapanel::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
