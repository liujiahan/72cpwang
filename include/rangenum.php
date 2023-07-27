<?php
/*
* 号码匹配函数
*/
function matchstr($PhoneStr){
		 $head  = substr($PhoneStr,0,3);
		 //获取检索域
		 $searchData = setThemRoughly();
		 foreach($searchData as $k=>$v){
				if($k==$head){
					$bool = checkeds($searchData[$k],$PhoneStr,$head);		
				}
		}
		return $bool;
}

function checkeds($d,$PhoneStr,$head){
	
		$start = '0000';
		$end   = '9999';
		foreach($d as $k=>$v){
			$ToCompare1 = $head.$d[$k][0].$start;
			$ToCompare2 = $head.$d[$k][1].$end;
			//echo $ToCompare2;

			if( (floatval($PhoneStr)<=floatval($ToCompare2)) & (floatval($PhoneStr)>=floatval($ToCompare1)) ){
				return 1;
			}			
		}
}

//设置域
function setThemRoughly(){
	$NumberArray = array(
		//130 为号段
		'130'=>array(
				//这里是元素
				array('0290','0299'),
				array('0840','0842'),
				array('2290','2299'),
				array('2840','2842'),
				array('2851','2851'),
				array('2856','2856'),
				array('2859','2859'),
				array('3290','3299'),
				array('3858','3859'),
				array('3893','3893'),
				array('6037','6042'),
				array('7290','7299'),
				array('8750','8759'),
				array('8895','8899'),
				array('8090','8093'),
				array('2280','2289'),
				array('9690','9699'),
		),
		'131'=>array(
				array('1910','1919'),
				array('1040','1049'),
				array('9330','9339'),
				array('8600','8609'),
				array('4920','4929'),
				array('6571','6575'),
				array('4918','4918'),
				array('6578','6579'),
				array('5200','5209'),
				array('5210','5219'),
				array('5240','5249'),
				array('8610','8619'),
				array('0950','0959'),
		),
		'132'=>array(
				array('8980','8980'),
				array('0170','0179'),
				array('0180','0189'),
				array('2700','2709'),
				array('2770','2779'),
				array('2780','2789'),
				array('2800','2809'),
				array('5980','5989'),
				array('0160','0169'),
				array('0140','0149'),
				array('0150','0159'),
				array('5940','5949'),
				array('5970','5979'),
				array('5990','5999'),
				array('7920','7929'),
				array('7930','7939'),
				array('7940','7949'),
				array('8920','8929'),
				array('8930','8939'),
				array('8989','8989'),
				array('8981','8988'),
				array('9900','9909'),
				array('9910','9913'),
				array('9916','9917'),
				array('9919','9919'),
				array('4410','4410'),
		),
		'155'=>array(
				array('2900','2909'),
				array('2920','2969'),
				array('9180','9189'),
				array('9460','9469'),
				array('9480','9499'),
				array('9616','9618'),
				array('9640','9644'),
				array('9660','9669'),
				array('9680','9689'),
				array('0918','0918'),
				array('0290','0299'),
				array('9410','9419'),
		),
		'156'=>array(
				array('0920','0929'),
				array('1900','1909'),
				array('1920','1949'),
				array('6460','6499'),
				array('6700','6709'),
				array('9140','9149'),
				array('9170','9199'),
				array('8600','8609'),
				array('8620','8629'),
				array('8640','8649'),
		),
		'185'=>array(
				array('0290','0299'),
				array('0920','0929'),
				array('9140','9149'),
				array('9175','9179'),
				array('9184','9189'),
				array('9191','9228'),
				array('9250','9259'),
				array('9260','9269'),
				array('9270','9279'),
				array('9280','9289'),
				array('9290','9299'),
		),
		'186'=>array(
				array('2914','2914'),
				array('2915','2915'),
				array('2924','2925'),
				array('2930','2939'),
				array('9145','9145'),
				array('9152','9152'),
				array('9163','9163'),
				array('9156','9157'),
				array('0290','0299'),
				array('0928','0928'),
				array('8180','8189'),
				array('8194','8194'),
				array('2900','2909'),
				array('2926','2926'),
				array('2928','2929'),
				array('9180','9189'),
				array('9195','9195'),
				array('2919','2919'),
				array('9148','9149'),
				array('9155','9155'),
				array('2940','2943'),
				array('2945','2949'),
				array('2960','2969'),
				array('8290','8299'),
				array('2950','2959'),
				array('2944','2944'),
		),
	);
	return $NumberArray;
}

//如果存在 输出1 不存在 为空;	
// $res = matchstr('18629598875');
// echo $res;
?>