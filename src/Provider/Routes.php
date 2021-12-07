<?php

namespace Emptynick\TwoFA\Provider;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use PragmaRX\Google2FALaravel\Facade as TwoFactor;
use Voyager\Admin\Facades\Voyager;

trait Routes {
    public function provideProtectedRoutes(): void
    {
        Route::get('manage-2fa', function (Request $request) {
            $key = TwoFactor::generateSecretKey();
            return Inertia::render('voyager-2fa-manage', [
                'twofakey'  => $key,
                'qr'        => str_replace(['#fefefe', '#000000'], ['none', 'currentcolor'], TwoFactor::getQRCodeInline(
                    config('app.name'),
                    $this->name(),
                    $key,
                )),
                'active'    => !empty($this->user()->{$this->get2FAField()}),
                'allowDisable' => Voyager::setting('2FA.allow_disabling', true),
            ])->withViewData('title', 'Manage 2FA');
        })->name('voyager-manage-2fa');

        Route::post('manage-2fa', function (Request $request) {
            if (!$request->get('enable', true)) {
                if (Voyager::setting('2FA.allow_disabling', true)) {
                    $this->user()->{$this->get2FAField()} = null;
                    $this->user()->save();

                    return true;
                } else {
                    abort(500, __('2fa::2fa.cannot_disable'));
                }
            } else if (!TwoFactor::verifyKey($request->input('key'), $request->input('code'))) {
                abort(500, __('2fa::2fa.otp_wrong'));
			} else {
                $this->user()->{$this->get2FAField()} = $request->input('key');
                $this->user()->save();

                return true;
            }
        })->name('voyager-manage-2fa');
    }
}