<?php

namespace App\Http\Controllers;

use App\Wechat\CheckSignature;
use App\Wechat\getAccessToken;
use App\Wechat\Menu;
use App\Wechat\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeChatController extends Controller
{
    public function index(Request $request)
    {
        if (isset($request->echostr)) {
            if (new CheckSignature($request)) {
                echo $request->echostr;
            }
        } else {
            $this->responseMsg($request);
        }
    }

    public function remark()
    {
        (new User)->remark();
    }

    public function getAccessToken()
    {
        new getAccessToken();
    }

    public function responseMsg(Request $request)
    {
        //get post data, May be due to the different environments
        $postStr = $msg = $request->getContent();
        //extract post data
        if (!empty($postStr)) {
            $this->logger("R \r\n" . $postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE) {
                case 'event': // 事件
                    $result = $this->receiveEvent($postObj);
                    break;
                case 'text': // 文本
                    $result = $this->receiveText($postObj);
                    break;
                case 'image': // 图片
                    $result = $this->receiveImage($postObj);
                    break;
                case 'location': // 位置
                    $result = $this->receiveLocation($postObj);
                    break;
                case 'voice': // 语音
                    $result = $this->receiveVoice($postObj);
                    break;
                case 'video': // 视频
                case 'shortvideo':
                    $result = $this->receiveVideo($postObj);
                    break;
                case 'link': // 链接
                    $result = $this->receiveLink($postObj);
                    break;
            }
            $this->logger("T \r\n" . $result);
            echo $result;
        } else {
            echo "";
            exit;
        }
    }

    // 接收事件消息
    private function receiveEvent($object)
    {
        switch ($object->Event) {
            case 'subscribe':
                $content = '欢迎关注我，查询天气，发送天气加城市名，如“深圳天气”';
                break;
            case 'unsubscribe':
                $content = '取消关注';
                break;
            case "scancode_waitmsg":
                if ($object->ScanCodeInfo->ScanType == "barcode"){
                    $codeinfo = explode(",",strval($object->ScanCodeInfo->ScanResult));
                    $codeValue = $codeinfo[1];
                    $content = array();
                    $content[] = array("Title"=>"扫描成功",  "Description"=>"快递号：".$codeValue."\r\n点击查看快递进度详情", "PicUrl"=>"", "Url" =>"m.kuaidi100.com/result.jsp?nu=".$codeValue);
                }else{
                    $content = "不是条码";
                }
                break;
            default:
                $content = 'receive a new event: ' . $object->Event;
        }
        if (is_array($content)) {
            $result = $this->transmitNews($object, $content);
        } else {
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收文本消息
    private function receiveText($object)
    {
        $keyword = trim($object->Content);

        //自动回复模式
        if (strstr($keyword, "文本")) {
            $content = "这是个文本消息";
        } else if (strstr($keyword, "表情")) {
            $content = "微笑：/::)\n乒乓：/:oo\n";
        } else if (strstr($keyword, "天气")) {
            $city = str_replace('天气', '', $keyword);
            $content = $this->getWeatherInfo($city);
        } else if (strstr($keyword, "单图文")) {
            $content = array();
            $content[] = array("Title" => "单图文标题", "Description" => "单图文内容", "PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
        } else if (strstr($keyword, "图文") || strstr($keyword, "多图文")) {
            $content = array();
            $content[] = array("Title" => "多图文3标题", "Description" => "Description", "PicUrl" => "http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
            $content[] = array("Title" => "多图文3标题", "Description" => "Description", "PicUrl" => "http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
            $content[] = array("Title" => "多图文3标题", "Description" => "Description", "PicUrl" => "http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
        } else if (strstr($keyword, "音乐")) {
            $content = array();
            $content = array("Title" => "最炫民族风", "Description" => "歌手：凤凰传奇", "MusicUrl" => "http://mascot-music.stor.sinaapp.com/zxmzf.mp3", "HQMusicUrl" => "http://mascot-music.stor.sinaapp.com/zxmzf.mp3");
        } else {
            $content =  $this->getJokeInfo();
        }

        if (is_array($content)) {
            if (isset($content[0])) {
                $result = $this->transmitNews($object, $content);
            } else if (isset($content['MusicUrl'])) {
                $result = $this->transmitMusic($object, $content);
            }
        } else {
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId" => $object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，经度为：" . $object->Location_Y . "；纬度为：" . $object->Location_X . "；缩放级别为：" . $object->Scale . "；位置为：" . $object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)) {
            $content = "你刚才说的是：" . $object->Recognition;
            $result = $this->transmitText($object, $content);
        } else {
            $content = array("MediaId" => $object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId" => $object->MediaId, "ThumbMediaId" => $object->ThumbMediaId, "Title" => "", "Description" => "");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)) {
            return "";
        }

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);

        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return "";
        }

        //多图文转文本回复
        if (count($newsArray) > 1){
            $content = "";
            foreach ($newsArray as &$item) {
                $content .= "\n\n".(empty($item["Url"]) ? $item["Title"] : "<a href='".$item["Url"]."'>".$item["Title"]."</a>");
            }
            $result = $this->transmitText($object, trim($content));
            return $result;
        }

        $itemTpl = "        <item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
        </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <ArticleCount>%s</ArticleCount>
    <Articles>
$item_str    </Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        if (!is_array($musicArray)) {
            return "";
        }
        $itemTpl = "<Music>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <MusicUrl><![CDATA[%s]]></MusicUrl>
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
    </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[music]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
        <MediaId><![CDATA[%s]]></MediaId>
    </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[image]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
        <MediaId><![CDATA[%s]]></MediaId>
    </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[voice]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
        <MediaId><![CDATA[%s]]></MediaId>
        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[video]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    // 日志记录
    private function logger($log_content)
    {
        $max_size = 1000000;
        $log_filename = 'log.xml';
        if (file_exists($log_filename) && abs(filesize(($log_filename)) > $max_size)) {
            unlink($log_filename);
        }

        file_put_contents($log_filename, date('Y-m-d H:i:s') . ' ' . $log_content . '\r\n', FILE_APPEND);
    }

    // 天气
    function getWeatherInfo($cityName)
    {
        $ak = 'WT7idirGGBgA6BNdGM36f3kZ';
        $sk = 'uqBuEvbvnLKC8QbNVB26dQYpMmGcSEHM';
        $url = 'http://api.map.baidu.com/telematics/v3/weather?ak=%s&location=%s&output=%s&sn=%s';
        $uri = '/telematics/v3/weather';
        $location = $cityName;
        $output = 'json';
        $querystring_arrays = array(
            'ak' => $ak,
            'location' => $location,
            'output' => $output
        );
        $querystring = http_build_query($querystring_arrays);
        $sn = md5(urlencode($uri . '?' . $querystring . $sk));
        $targetUrl = sprintf($url, $ak, urlencode($location), $output, $sn);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $targetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);
        if ($result["error"] != 0) {
            return $result["status"];
        }
        $curHour = (int)date('H', time());
        $weather = $result["results"][0];

        $weatherArray[] = array("Title" =>$weather['currentCity']."天气预报", "Description" =>"", "PicUrl" =>"", "Url" =>"");
        for ($i = 0; $i < count($weather["weather_data"]); $i++) {
            $weatherArray[] = array("Title"=>
                $weather["weather_data"][$i]["date"]."\n".
                $weather["weather_data"][$i]["weather"]." ".
                $weather["weather_data"][$i]["wind"]." ".
                $weather["weather_data"][$i]["temperature"],
                "Description"=>"",
                "PicUrl"=>(($curHour >= 6) && ($curHour < 18))?$weather["weather_data"][$i]["dayPictureUrl"]:$weather["weather_data"][$i]["nightPictureUrl"], "Url"=>"");
        }
        return $weatherArray;
    }

    // 笑话
    function getJokeInfo()
    {
        $joke = DB::table('joke')->pluck('content');
        return $joke;
    }


    public function createMenu()
    {
        dd((new Menu())->createMenu());
    }
}
