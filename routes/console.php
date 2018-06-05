<?php



use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

/*
 * app:key-generate command.
 */
Artisan::command('app:key-generate', function () {
    $this->call('key:generate', [
        '--force' => true,
    ]);
    $this->call('jwt:secret', [
        '--force' => true,
    ]);
})->describe('Run `key:generate` and `jwt:secret` commands.');
