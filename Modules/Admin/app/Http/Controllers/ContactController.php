<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HomePage\App\Models\ContactMessage;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query()->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            }
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        if ($request->filled('search')) {
            $q = '%' . $request->search . '%';
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', $q)
                    ->orWhere('email', 'like', $q)
                    ->orWhere('message', 'like', $q);
            });
        }

        $messages = $query->paginate(15)->withQueryString();

        return view('homepage::admin.homepage.contacts.index', compact('messages'));
    }

    /**
     * Mark one or more messages as read.
     */
    public function markRead(Request $request)
    {
        $request->validate(['ids' => 'nullable|array', 'ids.*' => 'integer|exists:contact_messages,id']);

        $ids = $request->input('ids', []);
        if (count($ids) > 0) {
            ContactMessage::whereIn('id', $ids)->update(['read_at' => now()]);
            return redirect()->route('admin.homepage.contacts.index', $request->only('status', 'search'))
                ->with('success', 'Mensagem(s) marcada(s) como lida(s).');
        }

        return redirect()->route('admin.homepage.contacts.index', $request->only('status', 'search'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);

        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view('homepage::admin.homepage.contacts.show', compact('message'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.homepage.contacts.index')
            ->with('success', 'Mensagem excluída com sucesso.');
    }
}
