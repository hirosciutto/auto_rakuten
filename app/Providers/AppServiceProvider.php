<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // publish した Voyager ビューを優先（resources/views/vendor/voyager を先に参照）
        View::getFinder()->prependNamespace('voyager', resource_path('views/vendor/voyager'));

        // BREAD 作成時、data_types フォームに config のデフォルト値をセット
        View::composer('voyager::tools.bread.edit-add', function ($view) {
            $data = $view->getData();
            if (empty($data['dataType'])) {
                $defaults = array_merge([
                    'id' => null,
                    'name' => null,
                    'slug' => null,
                    'icon' => null,
                    'model_name' => null,
                    'controller' => null,
                    'policy_name' => null,
                    'generate_permissions' => null,
                    'server_side' => null,
                    'order_column' => null,
                    'order_display_column' => null,
                    'order_direction' => null,
                    'default_search_key' => null,
                    'scope' => null,
                    'description' => null,
                ], config('voyager.bread.create_defaults', []));
                $view->with('dataType', (object) $defaults);
                $view->with('isModelTranslatable', false);
            }
        });
    }
}
