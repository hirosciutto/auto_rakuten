<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
// use App\Models\Actress;
// use App\Models\Post;
// use App\Services\SiteService;
// use Carbon\Carbon;
// use Illuminate\Http\Response;

/**
 * Google 等のクローラ向けに sitemap.xml を出力する。
 */
class SitemapController extends Controller
{
    /**
    public function __construct(
        private SiteService $siteService,
    ) {}

    public function index(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $siteId = $this->siteService->getSiteId();

        $urls = [];

        // 記事（公開済みのみ）。updated_at で lastmod を出す
        $posts = Post::where('site_id', $siteId)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        foreach ($posts as $post) {
            $lastmod = $post->updated_at?->setTimezone('Asia/Tokyo')->format('c');
            $urls[] = $this->urlEntry($base . '/posts/' . $post->id, $lastmod);
        }

        // 固定ページ: トップ・記事一覧は直近の記事の updated_at、女優一覧は直近の actress の updated_at、その他は直近の記事
        $latestPostLastmod = $posts->isNotEmpty()
            ? $posts->max('updated_at')?->setTimezone('Asia/Tokyo')->format('c')
            : null;
        $latestActressUpdatedAt = Actress::max('updated_at');
        $latestActressLastmod = $latestActressUpdatedAt !== null
            ? Carbon::parse($latestActressUpdatedAt)->setTimezone('Asia/Tokyo')->format('c')
            : null;

        $staticPages = [
            '/' => $latestPostLastmod,
            '/posts' => $latestPostLastmod,
            '/actress' => $latestActressLastmod,
            '/genres' => $latestPostLastmod,
            '/campaign' => $latestPostLastmod,
            '/ranking' => $latestPostLastmod,
            '/search' => $latestPostLastmod,
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
    */
}
