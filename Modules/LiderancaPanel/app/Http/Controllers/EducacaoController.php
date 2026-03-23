<?php

namespace Modules\liderancapanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class EducacaoController extends Controller
{
    /**
     * Educação - visão macro EBD e Worship Academy (layout pastoral).
     */
    public function index()
    {
        $ebdClasses = collect();
        $ebdCourses = collect();
        $academyCourses = collect();

        try {
            if (Schema::hasTable('ebd_classes')) {
                $ebdClasses = \Illuminate\Support\Facades\DB::table('ebd_classes')->where('is_active', true)->limit(10)->get();
            }
        } catch (\Throwable $e) {
            // EBD tables may have been restructured
        }
        try {
            if (Schema::hasTable('ebd_courses')) {
                $ebdCourses = \Illuminate\Support\Facades\DB::table('ebd_courses')->orderBy('created_at', 'desc')->limit(5)->get();
            }
        } catch (\Throwable $e) {
            //
        }
        if (class_exists(\Modules\Worship\App\Models\AcademyCourse::class) && Schema::hasTable('academy_courses')) {
            $academyCourses = \Modules\Worship\App\Models\AcademyCourse::query()->orderBy('created_at', 'desc')->limit(5)->get();
        }

        return view('liderancapanel::educacao.index', compact('ebdClasses', 'ebdCourses', 'academyCourses'));
    }
}
