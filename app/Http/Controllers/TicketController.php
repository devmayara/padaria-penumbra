<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Services\QrCodeService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    protected $qrCodeService;
    protected $pdfService;

    public function __construct(QrCodeService $qrCodeService, PdfService $pdfService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
    }

    /**
     * Generate ticket for an order.
     */
    public function generate(Order $order)
    {
        // Check if user has access to this order
        if (Auth::user()->role === 'member' && $order->user_id !== Auth::id()) {
            abort(403, 'Acesso negado. Este pedido não pertence a você.');
        }

        // Check if order is not cancelled
        if ($order->status === 'cancelado') {
            return back()->with('error', 'Não é possível gerar ficha para pedidos cancelados.');
        }

        // Check if ticket already exists
        if ($order->ticket) {
            return back()->with('info', 'Este pedido já possui uma ficha gerada.');
        }

        try {
            DB::transaction(function () use ($order) {
                // Generate ticket number
                $ticketNumber = Ticket::generateTicketNumber();
                
                // Generate QR code data
                $qrData = $this->qrCodeService->generateTicketData($ticketNumber, $order->id);
                
                // Save QR code to storage
                $qrCodePath = $this->qrCodeService->saveToStorage($qrData, $ticketNumber);
                
                // Create ticket record
                $ticket = Ticket::create([
                    'order_id' => $order->id,
                    'ticket_number' => $ticketNumber,
                    'qr_code_path' => $qrCodePath,
                    'status' => 'pendente',
                ]);
                
                // Generate PDF
                $pdfPath = $this->pdfService->generateTicketPdf($ticket);
                
                // Update ticket with PDF path
                $ticket->update([
                    'pdf_path' => $pdfPath,
                    'status' => 'gerado',
                    'generated_at' => now(),
                ]);
            });

            return back()->with('success', 'Ficha gerada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar ficha: ' . $e->getMessage());
        }
    }

    /**
     * Show ticket details.
     */
    public function show(Ticket $ticket)
    {
        // Check if user has access to this ticket
        if (Auth::user()->role === 'member' && $ticket->order->user_id !== Auth::id()) {
            abort(403, 'Acesso negado. Esta ficha não pertence a você.');
        }

        $ticket->load(['order.items.product.category', 'order.user']);
        
        // Return different views based on user role
        if (Auth::user()->role === 'admin') {
            return view('tickets.show-admin', compact('ticket'));
        } else {
            return view('tickets.show', compact('ticket'));
        }
    }

    /**
     * Download ticket PDF.
     */
    public function download(Ticket $ticket)
    {
        // Check if user has access to this ticket
        if (Auth::user()->role === 'member' && $ticket->order->user_id !== Auth::id()) {
            abort(403, 'Acesso negado. Esta ficha não pertence a você.');
        }

        if (!$ticket->pdf_path || !Storage::disk('public')->exists($ticket->pdf_path)) {
            return back()->with('error', 'PDF não encontrado.');
        }

        $filename = 'ticket_' . $ticket->ticket_number . '.pdf';
        
        return response()->download(storage_path('app/public/' . $ticket->pdf_path), $filename);
    }

    /**
     * Print ticket (mark as printed).
     */
    public function print(Ticket $ticket)
    {
        // Check if user has access to this ticket
        if (Auth::user()->role === 'member' && $ticket->order->user_id !== Auth::id()) {
            abort(403, 'Acesso negado. Esta ficha não pertence a você.');
        }

        if (!$ticket->isGenerated()) {
            return back()->with('error', 'Esta ficha ainda não foi gerada.');
        }

        $ticket->markAsPrinted();
        
        return back()->with('success', 'Ficha marcada como impressa!');
    }

    /**
     * List all tickets (admin only).
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem visualizar todas as fichas.');
        }

        $query = Ticket::with(['order.user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by ticket number
        if ($request->filled('ticket_number')) {
            $query->where('ticket_number', 'like', '%' . $request->ticket_number . '%');
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Regenerate ticket (admin only).
     */
    public function regenerate(Ticket $ticket)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem regenerar fichas.');
        }

        try {
            DB::transaction(function () use ($ticket) {
                // Delete old files
                if ($ticket->qr_code_path) {
                    Storage::disk('public')->delete($ticket->qr_code_path);
                }
                if ($ticket->pdf_path) {
                    Storage::disk('public')->delete($ticket->pdf_path);
                }

                // Generate new QR code
                $qrData = $this->qrCodeService->generateTicketData($ticket->ticket_number, $ticket->order_id);
                $qrCodePath = $this->qrCodeService->saveToStorage($qrData, $ticket->ticket_number);

                // Generate new PDF
                $pdfPath = $this->pdfService->generateTicketPdf($ticket);

                // Update ticket
                $ticket->update([
                    'qr_code_path' => $qrCodePath,
                    'pdf_path' => $pdfPath,
                    'status' => 'gerado',
                    'generated_at' => now(),
                    'printed_at' => null,
                ]);
            });

            return back()->with('success', 'Ficha regenerada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao regenerar ficha: ' . $e->getMessage());
        }
    }
}
