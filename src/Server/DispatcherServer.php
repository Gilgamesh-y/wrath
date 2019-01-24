<?php

namespace Src\Server;

use Src\App;
use App\Kernel;
use Src\Support\Core;
use Src\Helper\ErrorHelper;
use Src\Server\RequestServer;
use Src\Server\ResponseServer;
use Src\Resource\AnnotationResource;
use Src\Support\Contexts\RequestContext;

class DispatcherServer
{
    private $app;

    public function __construct(Core $app)
    {
        $this->app = $app;
        // Get routes
        $bootScan = $this->app->get('config')->get('app.bootScan');
        $resource = new AnnotationResource($bootScan);
        $resource->scanNamespace();
        $resource->getDefinitions();
    }

    public function handle(RequestServer $request, ResponseServer $response)
    {
        $this->beforeDispatch($request, $response);
        $request = RequestContext::getRequest();
        $table = $this->app->get('routeTable');
        $replace_uri = preg_replace('/\d+/i', '{}', $request->request->server['request_uri']);
        $type = strtolower($request->request->server['request_method']);
        $routes = $table->all();
        switch (isset($routes[$type . '@' . $replace_uri])) {
            case true:
                return $this->dispatch($request, $response, $routes[$type . '@' . $replace_uri]);
            case false:
                return error(ErrorHelper::ROUTE_ERROR_CODE, ErrorHelper::ROUTE_ERROR_MSG);
        }
    }

    public function dispatch(RequestServer $request, ResponseServer $response, $route)
    {
        preg_match('/\d+/i', $request->request->server['request_uri'], $params);
        $kernel = new Kernel($this->app);
        $kernel->bootstrap();
        $middleware = $kernel->getMiddleware();
        $middleware = array_filter($middleware + [$kernel->getRouteMiddleware($route['middleware'])]);
        $destination = $this->getDestination($request, $response, $route['controller'], $route['method'], end($params));

        // Execution middleware
        $pipeline = array_reduce(
            array_reverse($middleware),
            $this->getInitialSlice(),
            $this->prepareDestination($destination)
        );

        return $pipeline($request);
    }

    // Get Controller Closure
    public function getDestination(RequestServer $request, ResponseServer $response, string $controller, string $action, string $paraData)
    {
        return function () use ($controller, $request, $response, $action, $paraData) {
            return call_user_func_array([new $controller($request, $response), $action], [$paraData]);
        };
    }

    protected function getInitialSlice()
    {
        return function ($stack, $pipe) {
            return function (RequestServer $request) use ($stack, $pipe) {
                return (new $pipe())->handle($request, $stack);
            };
        };
    }

    protected function prepareDestination(\Closure $destination)
    {
        return function (RequestServer $request) use ($destination) {
            return $destination($request);
        };
    }

    protected function beforeDispatch(RequestServer $request, ResponseServer $response)
    {
        RequestContext::setRequest($request);
        RequestContext::setResponse($response);
    }
}
