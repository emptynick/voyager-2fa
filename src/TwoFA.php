<?php

namespace Emptynick\TwoFA;

use Voyager\Admin\Contracts\Plugins\AuthenticationPlugin;
use Voyager\Admin\Contracts\Plugins\Features\Provider\{JS};

class TwoFA implements AuthenticationPlugin, JS
{
    public $name = 'Voyager 2FA';
    public $description = 'Two-factor authentication for Voyager II';
    public $repository = 'emptynick/voyager-2fa';
    public $website = 'https://github.com/emptynick/voyager-2fa';
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
