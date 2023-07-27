<?php	require(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('boc_info_list');
// require_once('../chgservices/func.news.php');
/*
**************************
(C)2010-2013 phpMyWind.com
update: 2012-6-20 8:58:54
person: Feng
**************************
*/


//初始化参数
$tbname = 'boc_info_list';
$gourl  = 'boc_info_list.php';
$action = isset($action) ? $action : '';
$ismminfo= isset($ismminfo) ? $ismminfo : '';
$page = isset($page)?$page:1;

//添加列表信息
if($action == 'add')
{
	//栏目权限验证
	//IsCategoryPriv($classid,'add');


	//初始化参数
	if(!isset($mainid))        $mainid = '-1';
	if(!isset($flag))          $flag   = '';
	if(!isset($picarr))        $picarr = '';
	if(!isset($rempic))        $rempic = '';
	if(!isset($remote))        $remote = '';
	if(!isset($autothumb))     $autothumb = '';
	if(!isset($autodesc))      $autodesc = '';
	if(!isset($autodescsize))  $autodescsize = '';
	if(!isset($autopage))      $autopage = '';
	if(!isset($autopagesize))  $autopagesize = '';

	if(!isset($description))  $description = '';
	if(!isset($blineid))  $blineid = 0;
	if(!isset($info_attrid))  $info_attrid = 0;


	//获取parentstr
	$classid=0;
	$classstr=(isset($trale)?$trale:'0').','.(isset($mall)?$mall:'0').','.(isset($immigrant)?$immigrant:'0').','.(isset($abroad)?$abroad:'0');
	$parentid=0;
	$parentstr="";

/* 	$row = $dosql->GetOne("SELECT parentid,classname FROM `boc_info_class` WHERE `id`=$classid");
	$parentid = $row['parentid'];
	$classstr = $row['classname'];
	if($parentid == 0)
	{
		$parentstr = '0,';
	}
	else
	{
		$r = $dosql->GetOne("SELECT `classname` FROM `boc_info_class` WHERE `id`=$parentid");
		$parentstr = $r['classname'];
	} */
	
	
/* 	//获取mainid
	if($mainid != '-1')
	{
		$row = $dosql->GetOne("SELECT `parentid` FROM `#@__maintype` WHERE `id`=$mainid");
		$mainpid = $row['parentid'];
	
		if($mainpid == 0)
		{
			$mainpstr = '0,';
		}
		else
		{
			$r = $dosql->GetOne("SELECT parentstr FROM `#@__maintype` WHERE id=$mainpid");
			$mainpstr = $r['parentstr'].$mainpid.',';
		}
	}
	else
	{
		$mainpid  = '-1';
		$mainpstr = '';
	} */


	//文章属性
	if(is_array($flag))
	{
		$flag = implode(',',$flag);
	}



	//第一个图片作为缩略图
	if($autothumb == 'true')
	{
		$cont_str = stripslashes($content);
		preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/', $cont_str, $imgurl);

		//如果存在图片
		if(isset($imgurl[1][0]))
		{
			$picurl = $imgurl[1][0];
			$picurl = substr($picurl, strpos($picurl, 'uploads/'));
		}
	}



	$posttime = time();
	$checkinfo = 'false';
	$author = $_SESSION['admin'];
	$pictrue= empty($picurl)?0:1;
	
	$sql = "INSERT INTO `$tbname` (classid, classstr,parentid, parentstr, title, infoflag, blineid, info_attrid, author, description,content, picurl, posttime, checkinfo,infotype,pictrue) VALUES ($classid,'$classstr','$parentid', '$parentstr', '$title', '$infoflag','$blineid','$info_attrid', '$author', '$description', '$content', '$picurl', '$posttime', '$checkinfo',0,$pictrue)";
	
	
	if($dosql->ExecNoneQuery($sql))
	{
		
		$aid = $dosql->GetLastID();
		$sq="INSERT INTO `boc_info_flag`(aid,flagname,infotype) values";
		$flagname= explode('|',$infoflag);
		for($i=0;$i<count($flagname);$i++){
			$sq.="($aid,'$flagname[$i]',0),"; 
		}
		$sq = substr($sq,0,strlen($sq)-1);
		$dosql->ExecNoneQuery($sq);
		
				//----------------添加sitemp信息供百度spider抓取-------
		
		$xml='<url>
		<loc>http://business.sn-boc.cn/chgservices/news.php?id='.$aid.'</loc>
		<lastmod>'.date('Y-m-d',time()).'</lastmod>
		<changefreq>always</changefreq>
		<priority>0.8</priority>
		<data>
		<display>
		<title>'.$title.'</title>
		<tag>'.$infoflag.'</tag>
		<image loc="http://business.sn-boc.cn" title="'.$title.'"/>
		</display>
		</data>
		</url>';
		
		$start = '<?xml version="1.0" encoding="UTF-8" ?> '.chr(10);
		$filename =  'sitemap.xml';
		//更新到xml文件中,增加结尾
		if(!file_exists($filename))
			file_put_contents($filename,$start);
		$xmlList = file($filename);
		$xmlCount = count($xmlList);
		$xmlList[$xmlCount-1]=$xml.chr(10)."</urlset>";
		$newXml = '';

		foreach($xmlList as $v){
		$newXml.= $v;
		}
		if(file_put_contents($filename, $newXml)){
			if (file_exists(dirname(dirname(__FILE__)).'\\'.$filename)) {
				unlink(dirname(dirname(__FILE__)).'\\'.$filename);		
			} 
				$res=copy(dirname(__FILE__).'\\'.$filename,dirname(dirname(__FILE__)).'\\'.$filename);

		}
			
		header("location:$gourl");
		exit();
	}
}


//修改列表信息
else if($action == 'update')
{
	//栏目权限验证
	//IsCategoryPriv($cid,'update');


	//初始化参数
	if(!isset($mainid))        $mainid = '-1';
	if(!isset($flag))          $flag   = '';
	if(!isset($picarr))        $picarr = '';
	if(!isset($rempic))        $rempic = '';
	if(!isset($remote))        $remote = '';
	if(!isset($autothumb))     $autothumb = '';
	if(!isset($autodesc))      $autodesc = '';
	if(!isset($autodescsize))  $autodescsize = '';
	if(!isset($autopage))      $autopage = '';
	if(!isset($autopagesize))  $autopagesize = '';

	if(!isset($description))  $description = '';
	if(!isset($blineid))  $blineid = 0;
	if(!isset($info_attrid))  $info_attrid = 0;


	//获取parentstr
	
	$classid=0;
	$classstr=(isset($trale)?$trale:'0').','.(isset($mall)?$mall:'0').','.(isset($immigrant)?$immigrant:'0').','.(isset($abroad)?$abroad:'0');
	$parentid=0;
	$parentstr="";
/* 	$row = $dosql->GetOne("SELECT parentid,classname FROM `boc_info_class` WHERE `id`=$classid");
	$parentid = $row['parentid'];
	$classstr = $row['classname'];
	if($parentid == 0)
	{
		$parentstr = '0,';
	}
	else
	{
		$r = $dosql->GetOne("SELECT `classname` FROM `boc_info_class` WHERE `id`=$parentid");
		$parentstr = $r['classname'];
	} */
	
	
/* 	//获取mainid
	if($mainid != '-1')
	{
		$row = $dosql->GetOne("SELECT `parentid` FROM `#@__maintype` WHERE `id`=$mainid");
		$mainpid = $row['parentid'];
	
		if($mainpid == 0)
		{
			$mainpstr = '0,';
		}
		else
		{
			$r = $dosql->GetOne("SELECT parentstr FROM `#@__maintype` WHERE id=$mainpid");
			$mainpstr = $r['parentstr'].$mainpid.',';
		}
	}
	else
	{
		$mainpid  = '-1';
		$mainpstr = '';
	}
 */

	//文章属性
	if(is_array($flag))
	{
		$flag = implode(',',$flag);
	}




	//第一个图片作为缩略图
	if($autothumb == 'true')
	{
		$cont_str = stripslashes($content);
		preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/', $cont_str, $imgurl);

		//如果存在图片
		if(isset($imgurl[1][0]))
		{
			$picurl = $imgurl[1][0];
			$picurl = substr($picurl, strpos($picurl, 'uploads/'));
		}
	}





	$posttime = time();
	$checkinfo = 'false';
	$author = $_SESSION['admin'];
	$pictrue= empty($picurl)?0:1;

	$sql = "UPDATE `$tbname` SET classid=$classid, classstr='$classstr', parentid=$parentid, parentstr='$parentstr', title='$title', infoflag='$infoflag', blineid='$blineid', info_attrid='$info_attrid', author='$author',description='$description', content='$content', picurl='$picurl',pictrue=$pictrue, posttime=$posttime, checkinfo='$checkinfo' WHERE id=$id";
	
	
	
	
	
	
	if($dosql->ExecNoneQuery($sql))
	{
		
		$dosql->ExecNoneQuery("delete from boc_info_flag where aid= $id");
		
		$sq="INSERT INTO `boc_info_flag`(aid,flagname,infotype) values";
		$flagname= explode('|',$infoflag);
		for($i=0;$i<count($flagname);$i++){
			$sq.="($id,'$flagname[$i]',0),"; 
		}
		$sq = substr($sq,0,strlen($sq)-1);
		$dosql->ExecNoneQuery($sq);

		// InfolistOpadmin();//每次修改文章都重新生成文章信息文件
		
		header("location:$gourl"."?page=".$page);
		exit();
	}
}


//修改审核状态
else if($action == 'check')
{
	//审核权限
	//$r = $dosql->GetOne("SELECT `classid` FROM `boc_info_list` WHERE `id`=$id");
	//IsCategoryPriv($r['classid'],'update');


	if($checkinfo == '是')
	{
		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `checkinfo`='false' WHERE `id`=$id");

	}

	if($checkinfo == '否')
	{
		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `checkinfo`='true' WHERE `id`=$id");
	}
	
	// InfolistOpadmin();//每次审核文章都重新生成文章信息文件
	
	echo '[已审]';

	
}
else if($action == 'ismmup')
{
	if($ismminfo)
	{
		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `ismm`= 0 WHERE `id`=$id");
		echo '<a href="javascript:;" title="点击进行操作" onclick="Ismmop('.$id.',0)">设为快报</a>';
	}

	if($ismminfo == 0)
	{
		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `ismm`= 1 WHERE `id`=$id");
		echo '<a href="javascript:;" title="点击进行操作" onclick="Ismmop('.$id.',1)">取消快报</a>';
	}
		
/* 		echo "<script>location.href='boc_info_list.php';</script>";	
		exit(); */
}
else if($action == 'checkall')
{


		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `checkinfo`='true' WHERE `id` in ($id)");
	
		header("location:$gourl");
		//echo '[<a href="javascript:;" onclick="CheckInfo('.$id.',\'否\')" title="点击进行审核与未审操作">未审</a>]';
		exit();
}
else if($action == 'islore')
{


		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `islore`=1 WHERE `id` in ($id)");
	
		header("location:$gourl");
		//echo '[<a href="javascript:;" onclick="CheckInfo('.$id.',\'否\')" title="点击进行审核与未审操作">未审</a>]';
		exit();
}

else if($action == 'notlore')
{


		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `islore`=0 WHERE `id` in ($id)");
	
		header("location:$gourl");
		//echo '[<a href="javascript:;" onclick="CheckInfo('.$id.',\'否\')" title="点击进行审核与未审操作">未审</a>]';
		exit();
}
else if($action == 'ismmnews')
{


		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `ismm`=1 WHERE `id` in ($id)");
	
		header("location:$gourl");
		//echo '[<a href="javascript:;" onclick="CheckInfo('.$id.',\'否\')" title="点击进行审核与未审操作">未审</a>]';
		exit();
}
else if($action == 'notmmnews')
{


		$dosql->ExecNoneQuery("UPDATE `$tbname` SET `ismm`=0 WHERE `id` in ($id)");
	
		header("location:$gourl");
		//echo '[<a href="javascript:;" onclick="CheckInfo('.$id.',\'否\')" title="点击进行审核与未审操作">未审</a>]';
		exit();
}
//无状态返回
else
{
	header("location:$gourl");
	exit();
}
?>