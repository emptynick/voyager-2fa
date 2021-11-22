<?php

namespace Emptynick\TwoFA;

use Illuminate\Support\ServiceProvider;
use Voyager\Admin\Manager\Plugins as PluginManager;

class TwoFAServiceProvider extends ServiceProvider
{
    public function boot(PluginManager $pluginmanager)
    {
        $pluginmanager->addPlugin(\Emptynick\TwoFA\TwoFA::class);
    }

    public function register()
    {
        
    }
}