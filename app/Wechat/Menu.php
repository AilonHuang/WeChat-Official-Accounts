<?php

namespace App\Wechat;

use Illuminate\Support\Facades\Cache;

class Menu
{
    public function createMenu()
    {
//         $button[] = array('name' => $this->bytes_to_emoji(0x1F340) . "基本类型",
//             'sub_button' => array(
//                 array('type' => "click",
//                     'name' => $this->bytes_to_emoji(0x1F439) . "文本和表情",
//                     'key' => "TEXT"
//                 ),
//                 array('type' => "click",
//                     'name' => $this->bytes_to_emoji(0x1F4F0) . "单图文",
//                     'key' => "SINGLENEWS"
//                 ),
//                 array('type' => "click",
//                     'name' => $this->bytes_to_emoji(0x1F420) . "多图文",
//                     'key' => "MULTINEWS"
//                 ),
//                 array('type' => "click",
//                     'name' => $this->bytes_to_emoji(0x1F3B5) . "音乐",
//                     'key' => "MUSIC"
//                 ),
//             )
//         );

//        $button[] = array('name' => $this->bytes_to_emoji(0x1F4CA) . "扫码发图",
//            'sub_button' => array(
//                array('type' => "scancode_waitmsg",
//                    'name' => "扫码带提示",
//                    'key' => "rselfmenu_2_1"
//                ),
//                array('type' => "scancode_push",
//                    'name' => "扫码推事件",
//                    'key' => "rselfmenu_2_2"
//                ),
//                array('type' => "pic_sysphoto",
//                    'name' => "系统拍照发图",
//                    'key' => "rselfmenu_2_3"
//                ),
//                array('type' => "pic_photo_or_album",
//                    'name' => "拍照或相册发图",
//                    'key' => "rselfmenu_2_4"
//                ),
//                array('type' => "pic_weixin",
//                    'name' => "微信相册发图",
//                    'key' => "rselfmenu_2_5"
//                ),
//            )
//        );

//         $button[] = array('name' => $this->bytes_to_emoji(0x3299) . "其他类型",
//             'sub_button' => array(
//                 array('type' => "view",
//                     'name' => "跳转网页",
//                     'url' => "http://xw.qq.com/"
//                 ),
//                 array('type' => "view",
//                     'name' => "第三方授权",
//                     'url' => "http://aunt.vipsinaapp.com/demo/oauth2/index.php"
//                 ),
//                 array('type' => "location_select",
//                     'name' => "发送位置",
//                     'key' => "SIGNIN"
//                 ),
//             )
//         );

        $button[] = ['type' => "scancode_waitmsg",
            'name' => "扫快递码",
            'key' => "rselfmenu_2_1"
        ];

        $button[] = [
            'type' => 'view',
            'name' => '我的信息',
            'url' => 'http://huangyilun.eicp.vip/userInfo'
        ];

        $access_token = Cache::get(env('WECHAT_APPID'));
        if (isset($matchrule) && !is_null($matchrule)) {
            foreach ($matchrule as $k => $v) {
                $matchrule[$k] = urlencode($v);
            }
            $data = array('button' => $button, 'matchrule' => $matchrule);
            $url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=" . $access_token;
        } else {
            $data = array('button' => $button);
            $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token;
        }
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, ['body' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
        return json_decode($res->getBody(), true);
    }

    function bytes_to_emoji($cp)
    {
        if ($cp > 0x10000) {       # 4 bytes
            return chr(0xF0 | (($cp & 0x1C0000) >> 18)) . chr(0x80 | (($cp & 0x3F000) >> 12)) . chr(0x80 | (($cp & 0xFC0) >> 6)) . chr(0x80 | ($cp & 0x3F));
        } else if ($cp > 0x800) {   # 3 bytes
            return chr(0xE0 | (($cp & 0xF000) >> 12)) . chr(0x80 | (($cp & 0xFC0) >> 6)) . chr(0x80 | ($cp & 0x3F));
        } else if ($cp > 0x80) {    # 2 bytes
            return chr(0xC0 | (($cp & 0x7C0) >> 6)) . chr(0x80 | ($cp & 0x3F));
        } else {                    # 1 byte
            return chr($cp);
        }
    }
}