<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }


    public function boot(): void
    {
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email . $request->ip());
        });
        config([
            'auth.failed' => 'ログイン情報が登録されていません',
        ]);
        config([
            'validation.custom.email.required' => 'メールアドレスを入力してください',
            'validation.custom.password.required' => 'パスワードを入力してください',
            'validation.custom.email.email' => 'メール形式で入力してください',
        ]);
    }
}