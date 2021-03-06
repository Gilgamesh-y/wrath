<?php

$app = new Src\App(dirname(__DIR__));

$app->initializeServices([
    Src\Log\Provider\LogServiceProvider::class,
    Src\Event\Provider\EventServiceProvider::class,
    Src\Dispatcher\Provider\RouteTableServiceProvider::class,
    Src\Dispatcher\Provider\DispatchServiceProvider::class,
    Src\Middleware\Provider\MiddlewareServerProvider::class,
    Src\Redis\Provider\RedisServiceProvider::class,
    Src\Database\Provider\DatabaseServerProvider::class,
    Src\Pool\Provider\PoolServerProvider::class,
    Src\Dispatcher\Provider\HttpServiceProvider::class,
    Src\Dispatcher\Provider\WebSocketServiceProvider::class,
    Src\RPC\Provider\RpcServiceProvider::class,
    Src\RPCServer\Provider\RpcServerServiceProvider::class,
    Src\RPCClient\Provider\RpcClientServiceProvider::class,
    Src\MQ\Provider\MQServiceProvider::class,
    Src\Elasticsearch\Provider\ElasticsearchProvider::class
]);

return $app;

