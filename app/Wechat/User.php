<?php

namespace App\Wechat;

use Illuminate\Support\Facades\Cache;

class User
{
    public function remark()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=" . $access_token;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['body' => '{ "openid":"oYPVm6PPgv919I2I6N1TZmyqkeIo", "remark":"AilonHuang" }']);
        dd(json_decode($res->getBody(), true));
    }

    public function list()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $access_token . "&next_openid=";
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $url);
        dd(json_decode($res->getBody(), true));
    }

    public function info($openid)
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $openid ."&lang=zh_CN";
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $url);
        return json_decode($res->getBody(), true);
    }

    public function batchInfo()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=" . $access_token;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['body' => '{
    "user_list": [
        {
            "openid": "oYPVm6PPgv919I2I6N1TZmyqkeIo", 
            "lang": "zh_CN"
        }, 
        {
            "openid": "oYPVm6Ey41aEVJ_jr7SIlePIhHIE", 
            "lang": "zh_CN"
        }
    ]
}']);
        dd(json_decode($res->getBody(), true));
    }
}