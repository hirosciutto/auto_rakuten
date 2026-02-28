<?php

use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\Front\CosmeticaController::class, 'index'])->name('cosmetica.home');
Route::get('/cosmetica', [App\Http\Controllers\Front\CosmeticaController::class, 'index']);
Route::get('/cosmetica/items', [App\Http\Controllers\Front\CosmeticaController::class, 'items'])->name('cosmetica.items');

// クローラ向け（Google 等）
Route::get('/sitemap.xml', [App\Http\Controllers\Front\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    $base = rtrim(config('app.url'), '/');
    return response(
        "User-agent: *\nDisallow:\n\nSitemap: {$base}/sitemap.xml\n",
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
})->name('robots');

/*
|--------------------------------------------------------------------------
| Voyager Admin Routes
|--------------------------------------------------------------------------
*/
if (app()->environment('production')) {
    Route::any('admin/{any?}', fn () => abort(404))->where('any', '.*');
} else {
    Route::group(['prefix' => 'admin', 'middleware' => ['web']], function () {
        Route::post('ajax/status-update', [App\Http\Controllers\AjaxController::class, 'statusUpdate'])
            ->name('voyager.ajax.status-update')
            ->middleware('admin.user');

        Voyager::routes();
    });
}
