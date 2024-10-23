<?php

namespace src\views\components;

use src\dao\PaginationMetaDao;

class PaginationComponent
{
    public static function renderPagination(
        PaginationMetaDao $meta,
        $currentUri,
    ) {
        // Variables
        $currentPage = $meta->getCurrentPage();
        $totalPages = $meta->getTotalPage();
        $hasPrev = $meta->hasPrevPage();
        $hasNext = $meta->hasNextPage();

        $startPage = max(1, min($currentPage - 2, $totalPages - 4));
        $endPage = min($totalPages, max(5, $currentPage + 2));
        if ($totalPages < 5) {
            $startPage = 1;
            $endPage = $totalPages;
        }

        $prevAnchorClass = $hasPrev ? '' : ' anchor-disabled';
        $prevButtonAttribute = $hasPrev ? '' : 'disabled';
        $prevAnchorHref = $hasPrev ? PaginationComponent::buildUrl($currentUri, $currentPage - 1) : '#';

        $nextAnchorClass = $hasNext ? '' : ' anchor-disabled';
        $nextButtonAttribute = $hasNext ? '' : 'disabled';
        $nextAnchorHref = $hasNext ? PaginationComponent::buildUrl($currentUri, $currentPage + 1) : '#';

        $pageButtons = '';
        for ($i = $startPage; $i <= $endPage; $i++) {
            $buttonClass = ($i === $currentPage) ? 'button--default-color' : 'button--outline';
            $buttonHref = PaginationComponent::buildUrl($currentUri, $i);

            $pageButtons .= <<<HTML
            <li class="page-item">
                <a class="page-link" href="{$buttonHref}">
                    <button class="button {$buttonClass} button--icon">
                        {$i}
                    </button>    
                </a>
            </li>
            HTML;
        }

        return <<<HTML
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item">
                    <a class="page-link {$prevAnchorClass}" href="{$prevAnchorHref}" aria-label="Previous Anchor">
                        <button class="button button--icon button--outline" {$prevButtonAttribute} aria-label="Previous Button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left icon--sm"><path d="m15 18-6-6 6-6"/></svg>                        
                        </button>
                    </a>
                </li>

                {$pageButtons}


                <li class="page-item">
                    <a class="page-link {$nextAnchorClass}" href="{$nextAnchorHref}" aria-label="Next Anchor">
                        <button class="button button--icon button--outline" {$nextButtonAttribute} aria-label="Next Button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right icon--sm"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>
        HTML;
    }


    public static function buildUrl($baseUrl, $page)
    {
        $parsedUrl = parse_url($baseUrl);
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
        parse_str($query, $queryParams);
        $queryParams['page'] = $page;
        $newQuery = http_build_query($queryParams);
        $parsedUrl['query'] = $newQuery;
        return PaginationComponent::unparse_url($parsedUrl);
    }

    public static function unparse_url($parsed_url)
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
