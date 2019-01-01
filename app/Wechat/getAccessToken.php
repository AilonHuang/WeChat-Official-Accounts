<?php

namespace App\Wechat;

use Illuminate\Support\Facades\Cache;

class getAccessToken
{
    public function __construct()
    {
        $appid = 'wxc606d7ddee11491e';
        $appsecret = '35e0ccaf34cb47a6e071fc72b3132d7b';

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $url);
        $result = json_decode($res->getBody(), true);
        Cache::add($appid, $result['access_token'], $result['expires_in']);
    }
}