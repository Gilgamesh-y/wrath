<?php

namespace Src\Provider;

use Swoole\WebSocket\Server;
use Src\Event\WebSocketEvent;

class WebSocketServiceProvider extends AbstractProvider
{
    protected $onList = [
        'onOpen' => 'Open',
        'onMessage' => 'Message',
        'onClose' => 'Close',
        'onTask' => 'Task',
        'onFinish' => 'Finish',
    ];

    public function register()
    {
        $this->app->set('ws', function () {
            $config = $this->app->get('config')->get('app.ws');
            $server = new Server($config['host'], $config['port']);
            $server->set($config['set']);
            $ws = new WebSocketEvent($this->app);
            foreach ($this->onList as $function => $event) {
                $server->on($event, [$ws, $function]);
            }

            return $server;
        });
    }
}
