<?php

namespace App\Http\Controllers;

use App\Models\Aukstructure;
use App\Models\Link;
use App\Models\Aircraft;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'path' => 'required|string',
            'aircraft' => 'required|exists:aircrafts,id',
        ]);

        $searchTerm = strtolower($request->input('query'));
        $aircraft = Aircraft::find($request->input('aircraft'));
        $directory = "private/{$aircraft->path}/{$request->input('path')}/";

        $matches = [];
        $files = \Storage::disk('public')->files($directory);

        foreach ($files as $file) {
            if (str_ends_with($file, '.html') && !str_ends_with($file, 'index.html')) {
                $contents = \Storage::get($file);
                $lowerContents = strtolower($contents);

                if (str_contains($lowerContents, $searchTerm)) {
                    $filename = basename($file);
                    $link = Link::where('link', $filename)->first();
                    $aukstructure = Aukstructure::whereHas('links', function($query) use ($filename) {
                        $query->where('link', $filename);
                    })->first();

                    $highlightedResult = $this->highlightWords($contents, $searchTerm);
                    
                    $matches[] = [
                        'file' => $file,
                        'title' => $aukstructure?->title ?? 'Без названия',
                        'itemId' => $aukstructure?->id ?? null,
                        'highlightedNodes' => $highlightedResult['highlightedNodes'],
                        'originalXpath' => $highlightedResult['originalXpath'],
                    ];
                }
            }
        }

        return response()->json($matches);
    }

    private function highlightWords(string $html, string $searchTerm): array
    {
        libxml_use_internal_errors(true);
        
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOWARNING | LIBXML_NOERROR);
        
        $xpath = new DOMXPath($dom);
        $textNodes = $xpath->query('//text()[contains(translate(., "А-Я", "а-я"), "' . strtolower($searchTerm) . '")]');
        
        $highlightedNodes = [];
        $originalXpaths = [];
        $highlightId = 0;

        foreach ($textNodes as $node) {
            $text = $node->nodeValue;
            $pos = stripos($text, $searchTerm);
            
            if ($pos !== false) {
                $before = substr($text, 0, $pos);
                $match = substr($text, $pos, strlen($searchTerm));
                $after = substr($text, $pos + strlen($searchTerm));

                $node->nodeValue = $before;
                
                $markElement = $dom->createElement('mark', htmlspecialchars($match, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
                $markElement->setAttribute('class', 'highlighted');
                $markElement->setAttribute('data-id', 'hl-' . ++$highlightId);
                
                $node->parentNode->insertBefore($markElement, $node->nextSibling);
                
                if (!empty($after)) {
                    $afterNode = $dom->createTextNode($after);
                    $node->parentNode->insertBefore($afterNode, $markElement->nextSibling);
                }

                $highlightedNodes[] = $dom->saveHTML($markElement);
                $originalXpaths[] = $this->getNodePath($markElement);
            }
        }

        return [
            'highlightedNodes' => $highlightedNodes,
            'originalXpath' => $originalXpaths,
        ];
    }

    private function getNodePath(\DOMNode $node): string
    {
        $path = [];
        $current = $node;
        
        while ($current instanceof DOMElement && $current->nodeName !== 'html') {
            $index = 1;
            $siblings = $current->parentNode->childNodes;
            
            foreach ($siblings as $sibling) {
                if ($sibling === $current) break;
                if ($sibling instanceof DOMElement && $sibling->nodeName === $current->nodeName) {
                    $index++;
                }
            }
            
            $path[] = $current->nodeName . '[' . $index . ']';
            $current = $current->parentNode;
        }
        
        return '/' . implode('/', array_reverse($path));
    }
}
