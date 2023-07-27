<?php	require(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('zixun_list');

/*
**************************
(C)2010-2013 phpMyWind.com
update: 2013-8-17 23:02:04
person: Feng
**************************
*/


//初始化未传递参数
$page = isset($page)?$page:1;
$action  = isset($action) ? $action : '';
$keyword = isset($keyword) ? $keyword : '';
$styleal = '';
$stylec1 = '';
$stylec0 = '';
$styleah = '';

if(isset($s)){
	$stime = GetMkTime($starttime.' 0:00:00');
	$etime = GetMkTime($endtime.' 24:00:00');
	$estime = GetMkTime($endtime);
}	

//删除单条记录
if($action == 'del')
{
	//栏目权限验证
	$r = $dosql->GetOne("SELECT `classid` FROM `boc_info_list` WHERE `id`=$id");
	//IsCategoryPriv($r['classid'],'del',1);

	$deltime = time();
	$dosql->ExecNoneQuery("UPDATE `boc_info_list` SET delstate='true', deltime='$deltime' WHERE id=$id");
}


//删除选中记录
if($action == 'delall')
{
	if($ids != '')
	{
		//解析id,验证是否有删除权限
		$ids = explode(',',$ids);
		$idstr = '';
		foreach($ids as $id)
		{
			//$r = $dosql->GetOne("SELECT `classid` FROM `boc_info_list` WHERE `id`=$id");
			//if(IsCategoryPriv($r['classid'],'del',1))
			//{
				$idstr .= $id.',';
			//}
		}
		$idstr .= trim($idstr,',');

		if($idstr != '')
		{
			$deltime = time();
			$dosql->ExecNoneQuery("UPDATE `boc_info_list` SET delstate='true', deltime='$deltime' WHERE `id` IN ($idstr)");
		}
	}
}


//设置属性样式及查询语句
switch($flag)
{
	case 'all':
		$flagquery = 'id<>0';
		$styleal = 'onflag';
		break;  
	case 'checkinfo2':
		$flagquery  = "checkinfo='false'";
		$stylec1 = 'onflag';
		break;
	case 'checkinfo':
		$flagquery  = "checkinfo='true'";	
		$stylec0 = 'onflag';
		break;
	case 'author':
		$flagquery  = "author='".$_SESSION['admin']."'";
		$styleah = 'on_author';
		break;
	case 'blineid':
		$flagquery  = "blineid='$strf'";	
		$stylec0 = 'onflag';
		break;
	case 'info_attrid':
		$flagquery  = "info_attrid='$strf'";	
		$stylec0 = 'onflag';
		break;		
		case 'c0':
		$flagquery  = "classstr like '1______'";	
		$stylec0 = 'onflag';
		break;
		case 'c1':
		$flagquery  = "classstr like '__1____'";	
		$stylec0 = 'onflag';
		break;
		case 'c2':
		$flagquery  = "classstr like '____1__'";	
		$stylec0 = 'onflag';
		break;
		case 'c3':
		$flagquery  = "classstr like '______1'";	
		$stylec0 = 'onflag';
		break;
		case 'viewed':
		$flagquery  = "viewed >=0 order by viewed desc";	
		$stylec0 = 'onflag';
		break;
	default:
		/*$dosql->Execute("SELECT `flag` FROM `#@__infoflag`");
		while($row = $dosql->GetArray())
		{
			if($row['flag'] == $flag)
			{
				$flagquery = "`flag` LIKE '%$flag%'";
			}
		}*/
}

//Ajax输出数据
?>

<div class="toolbarTab">
	<ul class="">
		<li class="<?php echo $styleal; ?>"><a href="javascript:;" onclick="GetFlag('all')">全部</a></li>
		<li><span>|</span></li>
		<li class="<?php echo $stylec1; ?>"><a href="javascript:;" onclick="GetFlag('checkinfo2');">未审</a></li>
		<li><span>|</span></li>
		<li class="<?php echo $stylec0; ?>"><a href="javascript:;" onclick="GetFlag('checkinfo')">已审</a></li>
		<li><span>|</span></li>
		<li class="<?php echo $styleah; ?>"><a href="javascript:;" onclick="GetFlag('author')">我发布的文档</a></li>
		<li><span>|&nbsp;</span></li>
		<li class="<?php echo $stylec1; ?>">
			<select name="info_attrid" id="info_attrid" onchange="GetFlags('info_attrid',this.options[this.options.selectedIndex].value)">
				<option value="-1">资讯类别</option>
				<?php 
					$dosql->Execute("SELECT * FROM `#@__info_attr` ORDER BY `orderid` ASC");
					while($row = $dosql->GetArray())
					{
						echo '<option value="'.$row['id'].'">'.$row['attr_name'].'</option>';
					}
				?>
			</select>
		</li>
		<!-- <li><span>|&nbsp;</span></li>
		<li class="<?php echo $stylec1; ?>">
			<select name="viewflag" id="viewflag" onchange="GetFlags('qcflag',this.options[this.options.selectedIndex].value)">
				<option value="-1">业务标签</option>
				<?php
					$dosql->Execute("SELECT `flagname` FROM `boc_product_flag` order by orderid asc");
					while($row = $dosql->GetArray())
					{
						echo '<option value="'.$row['flagname'].'">'.$row['flagname'].'</option>';
					}
				?>
				
			</select>
		</li> -->
		<li><span>|&nbsp;</span></li>
		<li class="<?php echo $stylec1; ?>">
			<div style="height:40px;text-align:center;">
				<span>按发布时间：<input name="starttime" type="text" id="starttime" class="input_short" value="<?php if(isset($s)) echo GetDateMk($stime); else echo GetDateMk(time()) ; ?>" readonly="readonly" onFocus="WdatePicker({readOnly:true})" />
			—
			<input name="endtime" type="text" id="endtime" class="input_short" value="<?php if(isset($s)) echo GetDateMk($estime); else echo GetDateMk(time());  ?>" readonly="readonly" onFocus="WdatePicker({readOnly:true})" />
				&nbsp;<a href="javascript:;" onclick="GetFlagse();">查询</a></span>
				</div>
		</li>
		<li><span>|&nbsp;</span></li>
		<li class="<?php echo $stylec1; ?>">
			<div style="height:40px;text-align:center;">
				<span>按内容标签：<input type="text" value="<?php if(isset($word)) echo $word; ?>" id="flagword" name="flagword" placeholder="输入关键词"  />
		&nbsp;<a href="javascript:;" onclick="GetSearchs();">查询</a></span>
				</div>
		</li>
	</ul>

	<div id="search" class="search"> 
		<span class="s">
		<input name="keyword" id="keyword" type="text" title="输入标题名进行搜索" value="<?php echo $keyword; ?>" placeholder="输入关键词按标题搜索"/>
		</span> <span class="b"><a href="javascript:;" onclick="GetSearch();"><img src="templates/images/search_btn.png" title="搜索" /></a></span></div>
	</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable" id="ajaxlist">
	<tr class="head" align="left">
		<td align="left" height="32" width=""><input type="checkbox" name="checkid" id="checkid" onclick="CheckAll(this.checked);"></td>
		<td align="left" width="">ID</td>
		<td align="left" width="">标题</td>
		<td width="">权重</td>
		<td width="" onclick="GetFlag('viewed');">浏览量</td>
		<td width="">发布时间</td>
		<td width="">作者</td>
<!--		<td width="4%">审核</td> -->
		<td align="right" width="" class="noborder">操作</td>
	</tr>
	<?php

	//检查全局分页数
	if(empty($cfg_pagenum))  $cfg_pagenum = 20;


	//权限验证
	if($cfg_adminlevel != 1)
	{
		//初始化参数
		$catgoryListPriv   = '';
		$catgoryUpdatePriv = array();
		$catgoryDelPriv    = array();

		$dosql->Execute("SELECT * FROM `#@__adminprivacy` WHERE `groupid`=".$cfg_adminlevel." AND `model`='category' AND `action`<>'add'");
		while($row = $dosql->GetArray())
		{
			//查看权限
			if($row['action'] == 'list')
				$catgoryListPriv .= $row['classid'].',';

			//修改权限
			if($row['action'] == 'update')
				$catgoryUpdatePriv[] = $row['classid'];

			//删除权限
			if($row['action'] == 'del')
				$catgoryDelPriv[]    = $row['classid'];
			
		}

		$catgoryListPriv = trim($catgoryListPriv,',');
	}


	//设置sql
	if(isset($_SESSION['blineid']) && !empty($_SESSION['blineid'])){
		$sql = "SELECT * FROM `boc_info_list` where blineid='".$_SESSION['blineid']."' AND delstate='' and infotype=0";
	}else{
		$sql = "SELECT * FROM `boc_info_list` where delstate='' and infotype=0";
	}
	if(isset($starttime))
	{	
		$sql.=" AND posttime>$stime and posttime<$etime";
	}
	// if(!empty($catgoryListPriv)) $sql .= " AND classid IN ($catgoryListPriv)";	
	// if(!empty($cid))     $sql .= " AND (classid=$cid OR parentstr Like '%,$cid,%')";	
	if(!empty($flag))    $sql .= " AND $flagquery";
	if(!empty($keyword)) $sql .= " AND title LIKE '%$keyword%'";
	if(!empty($flagword)) $sql .= " AND infoflag LIKE '%$flagword%'";
	$dopage->GetPage($sql);
	while($row = $dosql->GetArray())
	{


		$title = $row['title'];
		$infoflag = $row['infoflag'];
		$c_arr = explode(',',$row['classstr']);
		$classstr=($c_arr[0]==1?'旅游,':'').($c_arr[1]==1?'海淘,':'').($c_arr[2]==1?'移民,':'').($c_arr[3]==1?'留学':'');
		
		//获取审核状态
		switch($row['checkinfo'])
		{
			case 'true':
				$checkinfo = '是';
				break;  
			case 'false':
				$checkinfo = '否';
				break;
			default:
				$checkinfo = '没有获取到参数';
		}
		

		//修改权限
		if($cfg_adminlevel != 1)
		{
			// if(in_array($row['classid'], $catgoryUpdatePriv))
				$updateStr = '<a href="boc_info_list_update.php?cid='.$cid.'&id='.$row['id'].'&page='.$page.'">修改</a>';
			// else
			// 	$updateStr = '修改';
		}
		else
		{
			$updateStr = '<a href="boc_info_list_update.php?cid='.$cid.'&id='.$row['id'].'&page='.$page.'">修改</a>';
		}

		//预览权限
		if($cfg_adminlevel != 1)
		{
			// if(in_array($row['classid'], $catgoryUpdatePriv))
				$perviewStr = '<a href="'.WEIXIN_BASE.'/wxpage/preview_news.php?id='.$row['id'].'" target="_blank">预览</a>';
			// else
			// 	$perviewStr = '预览';
		}
		else
		{
				$perviewStr = '<a href="'.WEIXIN_BASE.'/wxpage/preview_news.php?id='.$row['id'].'" target="_blank">预览</a>';
		}


		//删除权限
		if($cfg_adminlevel != 1)
		{
			// if(in_array($row['classid'], $catgoryDelPriv))
				$delStr = '<a href="javascript:;" onclick="ClearInfo('.$row['id'].')">删除</a>';
			// else
			// 	$delStr = '删除';
		}
		else
		{
			$delStr = '<a href="javascript:;" onclick="ClearInfo('.$row['id'].')">删除</a>';
		}
		
		
		//审核权限
		if($cfg_adminlevel != 1)
		{
			// if(in_array($row['classid'], $catgoryUpdatePriv))
				$checkStr = '<a href="javascript:;" title="点击进行审核与未审操作" onclick="CheckInfo('.$row['id'].',\''.$checkinfo.'\')">[审核]</a>';
			// else
			// 	$checkStr = $checkinfo;
		}
		else
		{
			$checkStr = '<a href="javascript:;" title="点击进行审核与未审操作" onclick="CheckInfo('.$row['id'].',\''.$checkinfo.'\')">[审核]</a>';
		}
		if($checkinfo=='是'){$checkStr="[已审]";}
		
		
		
		
	?>
	<tr align="left" class="mgr_tr" onmouseover="this.className='mgr_tr_on'" onmouseout="this.className='mgr_tr'">
		<td align="left" height="32"><input type="checkbox" name="checkid[]" id="checkid[]" value="<?php echo $row['id']; ?>" /></td>
		<td align="left" ><?php echo $row['id']; ?></td>
		<td align="left" class="titles"><?php echo $title; ?></td>
		<td class="number"><?php echo $row['weight']; ?></td>
		<td class="number"><?php echo $row['viewed']; ?></td>
		<td class="number"><?php echo GetDateTime($row['posttime']); ?></td>
		<td><?php echo $row['author']; ?></td>
<!--		<td><?php echo $checkinfo; ?></td>-->

		<td align="right" class="action"><span id="check<?php echo $row['id']; ?>"><?php echo $checkStr; ?></span><span><?php echo $perviewStr ?></span><span>[<?php echo $updateStr; ?>]</span><span>[<?php echo $delStr; ?>]</span></td>
	</tr>
	<?php
	}
	?>
</table>
<?php
if($dosql->GetTotalRow() == 0)
{
	echo '<div class="mgr_nlist">暂时没有相关的记录</div>';
}
?>
<div class="bottomToolbar">
	<div class="selArea"><span>全部操作：</span> <a href="javascript:CheckAll(true);">全选</a> | <a href="javascript:CheckAll(false);">取消</a> - <a href="javascript:;" title="点击进行审核与未审操作" onclick="AjaxCheckinfoAll()">通过审核</a> | <a href="javascript:;" onclick="AjaxClearAll();">删除</a></div>
	<a class="dataBtn" href="boc_info_list_add.php">添加信息</a>
</div>
<div class="page"> <?php echo $dopage->AjaxPage(); ?> </div>
