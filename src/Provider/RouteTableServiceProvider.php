<?php

namespace Src\Provider;

use Src\App;
use Src\Server\RouteTableServer;

class RouteTableServiceProvider extends AbstractProvider
{
    protected $serviceName = 'routeTableServer';

    public function register()
    {
        $this->app->set($this->serviceName, function () {
            return new RouteTableServer();
        });
    }
}