<?php

namespace App\Wechat;


use Illuminate\Support\Facades\Cache;

class Tag
{
    public function list()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=" . $access_token;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $url);
        dd(json_decode($res->getBody(), true));
    }
    public function store()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=" . $access_token;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['body' => '{   "tag" : {     "name" : "贵州"  } }']);
        return json_decode($res->getBody(), true);
    }

    public function update()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=" . $access_token;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['body' => '{   "tag" : {  "id": 100,   "name" : "北京"  } }']);
        dd(json_decode($res->getBody(), true));
    }

    public function destroy()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=" . $access_token;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['body' => '{   "tag" : {  "id": 100  } }']);
        dd(json_decode($res->getBody(), true));
    }
}