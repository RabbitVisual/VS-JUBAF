<?php

namespace Modules\Comunicacao\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Comunicacao\Http\Requests\StorePostagemRequest;
use Modules\Comunicacao\Http\Requests\UpdatePostagemRequest;
use Modules\Comunicacao\Models\Postagem;
use Modules\Notifications\App\Services\InAppNotificationService;
use App\Models\User;

class AdminPostagemController extends Controller
{
    public function index(Request $request): View
    {
        $q = Postagem::query()->with('autor')->latest();

        if ($request->filled('tipo')) {
            $q->where('tipo', $request->string('tipo'));
        }

        $postagens = $q->paginate(15)->withQueryString();

        return view('comunicacao::admin.index', compact('postagens'));
    }

    public function create(): View
    {
        return view('comunicacao::admin.form', ['postagem' => new Postagem]);
    }

    public function store(StorePostagemRequest $request, InAppNotificationService $notificationService): RedirectResponse
    {
        $data = $request->validated();
        unset($data['anexo']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('anexo')) {
            $data['anexo_path'] = $this->storeAnexo($request->file('anexo'));
        }

        $postagem = Postagem::query()->create($data);

        // Disparar notificação global do Mural
        $users = User::where('is_active', true)->get();
        if ($users->isNotEmpty()) {
            $notificationService->sendToUsers($users, 'Novo Aviso no Mural', $postagem->titulo, [
                'type' => 'mural_post',
                'action_url' => route('memberpanel.mural.index'),
                'action_text' => 'Ler Aviso',
            ]);
        }

        return redirect()
            ->route('admin.comunicacao.postagens.index')
            ->with('success', 'Postagem criada com sucesso e notificação disparada.');
    }

    public function edit(Postagem $postagem): View
    {
        return view('comunicacao::admin.form', compact('postagem'));
    }

    public function update(UpdatePostagemRequest $request, Postagem $postagem): RedirectResponse
    {
        $data = $request->validated();
        unset($data['anexo']);

        if ($request->hasFile('anexo')) {
            $this->deleteAnexoFileIfExists($postagem->anexo_path);
            $data['anexo_path'] = $this->storeAnexo($request->file('anexo'));
        }

        $postagem->update($data);

        return redirect()
            ->route('admin.comunicacao.postagens.index')
            ->with('success', 'Postagem atualizada com sucesso.');
    }

    public function destroy(Postagem $postagem): RedirectResponse
    {
        $this->deleteAnexoFileIfExists($postagem->anexo_path);
        $postagem->delete();

        return redirect()
            ->route('admin.comunicacao.postagens.index')
            ->with('success', 'Postagem removida.');
    }

    private function storeAnexo(\Illuminate\Http\UploadedFile $file): string
    {
        $dir = public_path('comunicacao/anexos');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $ext = $file->getClientOriginalExtension() ?: ($file->guessExtension() ?? 'bin');
        $safeName = Str::uuid()->toString().'.'.$ext;
        $file->move($dir, $safeName);

        return 'comunicacao/anexos/'.$safeName;
    }

    private function deleteAnexoFileIfExists(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }
        $full = public_path($relativePath);
        if (File::isFile($full)) {
            File::delete($full);
        }
    }
}
