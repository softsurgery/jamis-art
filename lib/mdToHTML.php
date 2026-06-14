<?php

function resolveMediaUrl($url, $basePath = '../../')
{
    if (preg_match('#^https?://#i', $url) || str_starts_with($url, '/')) {
        return $url;
    }

    if (str_starts_with($url, 'storage/')) {
        return $basePath . $url;
    }

    return $url;
}

function markdownToHtml($markdown, $mediaBasePath = '../../')
{
    // Preserve existing HTML blocks
    $placeholders = [];

    $markdown = preg_replace_callback(
        '/<(div|section|article|aside|figure|table|ul|ol|li|blockquote|pre|code|img|iframe|video|h[1-6]|p|span|a)[^>]*>.*?<\/\1>/is',
        function ($matches) use (&$placeholders) {
            $key = '%%HTMLBLOCK_' . count($placeholders) . '%%';
            $placeholders[$key] = $matches[0];
            return $key;
        },
        $markdown
    );

    // Escape markdown content only
    $html = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');

    // Headers (reduced spacing)
    $html = preg_replace(
        '/^### (.*?)$/m',
        '<h3 class="text-lg font-bold text-white mt-3 mb-1">$1</h3>',
        $html
    );

    $html = preg_replace(
        '/^## (.*?)$/m',
        '<h2 class="text-xl font-bold text-white mt-4 mb-2">$1</h2>',
        $html
    );

    $html = preg_replace(
        '/^# (.*?)$/m',
        '<h1 class="text-2xl font-bold text-white mt-5 mb-2">$1</h1>',
        $html
    );

    // Code blocks
    $html = preg_replace_callback(
        '/```(?:([a-zA-Z0-9_-]+))?\n(.*?)```/s',
        function ($matches) {
            $code = htmlspecialchars(trim($matches[2]), ENT_QUOTES, 'UTF-8');

            return sprintf(
                '<pre class="bg-gray-900 border border-white/10 rounded-md p-3 overflow-x-auto my-3"><code class="text-gray-300 text-sm font-mono">%s</code></pre>',
                $code
            );
        },
        $html
    );

    // Inline code
    $html = preg_replace(
        '/`([^`]+)`/',
        '<code class="bg-gray-800 text-red-400 px-1.5 py-0.5 rounded text-xs font-mono">$1</code>',
        $html
    );

    // Bold / italic
    $html = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<strong><em>$1</em></strong>', $html);
    $html = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $html);

    // Linked images: [![alt](src)](href)
    $html = preg_replace_callback(
        '/\[!\[([^\]]*)\]\(([^)]+)\)\]\(([^)]+)\)/',
        function ($matches) use ($mediaBasePath) {
            $alt = $matches[1];
            $src = htmlspecialchars(resolveMediaUrl($matches[2], $mediaBasePath), ENT_QUOTES, 'UTF-8');
            $href = htmlspecialchars(resolveMediaUrl($matches[3], $mediaBasePath), ENT_QUOTES, 'UTF-8');

            return sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="block my-4"><img src="%s" alt="%s" class="rounded-lg max-w-full h-auto border border-white/10" loading="lazy"></a>',
                $href,
                $src,
                $alt
            );
        },
        $html
    );

    // Images: ![alt](src)
    $html = preg_replace_callback(
        '/!\[([^\]]*)\]\(([^)]+)\)/',
        function ($matches) use ($mediaBasePath) {
            $alt = $matches[1];
            $src = htmlspecialchars(resolveMediaUrl($matches[2], $mediaBasePath), ENT_QUOTES, 'UTF-8');

            return sprintf(
                '<img src="%s" alt="%s" class="rounded-lg max-w-full h-auto my-4 border border-white/10" loading="lazy">',
                $src,
                $alt
            );
        },
        $html
    );

    // Links
    $html = preg_replace(
        '/\[(.*?)\]\((.*?)\)/',
        '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-red-400 hover:text-red-300 underline">$1</a>',
        $html
    );

    // Blockquotes
    $html = preg_replace(
        '/^&gt;\s?(.*?)$/m',
        '<blockquote class="border-l-2 border-red-500 pl-3 italic text-gray-400 my-2">$1</blockquote>',
        $html
    );

    // Lists
    $html = preg_replace(
        '/^[\s]*[-*]\s+(.*?)$/m',
        '<li>$1</li>',
        $html
    );

    $html = preg_replace_callback(
        '/(?:<li>.*?<\/li>\s*)+/s',
        function ($matches) {
            return '<ul class="list-disc pl-5 my-2 space-y-1">' .
                $matches[0] .
                '</ul>';
        },
        $html
    );

    // Paragraphs
    $blocks = preg_split('/\n{2,}/', $html);

    $html = implode('', array_map(function ($block) {
        $block = trim($block);

        if (
            preg_match('/^<(h[1-6]|ul|ol|li|pre|blockquote|div|table|img|a)/i', $block)
        ) {
            return $block;
        }

        return '<p class="text-gray-300 leading-6 mb-2">' .
            nl2br($block) .
            '</p>';
    }, $blocks));

    // Restore original HTML blocks
    $html = str_replace(
        array_keys($placeholders),
        array_values($placeholders),
        $html
    );

    return $html;
}
