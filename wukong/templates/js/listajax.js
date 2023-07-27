
/*
**************************
(C)2010-2013 phpMyWind.com
update: 2013-5-4 11:26:41
person: Feng
**************************
*/

//获取列表
function GetList(par_tbn, par_cid,page_list)
{
	pag     = par_tbn;
	tbn     = par_tbn;
	cid     = par_cid;
	flag    = "all";
	keyword = "";
	page    = page_list;
	tid     = "";

	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&flag="+flag+"&page="+page,
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("body").append('<div id="listload"></div><div id="coverbg"></div>');
			$("#coverbg").css("height",$(document).height()).show();
		},
		success:ShowList
	});
}

function GetListByWX(par_tbn, par_cid,page_list,wxnews_id)
{
	pag     = par_tbn;
	tbn     = par_tbn;
	cid     = par_cid;
	flag    = "all";
	keyword = "";
	page    = page_list;
	tid     = "";
	wxnewsid= wxnews_id;

	if(par_tbn == 'boc_weixin_news'){
		tbn = 'boc_info_list';
	}

	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&flag="+flag+"&page="+page+"&wxnewsid="+wxnews_id,
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("body").append('<div id="listload"></div><div id="coverbg"></div>');
			$("#coverbg").css("height",$(document).height()).show();
		},
		success:ShowList
	});
}


//输出列表
function ShowList(data, textStatus, xmlHttp)
{
	$("#list_area").html(data);
	$("#coverbg").hide();
}


//输出列表
function GetDone(data, textStatus, xmlHttp)
{
	$("#list_area").html(data);
	$("#coverbg").hide();
	$("#listload").hide();
}


//选择栏目函数
function GetType(par_cid, classname)
{
	cid = par_cid;
	
	$("#alltype .btn").html(classname);

	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag,
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});
}


//选择分类函数(目前只对于商品分类生效)
function GetType2(par_tid, classname)
{
	tid = par_tid;
	
	$("#alltype2 .btn").html(classname);

	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag,
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});
}

//按发布时间搜索
function GetFlagse()
{	
	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&starttime="+$("#starttime").val()+'&endtime='+$("#endtime").val()+'&s=1',
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});
		
}



//显示属性
function GetFlag(par_flag)
{
	flag = par_flag;
	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&keyword="+encodeURI(keyword),
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});
}

//显示属性
function GetFlags(par_flag,str_flag)
{
	flag = par_flag;
	strf = str_flag;
	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&flag="+flag+"&strf="+strf+"&keyword="+encodeURI(keyword),
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});
}



//按内容标签查询
function GetSearchs()
{
	keyword = $("#flagword").val();
	if(keyword == '' || keyword == '请填写关键字')
	{
		$("#flagword").val("请填写关键字");
		return;
	}

	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&flagword="+encodeURI(keyword),
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});

	$("#list_load").css({"display":"block"});
}

//显示查询列表
function GetSearch()
{
	keyword = $("#keyword").val();
	if(keyword == '' || keyword == '请填写关键字')
	{
		$("#keyword").val("请填写关键字");
		return;
	}

	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&keyword="+encodeURI(keyword),
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});

	$("#list_load").css({"display":"block"});
}


//显示分页
function PageList(par_page)
{
	page = par_page;
	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&keyword="+encodeURI(keyword)+"&page="+page,
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});
}

//显示分页
function PageListByWx(par_page)
{
	page = par_page;
	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&keyword="+encodeURI(keyword)+"&page="+page+"&wxnewsid="+wxnewsid,
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});
}


//删除信息
function ClearInfo(par_id)
{
	if(confirm("确定要删除选中的信息吗？"))
	{
		$.ajax({
			url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&keyword="+encodeURI(keyword)+"&page="+page+"&action=del&id="+par_id,
			type:'get',
			dataType:'html',
			beforeSend:function(){
				$("#coverbg").css("height",$(document).height()).show();
				$("#listload").show();
			},
			success:GetDone
		});
		
		$("#list_load").css({"display":"block"});
	}
	else
	{
		return false;
	}
}


//删除信息
function AjaxClearAll()
{
	var ckobj = $("input[type='checkbox'][name!='checkid'][name^='checkid']:checked");

	if(ckobj.size() > 0)
	{
		if(confirm("确定要删除选中的信息吗？"))
		{
			var ids = '';
	
			ckobj.each(function(){
				if($(this).val() != 'on'){
					ids += $(this).val() + ',';
				}
			});
		
			ids = ids.slice(0,-1);
		
			$.ajax({
				url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&keyword="+encodeURI(keyword)+"&page="+page+"&action=delall&ids="+ids,
				type:'get',
				dataType:'html',
				beforeSend:function(){
					$("#coverbg").css("height",$(document).height()).show();
					$("#listload").show();
				},
				success:GetDone
			});
			
			$("#list_load").css({"display":"block"});
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('没有任何选中信息！');
		return false;
	}
}


//更改审核状态
function CheckInfo(par_id,state)
{
	id = par_id;
	$.ajax({
		url : tbn+"_save.php?action=check&id="+id+"&checkinfo="+encodeURI(state),
		type:'get',
		dataType:'html',
		success:function(data){$("#check"+id).html(data);}
	});
}


//设置文章成为推送文章
function Iswxnews(par_id,state,wxnews_id)
{
	id = par_id;
	$.ajax({
		url : "boc_weixin_news_save.php?action=set_wxnews&id="+id+"&iswxnews="+state+"&wxnews_id="+wxnews_id,
		type:'get',
		dataType:'html',
		success:function(data){$("#isweixin_news"+id).html(data);}
	});
}

//通知下发
function Isnotice(par_id)
{
	id = par_id;
	$.ajax({
		url : tbn+"_save.php?action=isnotice&id="+id,
		type:'get',
		dataType:'html',
		success:function(data){$("#isnotice"+id).html(data);}
	});
}

//通知撤回
function ReCall(par_id)
{
	id = par_id;
	if(!confirm("您确定要撤回通知吗？")){
		return false;
	}
	$.ajax({
		url : tbn+"_save.php?action=recall&id="+id,
		type:'get',
		dataType:'html',
		success:function(data){$("#recall"+id).html(data);}
	});
}


//更改标签级别
function Pidop(par_id,state)
{
	id = par_id;
	$.ajax({
		url : "boc_question_tag_save.php?action=pidup&id="+id+"&pidinfo="+state,
		type:'get',
		dataType:'html',
		success:function(data){$("#pid"+id).html(data);}
	});
}

//批量审核
function AjaxCheckinfoAll()
{
	var ckobj = $("input[type='checkbox'][name!='checkid'][name^='checkid']:checked");

	if(ckobj.size() > 0)
	{
		if(confirm("确定要审核选中的信息吗？"))
		{
			var ids = '';
	
			ckobj.each(function(){
				if($(this).val() != 'on'){
					ids+= $(this).val()+',';
				}
			});
			ids=ids.substring(0,ids.length-1); 
			$.ajax({
				url : tbn+"_save.php?action=checkall&id="+ids+"&checkinfo="+encodeURI('否'),
				type:'get',
				dataType:'html',
				success:function(data){location.href=tbn+'.php';}
			});
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('没有任何选中信息！');
		return false;
	}
}

//批量设为知识库
function AjaxIsLore()
{
	var ckobj = $("input[type='checkbox'][name!='checkid'][name^='checkid']:checked");

	if(ckobj.size() > 0)
	{
		if(confirm("确定要将选中的信息设为知识库吗？"))
		{
			var ids = '';
	
			ckobj.each(function(){
				if($(this).val() != 'on'){
					ids+= $(this).val()+',';
				}
			});
			ids=ids.substring(0,ids.length-1); 
			$.ajax({
				url : tbn+"_save.php?action=islore&id="+ids,
				type:'get',
				dataType:'html',
				success:function(data){location.href='boc_info_list.php';}
			});
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('没有任何选中信息！');
		return false;
	}
}

//批量取消知识库
function AjaxNotLore()
{
	var ckobj = $("input[type='checkbox'][name!='checkid'][name^='checkid']:checked");

	if(ckobj.size() > 0)
	{
		if(confirm("确定要将选中的信息取消知识库吗？"))
		{
			var ids = '';
	
			ckobj.each(function(){
				if($(this).val() != 'on'){
					ids+= $(this).val()+',';
				}
			});
			ids=ids.substring(0,ids.length-1); 
			$.ajax({
				url : tbn+"_save.php?action=notlore&id="+ids,
				type:'get',
				dataType:'html',
				success:function(data){location.href='boc_info_list.php';}
			});
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('没有任何选中信息！');
		return false;
	}
}
//批量设置快报
function AjaxIsMm()
{
	var ckobj = $("input[type='checkbox'][name!='checkid'][name^='checkid']:checked");

	if(ckobj.size() > 0)
	{
		if(confirm("确定要将选中的信息设为快报吗？"))
		{
			var ids = '';
	
			ckobj.each(function(){
				if($(this).val() != 'on'){
					ids+= $(this).val()+',';
				}
			});
			ids=ids.substring(0,ids.length-1); 
			$.ajax({
				url : tbn+"_save.php?action=ismmnews&id="+ids,
				type:'get',
				dataType:'html',
				success:function(data){location.href='boc_info_list.php';}
			});
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('没有任何选中信息！');
		return false;
	}
}
//批量设置快报
function AjaxNotMm()
{
	var ckobj = $("input[type='checkbox'][name!='checkid'][name^='checkid']:checked");

	if(ckobj.size() > 0)
	{
		if(confirm("确定要将选中的信息取消快报吗？"))
		{
			var ids = '';
	
			ckobj.each(function(){
				if($(this).val() != 'on'){
					ids+= $(this).val()+',';
				}
			});
			ids=ids.substring(0,ids.length-1); 
			$.ajax({
				url : tbn+"_save.php?action=notmmnews&id="+ids,
				type:'get',
				dataType:'html',
				success:function(data){location.href='boc_info_list.php';}
			});
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('没有任何选中信息！');
		return false;
	}
}
//批量审核产品
function AjaxCheckinfoAllp()
{
	var ckobj = $("input[type='checkbox'][name!='checkid'][name^='checkid']:checked");

	if(ckobj.size() > 0)
	{
		if(confirm("确定要审核选中的信息吗？"))
		{
			var ids = '';
	
			ckobj.each(function(){
				if($(this).val() != 'on'){
					ids+= $(this).val()+',';
				}
			});
			ids=ids.substring(0,ids.length-1); 
			$.ajax({
				url : "boc_product_save.php?action=checkall&id="+ids+"&checkinfo="+encodeURI('否'),
				type:'get',
				dataType:'html',
				success:function(data){location.href='boc_product_list.php';}
			});
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('没有任何选中信息！');
		return false;
	}
}


//批量审核产品
function AjaxCheckinfoAlll()
{
	var ckobj = $("input[type='checkbox'][name!='checkid'][name^='checkid']:checked");

	if(ckobj.size() > 0)
	{
		if(confirm("确定要审核选中的信息吗？"))
		{
			var ids = '';
	
			ckobj.each(function(){
				if($(this).val() != 'on'){
					ids+= $(this).val()+',';
				}
			});
			ids=ids.substring(0,ids.length-1); 
			$.ajax({
				url : "boc_lore_list_save.php?action=checkall&id="+ids+"&checkinfo="+encodeURI('否'),
				type:'get',
				dataType:'html',
				success:function(data){location.href='boc_lore_list.php';}
			});
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('没有任何选中信息！');
		return false;
	}
}

/******************************* 回收站 *******************************/


//显示回收站
function ShowRecycle()
{
	var recycle_title;
	
	if(pag == "infolist")
	{
		recycle_title = "信息列表回收站";
	}
	else if(pag == "infoimg")
	{
		recycle_title = "图片列表回收站";
	}
	else if(pag == "soft")
	{
		recycle_title = "软件列表回收站";
	}
	else if(pag == "goods")
	{
		recycle_title = "商品列表回收站";
	}
	else
	{
		recycle_title = "参数获取失败";
	}
	
	$("body").append("<div id=\"recycle_window\"><div class=\"header\"><span class=\"title\">"+recycle_title+"：</span> <span class=\"close\"><a href=\"javascript:HideRecycle()\"></a></span><div class=\"cl\"></div></div><form id=\"recycleform\" name=\"recycleform\" method=\"post\"><div class=\"list\" id=\"recycle_list\"></div><div class=\"bottom\"><div class=\"selall\"><span>选择：</span> <a href=\"javascript:RecycleCheckAll(true);\">全部</a> - <a href=\"javascript:RecycleCheckAll(false);\">无</a> - <a href=\"javascript:;\" onclick=\"RecycleReAll('resetall')\">还原</a> - <a href=\"javascript:;\" onclick=\"RecycleReAll('delall')\">删除</a></div><a href=\"javascript:;\" onclick=\"RecycleReAll('empty')\"><img src=\"templates/images/empty_recycle.png\" /></a> </div></form></div>")

	$.ajax({
		url : "recycle_mini.php?type="+tbn,
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
			$("#recycle_window").show();
			$("#recycle_list").html('<div class="loading" style="text-align:center;margin-top:75px;"><img src="templates/images/loading.gif">列表加载中...</div>');
		},
		success:RecycleDone
	});
}


//隐藏回收站
function HideRecycle()
{
	$.ajax({
		url : pag+"_do.php?tbname="+tbn+"&cid="+cid+"&tid="+tid+"&flag="+flag+"&keyword="+encodeURI(keyword)+"&page="+page,
		type:'get',
		dataType:'html',
		beforeSend:function(){
			$("#recycle_window").remove();
			$("#coverbg").css("height",$(document).height()).show();
			$("#listload").show();
		},
		success:GetDone
	});
}


//回收站内容操作
function RecycleRe(action,id)
{
	$.ajax({
		url : "recycle_mini.php?type="+tbn+"&action="+action+"&id="+id,
		type:'get',
		dataType:'html',
		success:RecycleDone
	});
}


//操作所有
function RecycleReAll(action)
{
	var ids = '';

	$("#recycleform input[type='checkbox'][id^='recycle_checkid']:checked").each(function(){
		ids += $(this).val() + ',';
	});

	ids = ids.slice(0,-1);
	if(ids=='' && action!='empty')
	{
		alert('没有任何选中信息！');
		return false;
	}

	$.ajax({
		url : "recycle_mini.php?type="+tbn+"&action="+action+"&ids="+ids,
		type:'get',
		dataType:'html',
		success:RecycleDone
	});
}


//完成操作
function RecycleDone(data)
{
	$("#recycle_list").html(data);
}
