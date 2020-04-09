<?php

namespace  GarlicCMS\Modules\Core\Provider;

use Illuminate\Support\ServiceProvider;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Class ModuleServiceProvider extends ServiceProvider{
    
    public function register()
    {
        /*
         * Register other module providers
         */
        foreach (Config::get('cms.loadModules', []) as $module) {
            if (strtolower(trim($module)) != 'core') {
                App::register('\\' . $module . '\Provider\ServiceProvider');
            }
        }
    }
    /**
     * Bootstrap the module events.
     *
     * @return void
     */
    public function boot()
    {
        $this->applyDatabaseDefaultStringLength();

    }
     /**
     * Fix UTF8MB4 support for old versions of MariaDB (<10.2) and MySQL (<5.7)
     */
    protected function applyDatabaseDefaultStringLength()
    {
        if (Db::getDriverName() !== 'mysql') {
            return;
        }

        $defaultStrLen = Db::getConfig('varcharmax');

        if ($defaultStrLen === null && Db::getConfig('charset') === 'utf8mb4') {
            $defaultStrLen = 191;
        }

        if ($defaultStrLen !== null) {
            Schema::defaultStringLength((int) $defaultStrLen);
        }
    }
}