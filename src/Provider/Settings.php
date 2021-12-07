<?php

namespace Emptynick\TwoFA\Provider;

trait Settings {
    public function provideSettings(): array
    {
        return [
            [
                'type'          => 'toggle',
                'group'         => '2FA',
                'name'          => 'Force 2FA',
                'key'           => 'force_2fa',
                'value'         => false,
                'translatable'  => false,
                'info'          => 'Prevents the user to access any page before activating 2FA',
                'options'       => [],
                'validation'    => [],
            ],
            [
                'type'          => 'toggle',
                'group'         => '2FA',
                'name'          => 'Show warning',
                'key'           => 'show_warning',
                'value'         => true,
                'translatable'  => false,
                'info'          => 'Show a message to enable 2FA on every page visit',
                'options'       => [],
                'validation'    => [],
            ],
            [
                'type'          => 'toggle',
                'group'         => '2FA',
                'name'          => 'Allow disabling 2FA',
                'key'           => 'allow_disabling',
                'value'         => true,
                'translatable'  => false,
                'info'          => 'Allow users to disable 2FA',
                'options'       => [],
                'validation'    => [],
            ]
        ];
    }
}