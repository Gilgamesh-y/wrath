<?php

namespace Src\Event;

use Src\App;
use Src\Support\Core;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Src\Server\Redis\RedisManager;

class HttpEvent
{
    private $app;

    public function __construct(Core $app)
    {
        $this->app = $app;
    }

    /**
     * Execute when requested
     *
     * @param Swoole\Http\Request $swrequest
     * @param Swoole\Http\Response $swresponse
     * @return ResponseServer
     */
    public function onRequest(Request $swrequest, Response $swresponse)
    {
        $dispatcher = $this->app->get('dispatcher');
        $request = $this->app->get('request');
        $response = $this->app->get('response');
        $request->set($swrequest, $swresponse);
        $response->set($swresponse);

        $res = $dispatcher->handle($request, $response);

        $response->end($res);
    }

    /**
     * Execute when the server start
     *
     * @param Server $server
     */
    public function onStart(Server $server)
    {
        $redis = $this->app->get('php_redis');
        dd($redis->connection());
    }

    /**
     * Execute when the server close
     *
     * @param Server $server
     */
    public function onShutdown(Server $server)
    {
    }

    /**
     * Execute when the worker start
     *
     * @param Server $server
     */
    public function onWorkerStart(Server $server)
    {
    }

    /**
     * Execute when the worker stop
     *
     * @param Server $server
     * @param int $workerId
     */
    public function onWorkerStop(Server $server,int $workerId)
    {
    }

    /**
     * Execute when the browser connect
     *
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onConnect(Server $server,int $fd,int $reactorId)
    {
    }

    /**
     * Execute when the browser close
     *
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(Server $server,int $fd,int $reactorId)
    {
    }

    /**
     * @param Server $server
     * @param int $taskId
     * @param int $srcWorkerId
     * @param array $data
     * @return string
     */
    public function onTask(Server $server,int $taskId,int $srcWorkerId,array $data)
    {
    }

    /**
     * @param Server $server
     * @param int $taskId
     * @param $data
     * @return mixed
     */
    public function onFinish(Server $server,int $taskId,$data)
    {
    }
}