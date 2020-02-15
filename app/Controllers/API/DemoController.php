<?php

namespace App\Controllers\API;

use Protos\User;
use App\Models\ChatLog;
use App\Controllers\Controller;
use App\Events\RegisterEvent;
use App\Models\Administrator;
use App\Models\User as ModelsUser;
use App\Services\ChatRedisService;
use Src\App;
use Src\RPCClient\RPCClient;

class DemoController extends Controller
{
    /**
     * @Get('/api/rpc')
     */
    public function index()
    {
        $user = new User();
        $user->setId(1);
        $user->setName("ysama");
        $client = new RPCClient;
        $client->send('blog', $user);
        $res = $client->recv();
        $client->close();
        return success($res);
    }

    /**
     * @Get('/api/event')
     */
    public function evnet()
    {  
        event(new RegisterEvent(new ModelsUser));

        return success();
    }

    /**
     * @Get('/api/model')
     */
    public function model()
    {  
        return success(Administrator::get());
    }
}
