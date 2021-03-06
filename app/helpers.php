<?php

use Illuminate\Support\Facades\Http;

define('API_LOGIN', 'user@Webservice');
define('API_PASSWORD', 'UnitedCon$ultWS2021');

if (!function_exists('getBreadcrumb')) {
    function getBreadcrumb($key)
    {
        if (is_numeric($key)) {
            return $key;
        } else {
            $breadcrumbs = config('app.breadcrumbs');

            if (array_key_exists($key, $breadcrumbs))

                return $breadcrumbs[$key];

            else return null;
        }
    }
}

if (!function_exists('getToken')) {
    function getToken($login, $password)
    {
        $response = Http::get('https://app.centraltest.com/customer/REST/connect/json', [
            'login' => $login,
            'password' => $password
        ]);

        if ($response['token']) {
            return $response['token'];
        } else {
            abort(404, 'TOKEN is not accessed');
        }
    }
}
/* Hash password
* return hash password
*/
if (!function_exists('hash_password')) {
    function hash_password($pass, $userid)
    {
        $salt = md5($userid);
        return hash('sha256', $salt . $pass);
    }
}

// * password generator
if (!function_exists('keyGenerator')) {
    function keyGenerator()
    {
        return Str::upper(Str::random(1)) . Str::random(4) . rand(5, 10000);
    }
}
