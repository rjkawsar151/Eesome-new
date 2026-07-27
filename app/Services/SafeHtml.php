<?php

namespace App\Services;

class SafeHtml
{
    public function sanitize(?string $html): string
    {
        $html = preg_replace('#<(script|style|iframe|object|embed|form)\b[^>]*>.*?</\1>#is', '', (string) $html);
        $html = preg_replace('/\son\w+\s*=\s*(["\']).*?\1/is', '', $html);
        $html = preg_replace('/\sstyle\s*=\s*(["\']).*?\1/is', '', $html);
        $html = preg_replace_callback('/\shref\s*=\s*(["\'])(.*?)\1/is', function ($match) {
            $url = trim($match[2]);

            return preg_match('#^(https?://|/|mailto:)#i', $url) ? ' href="'.$url.'"' : '';
        }, $html);

        return trim(strip_tags($html, '<p><br><div><span><strong><b><em><i><u><h2><h3><h4><ul><ol><li><blockquote><table><thead><tbody><tr><th><td><a>'));
    }
}
