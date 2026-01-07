<?php

namespace App\Addons\HostyStats;

use Illuminate\Support\ServiceProvider;

use App\Addons\HostyStats\Controllers\DashboardController;
use App\Addons\HostyStats\Controllers\CategoryController;
use App\Addons\HostyStats\Controllers\MonitorController;

class HostyStatsServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (!is_installed()) {
            return;
        }

        $base = base_path('addons/hostystats');

        $this->loadViewsFrom($base.'/views', 'hostystats');
        $this->loadTranslationsFrom($base.'/lang', 'hostystats');
        $this->loadMigrationsFrom($base.'/database/migrations');

        
        if ($this->app->bound('settings')) {

            
            $this->app['settings']->addCard(
                'hostystats', 
                'HostyStats',
                "Surveillance uptime : disponibilité, latence, HTTP, incidents et maintenance — avec une vue admin + une page statut client.",
                40,           
                null,
                true,
                3
            );

            
            $this->app['settings']->addCardItem(
                'hostystats',
                'hostystats_dashboard',
                'Dashboard',
                "Vue globale des sondes : KPIs, état effectif, dernières mesures et accès rapide aux actions.",
                'bi bi-speedometer2',
                [DashboardController::class, 'index'],
                null
            );

            $this->app['settings']->addCardItem(
                'hostystats',
                'hostystats_categories',
                'Catégories',
                "Gérer les catégories : tri, activation, position d’affichage côté statut.",
                'bi bi-folder2-open',
                [CategoryController::class, 'index'],
                null
            );

            $this->app['settings']->addCardItem(
                'hostystats',
                'hostystats_monitors',
                'Sondes',
                "Gérer les sondes : type (HTTP/PING/TCP), cible, intervalles, seuils et statut forcé.",
                'bi bi-broadcast',
                [MonitorController::class, 'index'],
                null
            );
        }

        
        if (file_exists($base.'/routes/web.php')) require $base.'/routes/web.php';
        if (file_exists($base.'/routes/admin.php')) require $base.'/routes/admin.php';
        if (file_exists($base.'/routes/api.php')) require $base.'/routes/api.php';

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Addons\HostyStats\Console\HostyStatsCheckCommand::class,
            ]);
        }
    }
}
