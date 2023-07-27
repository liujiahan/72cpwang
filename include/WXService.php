<?php 	

if(!defined('IN_SNRUNNING')) exit('Request Error!');
 
/**
 * 服务微信接口类
 */
class WXService{

	var $postObj;

	public function __construct($postObj){
		$this->postObj = $postObj;
		$this->WXEventInit();
	}

	//微信事件初始化
	public function WXEventInit(){
		//记录用户信息
		$this->saveUserInfo();
	}
	
	/**
     * 微信公众号API URL数组
     * @param  string $key 接口URL数组下标
     * @return string      API地址
     */
	public static function apiUrls($key=''){
		$apiUrls = array(
			'user_info'        => 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=#access_token#&openid=#openid#',
			
			//添加客服帐号
			'kfaccount_add'    => 'https://api.weixin.qq.com/customservice/kfaccount/add?access_token=#access_token#',
			//修改客服帐号
			'kfaccount_update' => 'https://api.weixin.qq.com/customservice/kfaccount/update?access_token=#access_token#',
			//删除客服帐号
			'kfaccount_del'    => 'https://api.weixin.qq.com/customservice/kfaccount/del?access_token=#access_token#&kf_account=#kf_account#',
			//设置客服帐号的头像
			'kfaccount_avatar' => 'https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token=#access_token#&kf_account=#kf_account#',
			//获取所有客服账号
			'getkflist'        => 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=#access_token#',
			//客服接口-发消息
			'custom_send'      => 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=#access_token#',
			
			//上传图文消息内的图片获取URL
			'media_uploadimg'  => 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=#access_token#',
			//上传图文消息素材
			'media_uploadnews' => 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=#access_token#',
			//根据标签进行群发
			'mass_sendall'     => 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=#access_token#',
			//根据OpenID列表群发
			'mass_send'        => 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=#access_token#',
			//删除群发
			'mass_delete'      => 'https://api.weixin.qq.com/cgi-bin/message/mass/delete?access_token=#access_token#',
			//预览接口
			'mass_preview'     => 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=#access_token#',
			//查询群发消息发送状态
			'mass_get'         => 'https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token=#access_token#',
			
			//获取公众号的自动回复规则
			'autoreply'        => 'https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token=#access_token#',
			//创建二维码ticket
			'qrcode_create'    => 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=#access_token#',
			//删除群发
			'mass_delete'      => '',
			//删除群发
			'mass_delete'      => '',
			//删除群发
			'mass_delete'      => '',
			
            // 'kfaccount_add'           => 'https://api.weixin.qq.com/customservice/kfaccount/add?access_token=',
		);

		$url = '';
		if(isset($apiUrls[$key])){
			$url = str_replace('#access_token#', self::GetWeixinToken(), $apiUrls[$key]);
		}

		return $url;
	}

	public static function getNewsByKeyword($keyword){
		global $dosql;

		$result = array('has' => false);
		$dzpRow = $dosql->GetOne("SELECT a.id, b.styletype, b.title, b.info FROM `#@__dazhuanpan_prizes` a INNER JOIN `#@__dazhuanpan` b ON a.act_id=b.id WHERE a.keyword='$keyword'");
		if(isset($dzpRow['id'])){
			$result['has']       = true;
			$picUrl              = WEIXIN_BASE . 'uploads/image/game/' . ($dzpRow['styletype'] == 1 ? 'dzp-2.jpg' : 'dzp-1.jpg');
			$url                 = WEIXIN_BASE . 'dzp/wxOauth2.php?apid=' . $dzpRow['id'];
			$result['content']   = array();
			$result['content'][] = array(
				"Title"       => $dzpRow['title'],  
				"Description" => str_replace('<br/>', chr(13), $dzpRow['info']), 
				"PicUrl"      => $picUrl, 
				"Url"         => $url,
			);
		}else{
			$ggkRow = $dosql->GetOne("SELECT a.id, b.title, b.info FROM `#@__guaguaka_prizes` a INNER JOIN `#@__guaguaka` b ON a.act_id=b.id WHERE a.keyword='$keyword'");
			if(isset($ggkRow['id'])){
				$result['has']       = true;
				$picUrl              = WEIXIN_BASE . 'uploads/image/game/ggk.jpg';
				$url                 = WEIXIN_BASE . 'ggk/wxOauth2.php?apid=' . $ggkRow['id'];
				$result['content']   = array();
				$result['content'][] = array(
					"Title"       => $ggkRow['title'],  
					"Description" => str_replace('<br/>', chr(13), $ggkRow['info']), 
					"PicUrl"      => $picUrl, 
					"Url"         => $url,
				);
			}
		}

		return $result;
	}

	/**
	 * 获取并记录用户基本信息
	 * @param [type] $object [description]
	 */
	private function saveUserInfo(){
		global $dosql;

		$FromUserName = $this->postObj->FromUserName;

		$url       = str_replace('#openid#', $FromUserName, self::apiUrls('user_info'));
		$json      = self::getUrlText($url);
		$obj       = json_decode($json);
		
		$logintime = time();
		$row       = $dosql->GetOne("SELECT * FROM `#@__wxuser` WHERE openid='$FromUserName'");
		if(isset($row['openid'])){
			if(empty($row['nickname'])){
				$sql = "update `#@__wxuser`  set nickname='".$obj->{'nickname'}."',country='".$obj->{'country'}."',province='".$obj->{'province'}."',city='".$obj->{'city'}."',headimgurl='".$obj->{'headimgurl'}."',subscribe_time='".$obj->{'subscribe_time'}."' where openid='$FromUserName' ";
			}
		}else{
			$sql = "insert into `#@__wxuser` (openid,createdate,`event`,nickname,country,province,city,headimgurl,subscribe_time) values ('$FromUserName','$logintime','subscribe','".$obj->{'nickname'}."','".$obj->{'country'}."','".$obj->{'province'}."','".$obj->{'city'}."','".$obj->{'headimgurl'}."','".$obj->{'subscribe_time'}."') ";
		}
		$dosql->ExecNoneQuery($sql);

		$Event = $this->postObj->Event;
		if(in_array($Event, array('subscribe', 'unsubscribe'))){
			$dosql->ExecNoneQuery("update `#@__wxuser`  set `event`='$Event',updatetime='$logintime' where openid='$FromUserName'");
		}
	}

	private static function getUrlPostText($url, $postObj){
		$paramStr = '';
        $params = self::xmlToArr($postObj, false);
        $num = 0;
        foreach ($params as $uri => $param) {
            $num++;
            if($num == count($params))
                $paramStr .= "$uri=$param";
            else
                $paramStr .= "$uri=$param&";
        }

		$returnMessage = '';
        $context = array();
		$context['http'] = array (
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded ",
            'content' => $paramStr
        );
        $returnMessage = file_get_contents($url, false, stream_context_create($context));
        return $returnMessage;
	}

	public static function goMenuUrl($postObj){
		global $dosql;
        
		$returnMessage = '';
        $row = $dosql->GetOne("SELECT id,murl,classid,classname FROM `#@__wxmenu` WHERE mkey='".$postObj->EventKey."' ");
        if(isset($row["murl"]))
        {                           
            $newurl = $row['murl'];
            if($row['classid'] != 0){
                $newurl .= "?classid=".$row["classid"];
            }

            $returnMessage = self::getUrlPostText(WEIXIN_BASE.$newurl, $postObj);
        }
        return $returnMessage;
	}

	/**
	 * 扫码推事情
	 * @return [type] [description]
	 */
	public static function scanEvent($postObj){
		$content = '';
		if(!empty($postObj->EventKey)){
			$codeid = $postObj->Event == 'SCAN' ? $postObj->EventKey : substr($postObj->EventKey, 8);
			if($codeid == 123){
				$content .= "营养大枣活动地址：";
				$content .= "\n\n".'<a href="http://luckzao.applinzi.com">富达林果</a>';
			}
		}else{
			$content .= "郑州富达林果，幸运枣欢迎您！\n请回复以下关键字：文本 表情 单图文 多图文 音乐\n请按住说话 或 点击 + 再分别发送以下内容：语音 图片 小视频 我的收藏 位置";
            //$content .= (!empty($object->EventKey))?("\n来自二维码场景 ".str_replace("qrscene_","",$object->EventKey)):"";
            $content .= "\n\n".'<a href="http://luckzao.applinzi.com">富达林果</a>';
		}
		return $content;

		$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
				</xml>";

		$MsgType = 'text';
		$resultStr = sprintf($textTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $MsgType, $content);
		return $resultStr;
	}

    /*
     * 函数说明：获取微信access_token
     *
     * @return            string  返回access_token
     */
    public static function GetWeixinToken()
    {
        global $dosql;

        $row = $dosql->GetOne("SELECT * FROM `#@__wxtoken` WHERE id=1");
        if(isset($row['id']))
        {
            if(time()-$row['createtime']>300)
            {
                $wx_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WEIXIN_APPID."&secret=".WEIXIN_SECRET."";
                $wx_json = self::getUrlText($wx_url);
                $wx_obj = json_decode($wx_json);
                
                $sql = "update `#@__wxtoken` set access_token='".$wx_obj->{'access_token'}."', createtime=".time()." where id=1";
                $dosql->ExecNoneQuery($sql);
                return $wx_obj->{'access_token'};
            }
            else
            {
                return $row['access_token'];
            }
        }
        else
        {
            $wx_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WEIXIN_APPID."&secret=".WEIXIN_SECRET."";
            $wx_json = self::getUrlText($wx_url);
            $wx_obj = json_decode($wx_json);
            
            $sql = "insert into `#@__wxtoken` (id,access_token, createtime) values (1,'".$wx_obj->{'access_token'}."',".time().")";
            $dosql->ExecNoneQuery($sql);
            return $wx_obj->{'access_token'};
        }
    }

	public static function getUrlText($url, $postData='')
	{
		$header = "Content-type: text/html";//定义content-type为xml

		$ch = curl_init(); //初始化curl  
		curl_setopt($ch, CURLOPT_URL, $url);//设置链接  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		if(!empty($postData)){
			curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式  
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);//POST数据  
		}

		$response = curl_exec($ch);//接收返回信息  
		 if(curl_errno($ch)){//出错则显示错误信息  
			 return 'Error';
		 }  
		 curl_close($ch); //关闭curl链接  
		return $response;//显示返回信息 
	}

    public static function xmlToArr ($xml, $root = true) {
        if (!$xml->children()) {
            return (string) $xml;
        }
        $array = array();
        foreach ($xml->children() as $element => $node) {
            $totalElement = count($xml->{$element});
            if (!isset($array[$element])) {
            $array[$element] = "";
            }
            // Has attributes
            if ($attributes = $node->attributes()) {
                $data = array(
                'attributes' => array(),
                'value' => (count($node) > 0) ? self::xmlToArr($node, false) : (string) $node
                );
                foreach ($attributes as $attr => $value) {
                $data['attributes'][$attr] = (string) $value;
                }
                if ($totalElement > 1) {
                    $array[$element][] = $data;
                } else {
                    $array[$element] = $data;
                }
            // Just a value
            } else {
                if ($totalElement > 1) {
                $array[$element][] = self::xmlToArr($node, false);
                } else {
                $array[$element] = self::xmlToArr($node, false);
                }
            }
        }
        if ($root) {
            return array($xml->getName() => $array);
        } else {
            return $array;
        }

    }
}