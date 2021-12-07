<?php

namespace Emptynick\TwoFA;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Event};
use PragmaRX\Google2FALaravel\Facade as TwoFactor;
use Illuminate\Support\Facades\Route;

use Voyager\Admin\Classes\UserMenuItem;
use Voyager\Admin\Contracts\Plugins\AuthenticationPlugin;
use Voyager\Admin\Contracts\Plugins\Features\Provider\{JS, ProtectedRoutes, Settings};
use Voyager\Admin\Facades\Voyager;
use Voyager\Admin\Manager\Menu as MenuManager;
use Voyager\Admin\Plugins\AuthenticationPlugin as BaseAuthPlugin;

use Emptynick\TwoFA\Provider\{Settings as SettingsProvider, Routes as RoutesProvider};

class TwoFA extends BaseAuthPlugin implements AuthenticationPlugin, JS, ProtectedRoutes, Settings
{
    use SettingsProvider,
        RoutesProvider;

    private bool $registered = false;

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

    public function loginComponent(): ?string
    {
        return 'voyager-2fa-login';
    }

    public function authenticate(Request $request): ?array
    {
        if (!$request->get('email', null) || !$request->get('password', null)) {
            return [ __('voyager::auth.error_field_empty') ];
        } else if ($request->has('otp') && empty($request->otp)) {
            return [ __('2fa::2fa.otp_empty') ];
        }

        if (Auth::validate($request->only('email', 'password'))) {
            // Credentials are good
            $user = $this->getUserModel()->where('email', $request->get('email'))->firstOrFail();

            if ($request->has('otp')) {
                if (!TwoFactor::verifyKey($user->{$this->get2FAField()}, $request->input('otp'))) {
                    // 2FA code wrong
                    return [ __('2fa::2fa.otp_wrong') ];
                }
                // Code is correct. Login normally
            } else {
                $secret = $user->{$this->get2FAField()};
                if (!is_null($secret) && !empty($secret)) {
                    // Has to enter code
                    return [ __('2fa::2fa.enter_otp') ];
                }
                // 2FA not activated. Login normally
            }
        }

        if (Auth::attempt($request->only('email', 'password'), $request->has('remember'))) {
            $request->session()->regenerate();
            return null;
        }

        return [ __('voyager::auth.login_failed') ];
    }

    public function handleRequest(Request $request, Closure $next): mixed
    {
        if (!$this->registered) {
            auth()->setDefaultDriver($this->guard());
            $this->registered = true;
            Event::dispatch('voyager.auth.registered', $this);
            $this->registerUserMenuItems();
        }

        if ($this->user() && !Auth::guest()) {
            if (!TwoFactor::isActivated()) {
                if (Route::currentRouteName() !== 'voyager.voyager-manage-2fa') {
                    if (Voyager::setting('2FA.force_2fa', false)) {
                        return redirect()->route('voyager.voyager-manage-2fa');
                    }
                    if (Voyager::setting('2FA.show_warning', true)) {
                        Voyager::flashMessage(__('2fa::2fa.activate_message', ['url' => route('voyager.voyager-manage-2fa')]), 'yellow');
                    }
                }
            }
            if (Voyager::authorize($this->user(), 'browse', ['voyager'])) {
                return $next($request);
            }
        }

        return redirect()->guest(route('voyager.login'));
    }

    private function get2FAField() {
        return config('google2fa.otp_secret_column', 'google2fa_secret');
    }

    private function getUserModel() {
        return app(config('auth.providers.'.config('guards.'.$this->guard().'.provider', 'users').'.model', null));
    }

    private function registerUserMenuItems() {
        app(MenuManager::class)->addItems(
            (new UserMenuItem(__('voyager::generic.dashboard')))->route('voyager.dashboard'),
            (new UserMenuItem('Manage 2FA'))->route('voyager.voyager-manage-2fa'),
            (new UserMenuItem(__('voyager::auth.logout')))->route('voyager.logout')
        );
    }
}
