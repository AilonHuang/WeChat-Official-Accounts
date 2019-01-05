<?php

namespace App\Wechat;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

    public function list($next_openid = NULL)
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $access_token . "&next_openid=" . $next_openid;
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $url);
        $list = json_decode($res->getBody(), true);
        if ($list["count"] == 10000) {
            $new = $this->list($next_openid = $list["next_openid"]);
            $list["data"]["openid"] = array_merge_recursive($list["data"]["openid"], $new["data"]["openid"]); //合并OpenID列表
        }
        return $list;
    }

    public function info($openid)
    {
        $access_token = Cache::get(env('WECHAT_APPID'));
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
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

    public function syncUsers()
    {
        $user_list = $this->list();
        for ($i = 0; $i < count($user_list["data"]["openid"]); $i++) {
            $openid = $user_list["data"]["openid"][$i];
            DB::table('wx_user')->insert(
                ['openid' => $openid]
            );
        }
        echo "over";
    }

    public function updateUserInfo()
    {
        $result = DB::table('wx_user')->where('subscribe_time', '=', NULL)->first();
        $sexes = array("", "男", "女");
        if ($result) {
            $openid = $result->openid;
            $info = $this->info($openid);
            DB::table('wx_user')->where('openid', '=', $openid)->update([
                'nickname' => $info['nickname'],
                'sex' => $sexes[$info['sex']],
                'country' => $info['country'],
                'province' => $info['province'],
                'city' => $info['city'],
                'headimgurl' => $info['headimgurl'],
                'subscribe_time' => date('Y-m-d H:i:s', $info['subscribe_time'])
            ]);
        } else {
            echo "over";
        }
    }
}