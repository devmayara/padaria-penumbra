<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Ticket;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Generate ticket PDF.
     */
    public function generateTicketPdf(Ticket $ticket): string
    {
        $order = $ticket->order;
        $order->load(['items.product.category', 'user']);
        
        // Gera o QR code como HTML table para melhor compatibilidade com PDF
        $qrCodeService = app(QrCodeService::class);
        $qrData = $qrCodeService->generateTicketData($ticket->ticket_number, $order->id);
        $qrCodeHtml = $qrCodeService->generateHtmlTable($qrData, 120);
        
        // Debug: verifica se o QR code foi gerado
        if (empty($qrCodeHtml)) {
            \Illuminate\Support\Facades\Log::error('QR Code HTML vazio para ticket #'.$ticket->ticket_number);
        } else {
            \Illuminate\Support\Facades\Log::info('QR Code HTML gerado para ticket #'.$ticket->ticket_number.' - Tamanho: '.strlen($qrCodeHtml));
        }
        
        $data = [
            'ticket' => $ticket,
            'order' => $order,
            'items' => $order->items,
            'user' => $order->user,
            'qrCodeHtml' => $qrCodeHtml,
        ];
        
        $pdf = Pdf::loadView('pdfs.ticket', $data);
        
        // Set paper size and orientation
        $pdf->setPaper('a6', 'portrait');
        
        // Generate filename
        $filename = 'ticket_' . $ticket->ticket_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $path = 'tickets/' . $filename;
        
        // Ensure directory exists
        $fullPath = storage_path('app/public/' . $path);
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Save PDF to storage
        $pdf->save($fullPath);
        
        return $path;
    }
    
    /**
     * Generate ticket PDF content as HTML.
     */
    public function generateTicketHtml(Ticket $ticket): string
    {
        $order = $ticket->order;
        $order->load(['items.product.category', 'user']);
        
        // Gera o QR code como HTML table para melhor compatibilidade
        $qrCodeService = app(QrCodeService::class);
        $qrData = $qrCodeService->generateTicketData($ticket->ticket_number, $order->id);
        $qrCodeHtml = $qrCodeService->generateHtmlTable($qrData, 120);
        
        $data = [
            'ticket' => $ticket,
            'order' => $order,
            'items' => $order->items,
            'user' => $order->user,
            'qrCodeHtml' => $qrCodeHtml,
        ];
        
        return view('pdfs.ticket', $data)->render();
    }
}
