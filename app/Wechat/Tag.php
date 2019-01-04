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

    public function batchUser()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=" . $access_token;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['body' => '{   "openid_list" : ["oYPVm6PPgv919I2I6N1TZmyqkeIo","oYPVm6Ey41aEVJ_jr7SIlePIhHIE"], "tagid" : 101 }']);
        dd(json_decode($res->getBody(), true));
    }

    public function users()
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=" . $access_token;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['body' => '{   "tagid" : 101,   "next_openid":""//第一个拉取的OPENID，不填默认从头开始拉取 }']);
        dd(json_decode($res->getBody(), true));
    }
}