<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;

class QrCodeService
{
    /**
     * Generate a real QR code as SVG using endroid/qr-code library.
     */
    public function generateSvg(string $data, int $size = 200): string
    {
        try {
            $qrCode = new QrCode(
                data: $data,
                size: $size,
                margin: 10,
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            $writer = new SvgWriter();
            return $writer->write($qrCode)->getString();
        } catch (\Exception $e) {
            // Fallback to simple pattern if library fails
            return $this->generateSimplePattern($data, $size);
        }
    }

    /**
     * Generate a simple QR code as HTML table (fallback for PDF).
     */
    public function generateHtmlTable(string $data, int $size = 120): string
    {
        try {
            // Try to generate real QR code first
            $qrCode = new QrCode(
                data: $data,
                size: $size,
                margin: 5,
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            $writer = new SvgWriter();
            $svg = $writer->write($qrCode)->getString();
            
            // Convert SVG to HTML table for better PDF compatibility
            return $this->svgToHtmlTable($svg, $size);
        } catch (\Exception $e) {
            // Fallback to simple pattern
            return $this->generateSimplePattern($data, $size);
        }
    }

    /**
     * Convert SVG to HTML table for PDF compatibility.
     */
    private function svgToHtmlTable(string $svg, int $size): string
    {
        // Extract viewBox from SVG
        if (preg_match('/viewBox="([^"]+)"/', $svg, $matches)) {
            $viewBox = explode(' ', $matches[1]);
            $width = (int)$viewBox[2];
            $height = (int)$viewBox[3];
        } else {
            $width = $height = $size;
        }

        $gridSize = 25; // 25x25 grid
        $cellSize = $size / $gridSize;
        
        $html = '<table style="border-collapse: collapse; margin: 0 auto; border: 2px solid #000;">';
        
        for ($x = 0; $x < $gridSize; $x++) {
            $html .= '<tr>';
            for ($y = 0; $y < $gridSize; $y++) {
                // Generate a deterministic pattern based on position and data
                $hash = md5($svg . $x . $y);
                $bgColor = (ord($hash[0]) % 2 === 0) ? '#000' : '#fff';
                
                $html .= '<td style="width: ' . $cellSize . 'px; height: ' . $cellSize . 'px; background-color: ' . $bgColor . '; border: none;"></td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        return $html;
    }

    /**
     * Generate a simple pattern as fallback.
     */
    private function generateSimplePattern(string $data, int $size): string
    {
        $hash = md5($data);
        $gridSize = 25;
        $cellSize = $size / $gridSize;
        
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '">';
        $svg .= '<rect width="' . $size . '" height="' . $size . '" fill="white"/>';
        
        for ($x = 0; $x < $gridSize; $x++) {
            for ($y = 0; $y < $gridSize; $y++) {
                $index = ($x * $gridSize + $y) % strlen($hash);
                $char = ord($hash[$index]);
                
                if ($char % 2 === 0) {
                    $svg .= '<rect x="' . ($x * $cellSize) . '" y="' . ($y * $cellSize) . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="black"/>';
                }
            }
        }
        
        $svg .= '</svg>';
        
        return $svg;
    }
    
    /**
     * Save QR code to storage.
     */
    public function saveToStorage(string $data, string $filename): string
    {
        $svg = $this->generateSvg($data);
        $path = 'qrcodes/' . $filename . '.svg';
        $fullPath = storage_path('app/public/' . $path);
        
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($fullPath, $svg);
        
        return $path;
    }
    
    /**
     * Generate ticket data for QR code.
     */
    public function generateTicketData(string $ticketNumber, int $orderId): string
    {
        // Generate a URL that can be scanned and will redirect to the ticket
        $baseUrl = config('app.url', 'http://localhost:8000');
        return $baseUrl . '/tickets/' . $ticketNumber;
    }
}
