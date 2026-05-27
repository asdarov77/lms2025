<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateController extends Controller
{
    /**
     * Stream private course files with proper MIME type
     * This replaces the hardcoded htmles, htmles2...htmles8 methods
     */
    public function stream($aircraft, $auk, $path = '')
    {
        // Construct the full path
        $filePath = rtrim("private/{$aircraft}/{$auk}/{$path}", '/');
        
        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $extension = pathinfo($filePath)['extension'] ?? '';
        $mimeType = $this->getMimeType($extension);

        return response()->stream(function () use ($filePath) {
            $stream = Storage::readStream($filePath);
            while (!feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => Storage::size($filePath),
        ]);
    }

    /**
     * Get MIME type by file extension
     */
    private function getMimeType(string $extension): string
    {
        return match (strtolower($extension)) {
            'html', 'htm' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'eot' => 'application/vnd.ms-fontobject',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }
}
