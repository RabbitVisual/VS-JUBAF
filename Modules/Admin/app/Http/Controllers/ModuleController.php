<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Nwidart\Modules\Facades\Module;

class ModuleController extends Controller
{
    /**
     * Display a listing of modules.
     */
    public function index()
    {
        $modules = Module::all();
        $modulesData = [];

        foreach ($modules as $module) {
            $modulesData[] = [
                'name' => $module->getName(),
                'alias' => $module->get('alias', $module->getLowerName()),
                'enabled' => $module->isEnabled(),
                'priority' => $module->get('priority', 0),
                'description' => $module->get('description', ''),
                'is_core' => $module->get('is_core', false),
                'version' => $module->get('version', '1.0.0'),
                'author' => $module->get('author', []),
                'keywords' => $module->get('keywords', []),
                'settings_route' => $module->get('settings_route'),
            ];
        }

        // Sort by priority (core modules first)
        usort($modulesData, function ($a, $b) {
            if ($a['is_core'] && ! $b['is_core']) {
                return -1;
            }
            if (! $a['is_core'] && $b['is_core']) {
                return 1;
            }

            return $b['priority'] <=> $a['priority'];
        });

        return view('admin::modules.index', compact('modulesData'));
    }

    /**
     * Enable a module.
     */
    public function enable($moduleName)
    {
        try {
            $module = Module::find($moduleName);

            if (! $module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Módulo não encontrado',
                ], 404);
            }

            if ($module->get('is_core', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível desativar módulos core do sistema',
                ], 403);
            }

            Module::enable($moduleName);

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Módulo ativado com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao ativar módulo: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disable a module.
     */
    public function disable($moduleName)
    {
        try {
            $module = Module::find($moduleName);

            if (! $module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Módulo não encontrado',
                ], 404);
            }

            if ($module->get('is_core', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível desativar módulos core do sistema',
                ], 403);
            }

            Module::disable($moduleName);

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Módulo desativado com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao desativar módulo: '.$e->getMessage(),
            ], 500);
        }
    }
}
