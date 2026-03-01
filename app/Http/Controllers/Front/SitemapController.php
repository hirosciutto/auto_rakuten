<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Response;

/**
 * Google 等のクローラ向けに sitemap.xml を出力する。
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $siteId = (int) config('cosmetica.site_id', 1);
        $site = Site::find($siteId);

        $urls = [];

        $latestPostUpdatedAt = null;
        if ($site) {
            $raw = Post::forSite($site)->max('updated_at');
            if ($raw) {
                $latestPostUpdatedAt = Carbon::parse($raw)->setTimezone('Asia/Tokyo')->format('c');
            }
        }

        $staticPages = [
            '/' => $latestPostUpdatedAt,
        ];
        foreach ($staticPages as $path => $lastmod) {
            $urls[] = $this->urlEntry($base . $path, $lastmod);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
            . implode("\n", $urls) . "\n"
            . '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    private function urlEntry(string $loc, ?string $lastmod): string
    {
        $loc = htmlspecialchars($loc, ENT_XML1, 'UTF-8');
        $lastmodTag = $lastmod !== null
            ? "\n  <lastmod>{$lastmod}</lastmod>"
            : '';
        return "  <url>\n  <loc>{$loc}</loc>{$lastmodTag}\n  </url>";
    }
}
