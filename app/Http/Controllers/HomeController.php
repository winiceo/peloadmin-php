<?php

declare(strict_types=1);



namespace Leven\Http\Controllers;

use Jenssegers\Agent\Agent;

class HomeController
{
    /**
     * Home page.
     *
     * @param \Jenssegers\Agent\Agent $agent
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function welcome(Agent $agent)
    {
        // If request client is mobile and opened SPA.
        if ($agent->isMobile() && config('http.spa.open')) {
            return redirect(config('http.spa.url'));

        // If web is opened.
        } elseif (config('http.web.open')) {
            return redirect(config('http.web.url'));
        }

        // By default, view welcome page.
        return view('welcome');
    }
}
