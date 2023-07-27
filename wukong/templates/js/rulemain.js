//console.log($("#iframe01").height());

//console.log($("#iframe01"));  //contentDocument.body.clientHeight .contentDocument

/*
var frame = document.getElementById('#frame'), 
    win = frame.contentWindow, 
    doc = win.document, 
    html = doc.documentElement, 
    body = doc.body; 
	
	console.log(body);
*/
/*
function fatherWindow(param){
	//alert(param);
		
} 
*/

var bodyHeight 	= $("body").height();
var htmlPop 	= '<div style="width:100%;height:100%;position:absolute;top:0;left:0;background:#000;opacity:0.6;display:none;" id="systemdialogue" ></div>';
$("body").append(htmlPop);
$("#systemdialogue").height(bodyHeight);