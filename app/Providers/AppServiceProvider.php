<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (\Illuminate\Support\Facades\App::environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        view()->composer('*', function ($view) {
            if (!isset($view->globalCategories)) {
                try {
                    $categories = \App\Models\Category::whereNull('parent_id')
                        ->where('status', 'active')
                        ->with(['children' => function($q) {
                            $q->where('status', 'active')->with(['children' => function($q2) {
                                $q2->where('status', 'active');
                            }]);
                        }])
                        ->get();
                    $view->with('globalCategories', $categories);
                } catch (\Exception $e) {
                    $view->with('globalCategories', collect([]));
                }
            }
        });
    }
}
