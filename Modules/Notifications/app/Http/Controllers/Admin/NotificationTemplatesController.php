<?php

namespace Modules\Notifications\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Notifications\App\Models\NotificationTemplate;

class NotificationTemplatesController extends Controller
{
    public function index()
    {
        $templates = NotificationTemplate::orderBy('key')->paginate(15);

        return view('notifications::admin.control.templates.index', compact('templates'));
    }

    public function create()
    {
        $types = config('notifications.notification_types', ['generic']);

        return view('notifications::admin.control.templates.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:128|unique:notification_templates,key',
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'channels' => 'nullable|array',
            'channels.*' => 'in:in_app,email,webpush,sms',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        NotificationTemplate::create($validated);

        return redirect()->route('admin.notifications.templates.index')
            ->with('success', 'Template criado com sucesso.');
    }

    public function edit(NotificationTemplate $template)
    {
        $types = config('notifications.notification_types', ['generic']);

        return view('notifications::admin.control.templates.edit', compact('template', 'types'));
    }

    public function update(Request $request, NotificationTemplate $template)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:128|unique:notification_templates,key,'.$template->id,
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'channels' => 'nullable|array',
            'channels.*' => 'in:in_app,email,webpush,sms',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $template->update($validated);

        return redirect()->route('admin.notifications.templates.index')
            ->with('success', 'Template atualizado com sucesso.');
    }

    public function destroy(NotificationTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.notifications.templates.index')
            ->with('success', 'Template removido.');
    }
}
