<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

/** Boîte de réception (formulaire de contact) et abonnés newsletter */
class MessageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $messages = ContactMessage::when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = ContactMessage::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.pages.messages.index', compact('messages', 'counts', 'status'));
    }

    public function show(ContactMessage $message)
    {
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
            Cache::forget('admin_badges');
        }

        return view('admin.pages.messages.show', compact('message'));
    }

    public function markReplied(ContactMessage $message)
    {
        $message->update(['status' => 'replied']);
        Cache::forget('admin_badges');

        Alert::toast('Message marqué comme traité', 'success');
        return back();
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        Cache::forget('admin_badges');

        Alert::toast('Message supprimé', 'success');
        return redirect()->route('messages.index');
    }

    public function newsletter()
    {
        $subscribers = NewsletterSubscriber::latest('subscribed_at')->paginate(30);
        $activeCount = NewsletterSubscriber::where('status', 'active')->count();

        return view('admin.pages.messages.newsletter', compact('subscribers', 'activeCount'));
    }

    public function exportNewsletter()
    {
        $filename = 'abonnes-newsletter-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($out, ['Email', 'Nom', 'Statut', 'Inscrit le'], ';');
            NewsletterSubscriber::orderBy('subscribed_at')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $s) {
                    fputcsv($out, [$s->email, $s->name, $s->status, $s->subscribed_at ? \Carbon\Carbon::parse($s->subscribed_at)->format('d/m/Y H:i') : ''], ';');
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
