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
                        ->where(function($q) {
                            $q->where('status', 'active')->orWhereNull('status');
                        })
                        ->with(['children' => function($q) {
                            $q->where(function($cq) {
                                $cq->where('status', 'active')->orWhereNull('status');
                            })->orderBy('name', 'asc')->with(['children' => function($cq2) {
                                $cq2->where(function($cq3) {
                                    $cq3->where('status', 'active')->orWhereNull('status');
                                })->orderBy('name', 'asc');
                            }]);
                        }])
                        ->orderBy('name', 'asc')
                        ->get()
                        ->unique(function ($cat) {
                            return strtolower(trim($cat->name ?? ''));
                        })
                        ->values();

                    $view->with('globalCategories', $categories);
                } catch (\Exception $e) {
                    $view->with('globalCategories', collect([]));
                }
            }
        });
    }
}
