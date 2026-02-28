<?php

namespace App\Providers;

use App\View\Composers\StudentLayoutComposer;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer([
            'layouts.student',
            'dashboard.student',
            'dashboard.student-topics',
            'dashboard.student-quizzes',
            'dashboard.student-games',
            'dashboard.student-badges',
        ], StudentLayoutComposer::class);
    }
}
