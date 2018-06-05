<?php



if (! Route::has('home')) {
    /**
     * Get front “/” route.
     *
     * @var \Illuminate\Routing\Route
     */
    $route = array_get(Route::getRoutes()->get('GET'), '/');

    // Not defined "/" route,
    // Create a default "/" route.
    if (! $route) {
        $route = Route::redirect("/","admin");//Route::get('/', 'HomeController@index');
    }

    // Set "/" route name as "home"
    $route->name('home');
}

if (! Route::has('login')) {
    Route::get('/auth/login', 'Auth\\LoginController@showLoginForm')->name('login');
}

if (! Route::has('logout')) {
    Route::any('auth/logout', 'Auth\\LoginController@logout')->name('logout');
}

Route::post('/auth/login', 'Auth\\LoginController@login');

Route::prefix('admin')
    ->namespace('Admin')
    ->group(base_path('routes/admin.php'));
