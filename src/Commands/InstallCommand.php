<?php

namespace Emptynick\TwoFA\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = '2fa:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install two-factor-auth for Voyager II';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Publish and migrate migrations
        if ($this->confirm('Do you want to publish pragmarx/google2fa-laravel config file?', true)) {
            $this->call('vendor:publish', ['--provider' => \PragmaRX\Google2FALaravel\ServiceProvider::class]);
        }

        $this->info('Publishing migrations');
        $this->call('vendor:publish', ['--provider' => \Emptynick\TwoFA\TwoFAServiceProvider::class]);
        if ($this->confirm('Do you wish to migrate now? Skip this if you want to modify the migration file', true)) {
            $this->call('migrate');
            $this->info('Successfully migrated!');
        }

        $this->info('Everything done. You can re-run this command to repeat steps if needed.');
    }
}