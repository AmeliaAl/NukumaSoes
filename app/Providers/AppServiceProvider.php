<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\FormatHelper;

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
        // Blade directive: @rupiah($value)  →  Rp 15.000,00
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo \\App\\Helpers\\FormatHelper::rupiah($expression); ?>";
        });
    }
}
