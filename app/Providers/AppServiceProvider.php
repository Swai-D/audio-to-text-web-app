<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\AssetHelper;

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

        // Register custom Blade directives
        Blade::directive('viteCss', function ($expression) {
            return "<?php echo App\Helpers\AssetHelper::viteCss($expression); ?>";
        });

        Blade::directive('viteJs', function ($expression) {
            return "<?php echo App\Helpers\AssetHelper::viteJs($expression); ?>";
        });

        Blade::directive('viteAsset', function ($expression) {
            return "<?php echo App\Helpers\AssetHelper::viteAsset($expression); ?>";
        });
    }
}
