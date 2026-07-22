<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateRequest;
use App\Models\MessageTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MessageTemplateController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', MessageTemplate::class);

        $templates = MessageTemplate::where('user_id', $request->user()->id)->latest()->get();

        return view('templates.index', compact('templates'));
    }

    public function store(StoreTemplateRequest $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('create', MessageTemplate::class);

        $template = MessageTemplate::create([
            'user_id' => $request->user()->id,
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'content' => $request->input('content'),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'template' => $template]);
        }

        return redirect()->route('templates.index')->with('status', 'Template de mensagem criado!');
    }

    public function update(StoreTemplateRequest $request, MessageTemplate $template): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $template);

        $template->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'template' => $template]);
        }

        return redirect()->route('templates.index')->with('status', 'Template atualizado com sucesso!');
    }

    public function destroy(MessageTemplate $template): RedirectResponse|JsonResponse
    {
        Gate::authorize('delete', $template);

        $template->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('templates.index')->with('status', 'Template excluído!');
    }
}
