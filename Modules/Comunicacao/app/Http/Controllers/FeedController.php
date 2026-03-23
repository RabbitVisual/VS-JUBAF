<?php

namespace Modules\Comunicacao\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Comunicacao\Models\Postagem;

class FeedController extends Controller
{
    public function index(): View
    {
        $postagens = Postagem::query()->with('autor')->latest()->paginate(10);

        return view('comunicacao::feed.index', compact('postagens'));
    }
}
