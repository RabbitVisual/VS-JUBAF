<?php

namespace Modules\LiderancaPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class SermoesController extends Controller
{
    /**
     * Estúdio da Palavra - listagem liderancaal (layout liderancaal).
     */
    public function index()
    {
        $sermons = collect();
        if (class_exists(\Modules\Sermons\App\Models\Sermon::class) && Schema::hasTable('sermons')) {
            $sermons = \Modules\Sermons\App\Models\Sermon::query()
                ->with(['category', 'user'])
                ->whereNotNull('published_at')
                ->orderBy('sermon_date', 'desc')
                ->limit(20)
                ->get();
        }

        return view('liderancapanel::sermoes.index', compact('sermons'));
    }
}
