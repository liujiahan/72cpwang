<?php	if(!defined('IN_SNRUNNING')) exit('Request Error!');

require_once('WXService.php');

/*
    微信接口参数名称
    $fromUsername    = $postObj->FromUserName;//发送者
    $toUsername      = $postObj->ToUserName;//接收者
    $PicUrl          = $postObj->PicUrl;//图片url
    $MsgType         = $postObj->MsgType;//消息类型
    $MsgId           = $postObj->MsgId;//消息id
    $MediaId         = $postObj->MediaId;//媒体id
    $Url             = $postObj->Url;//url
    $Event           = $postObj->Event;//事件类型CLICK VIEW
    $EventKey        = $postObj->EventKey;//事件KEY值，与自定义菜单接口中KEY值对应
    $Message         = trim($postObj->Content);//文本消息内容
    $Location_X      = $postObj->Location_X;
    $Location_Y      = $postObj->Location_Y;
    $Scale           = $postObj->Scale;
    $Label           = $postObj->Label;
    $Latitude        = $postObj->Latitude;
    $Longitude       = $postObj->Longitude;
    $Precision       = $postObj->Precision;

    $CardId          = $postObj->CardId;
    $IsGiveByFriend  = $postObj->IsGiveByFriend;
    $UserCardCode    = $postObj->UserCardCode;
    $OldUserCardCode = $postObj->OldUserCardCode;
    $OuterId         = $postObj->OuterId;
    $ConsumeSource   = $postObj->ConsumeSource;
*/

class WeiXinFun{

    var $object;
    var $wxservice;

    public function __construct($object){
        $this->object = $object;
        //微信事件初始化
        $this->wxservice = new WXService($object);
        $this->wxservice->WXEventInit();
    }

	//接收事件消息
    public function receiveEvent($object)
    {
        //关注
        if($object->Event == 'subscribe'){
            // $content = "郑州富达林果，幸运枣欢迎您！\n请回复以下关键字：文本 表情 单图文 多图文 音乐\n请按住说话 或 点击 + 再分别发送以下内容：语音 图片 小视频 我的收藏 位置";
            // $content .= (!empty($object->EventKey))?("\n来自二维码场景 ".str_replace("qrscene_","",$object->EventKey)):"";
            // $content .= "\n\n".'<a href="http://luckzao.applinzi.com">富达林果</a>';
            $content = WXService::scanEvent($object);
        }
        //取消关注
        else if($object->Event == 'unsubscribe'){
            $content = "取消关注";
        }
        //点击事件
        else if($object->Event == 'CLICK'){
            return WXService::goMenuUrl($object);
        }
        //VIEW
        else if($object->Event == 'VIEW'){
            $content = "跳转链接 ".$object->EventKey;
        }
        //扫描场景
        else if($object->Event == 'SCAN'){
            $content = "扫描场景 ".$object->EventKey;
            $content = WXService::scanEvent($object);
        }
        //位置
        else if($object->Event == 'LOCATION'){
            $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
            
        }
        //扫码推事件
        else if($object->Event == 'scancode_push'){
            $content = "扫码推事件";
            
        }
        //系统拍照
        else if($object->Event == 'pic_sysphoto'){
            $content = "系统拍照";
            
        }
        //相册发图
        else if($object->Event == 'pic_weixin'){
            $content = "相册发图：数量 ".$object->SendPicsInfo->Count;
            
        }
        //拍照或者相册
        else if($object->Event == 'pic_photo_or_album'){
            $content = "拍照或者相册：数量 ".$object->SendPicsInfo->Count;
            
        }
        //发送位置
        else if($object->Event == 'location_select'){
            $content = "发送位置：标签 ".$object->SendLocationInfo->Label;
            
        }
        //扫码带提示
        else if($object->Event == 'scancode_waitmsg'){
            if ($object->ScanCodeInfo->ScanType == "qrcode"){
                $content = "扫码带提示：类型 二维码 结果：".$object->ScanCodeInfo->ScanResult;
            }else if ($object->ScanCodeInfo->ScanType == "barcode"){
                $codeinfo = explode(",",strval($object->ScanCodeInfo->ScanResult));
                $codeValue = $codeinfo[1];
                $content = "扫码带提示：类型 条形码 结果：".$codeValue;
            }else{
                $content = "扫码带提示：类型 ".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
            }
        }        

        if(is_array($content)){
            $result = $this->transmitNews($object, $content);
        }else if(!empty($content)){
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收文本消息
    public function receiveText($object)
    {
        $keyword = trim($object->Content);
        //多客服人工回复模式
        if (strstr($keyword, "请问在吗") || strstr($keyword, "在线客服")){
            $result = $this->transmitService($object);
            return $result;
        }

        //自动回复模式
        if (strstr($keyword, "文本")){
            // $content = "这是个文本消息";
        }else if (strstr($keyword, "表情")){
            // $content = "微笑：/::)\n乒乓：/:oo\n中国：".$this->bytes_to_emoji(0x1F1E8).$this->bytes_to_emoji(0x1F1F3)."\n仙人掌：".$this->bytes_to_emoji(0x1F335);
        }else if (strstr($keyword, "单图文")){
            // $content = array();
            // $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
        }else if (strstr($keyword, "图文") || strstr($keyword, "多图文")){
            // $content = array();
            // $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            // $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            // $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
        }else if (strstr($keyword, "音乐")){
            $content = array();
            $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://mascot-music.stor.sinaapp.com/zxmzf.mp3", "HQMusicUrl"=>"http://mascot-music.stor.sinaapp.com/zxmzf.mp3"); 
        }else{
            $content = date("Y-m-d H:i:s",time())."\n\n".'<a href="http://luckzao.applinzi.com">幸运枣-花满院（富达林果）</a>';
        }

        //大转盘、刮刮卡活动链接
        $actInfo = WXService::getNewsByKeyword($keyword);
        if($actInfo['has']){
            $content = $actInfo['content'];
        }

        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收图片消息
    public function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    public function receiveLocation($object)
    {
        $content = "你发送的是位置，经度为：".$object->Location_Y."；纬度为：".$object->Location_X."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    public function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }

    //接收视频消息
    public function receiveVideo($object)
    {
        $content = "上传视频类型：".$object->MsgType;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收链接消息
    public function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)){
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
        $itemTpl = "<item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
        </item>";

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
            <Articles>$item_str</Articles>
        </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        if(!is_array($musicArray)){
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

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[transfer_customer_service]]></MsgType>
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复第三方接口消息
    public static function relayPart3($url, $rawData)
    {
        $headers = array("Content-Type: text/xml; charset=utf-8");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    //字节转Emoji表情
    function bytes_to_emoji($cp)
    {
        if ($cp > 0x10000){       # 4 bytes
            return chr(0xF0 | (($cp & 0x1C0000) >> 18)).chr(0x80 | (($cp & 0x3F000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x800){   # 3 bytes
            return chr(0xE0 | (($cp & 0xF000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x80){    # 2 bytes
            return chr(0xC0 | (($cp & 0x7C0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else{                    # 1 byte
            return chr($cp);
        }
    }

    //日志记录
    public function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 1000000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }
}
