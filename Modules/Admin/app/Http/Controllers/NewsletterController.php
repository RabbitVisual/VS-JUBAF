<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HomePage\App\Models\NewsletterSubscriber;
use Symfony\Component\HttpFoundation\StreamedResponse;

// You would ideally create a Mailable for this: use App\Mail\NewsletterCampaign;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query()->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            }
            if ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $subscribers = $query->paginate(20)->withQueryString();
        $totalCount = NewsletterSubscriber::count();
        $activeCount = NewsletterSubscriber::where('is_active', true)->count();

        return view('homepage::admin.homepage.newsletter.index', compact('subscribers', 'totalCount', 'activeCount'));
    }

    /**
     * Export subscribers as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = NewsletterSubscriber::query()->orderBy('created_at', 'desc');
        if ($request->filled('status') && $request->status === 'active') {
            $query->where('is_active', true);
        }
        if ($request->filled('status') && $request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $filename = 'newsletter-assinantes-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['E-mail', 'Nome', 'Ativo', 'Data de inscrição', 'Confirmado']);
            $query->chunk(100, function ($subscribers) use ($handle) {
                foreach ($subscribers as $s) {
                    fputcsv($handle, [
                        $s->email,
                        $s->name ?? '',
                        $s->is_active ? 'Sim' : 'Não',
                        $s->subscribed_at?->format('d/m/Y H:i') ?? '',
                        $s->is_confirmed ? 'Sim' : 'Não',
                    ]);
                }
            });
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Show the form for creating a new email campaign.
     * We hijack 'create' for 'compose' to fit resources, or just use a custom route.
     */
    public function create()
    {
        return view('homepage::admin.homepage.newsletter.compose');
    }

    /**
     * Send the email to subscribers.
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Dispara o Job para envio em background
        \Modules\Admin\App\Jobs\SendNewsletterJob::dispatch($request->subject, $request->content);

        return redirect()->route('admin.homepage.newsletter.index')
            ->with('success', 'O envio da campanha foi iniciado e será processado em segundo plano.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        return redirect()->route('admin.homepage.newsletter.index')
            ->with('success', 'Assinante removido com sucesso.');
    }
}
