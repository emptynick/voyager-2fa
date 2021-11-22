<?php

namespace Emptynick\Permissions;

use Voyager\Admin\Contracts\Plugins\AuthenticationPlugin;
use Voyager\Admin\Contracts\Plugins\Features\Provider\{JS};

class Permissions implements AuthenticationPlugin, JS
{
    public $name = 'Voyager Permissions';
    public $description = 'Permission system for Voyager II using spatie/laravel-permission';
    public $repository = 'emptynick/voyager-permissions';
    public $website = 'https://github.com/emptynick/voyager-permissions';
    public $version = '1.0.0';


    public function __construct()
    {
        $this->readme = realpath(dirname(__DIR__, 1).'/README.md');
    }

    public function provideJS() : string
    {
        return file_get_contents(realpath(dirname(__DIR__, 1).'/dist/voyager-2fa.umd.js'));
    }
}
