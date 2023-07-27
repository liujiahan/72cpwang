
/*
**************************
(C)2014-2015 sxtm
update: 2014-04-19 10:00:00
person: dingzq
**************************
*/

//验证管理组添加
function cfm_wxmenu()
{
	if($("#classname").val()=="")
	{
		alert("请填写菜单名称！");
		return false;
	}
	
	if($("#mtype").val()=="view")
	{
		if($("#statestr").val()=="")
		{
			alert("请输入State参数，此参数请询问开发人员！");
			$("#statestr").focus();
			return false;
		}
		
		if($("#urltype").val()=="1"||$("#urltype").val()=="2"||$("#urltype").val()=="3"||$("#urltype").val()=="4"||$("#urltype").val()=="6")
		{
			alert("当菜单类型为view时URL类型只能选择接口URL或者接口URL（直跳）！");
			//$("#urltype").val("5");
			return false;
		}
	}
	
	/*
	
	if($("#urltype").val()=="0")
	{
		alert("请选择URL类型！");
		return false;
	}
	
	*/
	if($("#urltype").val()=="1"&&$("#classid").val()=="0")
	{
		alert("信息类必须要关联一个栏目！");
		return false;
	}
}

function chgmtype()
{
	if($("#mtype").val()=="view")
	{
		$("#urltype").val("5");
	}
}

function chgurltype()
{
	if($("#urltype").val()=="5"&&$("#mtype").val()!="view")
	{
		alert("只能当菜单类型为view时才能选择接口URL类型！");
		$("#urltype").val("0");
	}
	if($("#urltype").val()=="7"&&$("#mtype").val()!="view")
	{
		alert("只能当菜单类型为view时才能选择接口URL（直跳）类型！");
		$("#urltype").val("0");
	}
	
	if($("#mtype").val()=="view")
	{
		if($("#urltype").val()=="1"||$("#urltype").val()=="2"||$("#urltype").val()=="3"||$("#urltype").val()=="4"||$("#urltype").val()=="6")
		{
			alert("当菜单类型为view时此项只能选择接口URL或者接口URL（直跳）！");
			
			$("#urltype").val("5");
		}
	}
}
