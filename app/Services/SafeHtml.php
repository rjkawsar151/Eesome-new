<?php

namespace App\Services;

class SafeHtml
{
    /**
     * Allowed HTML tags for rich content.
     */
    private const ALLOWED_TAGS = '<p><br><div><span><strong><b><em><i><u><s><del><h2><h3><h4><h5><h6><ul><ol><li><blockquote><table><thead><tbody><tr><th><td><a><hr><img>';

    public function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // 1. Remove dangerous executable/embedding elements and their inner content
        $html = preg_replace('#<(script|style|iframe|object|embed|form|applet|svg|math|base|link|meta)\b[^>]*>.*?</\1>#is', '', (string) $html);

        // 2. Strip any dangling or self-closing dangerous tags
        $html = preg_replace('#</?(script|style|iframe|object|embed|form|applet|svg|math|base|link|meta)\b[^>]*>#is', '', $html);

        // 3. Remove all inline javascript event handlers (e.g. onclick, onerror, onload), with or without quotes
        $html = preg_replace('#\s+on[a-z0-9_-]+\s*=\s*(?:\'[^\']*\'|"[^"]*"|[^\s>]+)#is', '', $html);

        // 4. Remove inline style attributes to prevent CSS injection / exfiltration
        $html = preg_replace('#\s+style\s*=\s*(?:\'[^\']*\'|"[^"]*"|[^\s>]+)#is', '', $html);

        // 5. Sanitize href attributes (only allow http://, https://, /, mailto:, tel:)
        $html = preg_replace_callback('#\s+href\s*=\s*(?:\'([^\']*)\'|"([^"]*)"|([^\s>]+))#is', function ($match) {
            $url = trim($match[1] ?: ($match[2] ?: ($match[3] ?? '')));
            $decoded = html_entity_decode(urldecode($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cleanUrl = preg_replace('/[\x00-\x1F\x7F]/u', '', $decoded);

            if (preg_match('#^(https?://|/|mailto:|tel:)#i', $cleanUrl) && ! preg_match('#^(javascript|data|vbscript):#i', $cleanUrl)) {
                return ' href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"';
            }

            return '';
        }, $html);

        // 6. Sanitize src attributes (only allow http://, https://, /)
        $html = preg_replace_callback('#\s+src\s*=\s*(?:\'([^\']*)\'|"([^"]*)"|([^\s>]+))#is', function ($match) {
            $url = trim($match[1] ?: ($match[2] ?: ($match[3] ?? '')));
            $decoded = html_entity_decode(urldecode($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cleanUrl = preg_replace('/[\x00-\x1F\x7F]/u', '', $decoded);

            if (preg_match('#^(https?://|/)#i', $cleanUrl) && ! preg_match('#^(javascript|data|vbscript):#i', $cleanUrl)) {
                return ' src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"';
            }

            return '';
        }, $html);

        // 7. Strip tags that are not in the allowed list
        return trim(strip_tags($html, self::ALLOWED_TAGS));
    }
}

