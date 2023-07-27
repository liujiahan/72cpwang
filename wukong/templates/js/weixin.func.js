
/*
**************************
(C)2010-2013 sxtm.com
update: 2012-1-2 10:09:06
person: dzq
**************************
*/
//控制图文框信息
function change_panel()
{
	if($('input[name="msgtype"]:checked').val()=="text")
	{
		$("#tr_pic").hide();
	}
	else if($('input[name="msgtype"]:checked').val()=="news")
	{
		$("#tr_pic").show();
	}
	else if($('input[name="msgtype"]:checked').val()=="music")
	{
		$("#tr_music").show();
	}
}

//关注后的消息
function isbizhello()
{	
	if ($("#bizhello").attr("checked")) {
		//select
		$("#question").val("subscribe");
		$("#question").hide();
	}
	else
	{
		$("#question").val("");
		$("#question").show();
	}
}
	
	
function DrawImg(boxWidth,boxHeight)
{
    var imgWidth=$(".img").width();
    var imgHeight=$(".img").height();
    //比较imgBox的长宽比与img的长宽比大小
    if((boxWidth/boxHeight)>=(imgWidth/imgHeight))
    {
        //重新设置img的width和height
        $(".img").width((boxHeight*imgWidth)/imgHeight);
        $(".img").height(boxHeight);
        //让图片居中显示
        var margin=(boxWidth-$(".img").width())/2;
        $(".img").css("margin-left",margin);
    }
    else
    {
        //重新设置img的width和height
        $(".img").width(boxWidth);
        $(".img").height((boxWidth*imgHeight)/imgWidth);
        //让图片居中显示
        var margin=(boxHeight-$(".img").height())/2;
        $(".img").css("margin-top",margin);
    }
}


//验证管理员添加
function cfm_weixinprize()
{
	if($("#esscode").val() == "")
	{
		alert("请输入ESS订单流水号！");
		$("#esscode").focus();
		return false;
	}
	if($("#realname").val() == "")
	{
        alert ("请输入客户姓名！");
        $("#realname").focus();
        return false;
    }	
	if($("#mobile").val().length!=11)
	{
		alert("请核对手机号码！");
		$("#mobile").focus();
		return false;
	}
	
}