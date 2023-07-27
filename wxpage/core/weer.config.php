<?php

function Weer8Ratio($arrid = 1){
	$rationArr = array(
		1 => array('0:0', '0:1', '0:2', '1:2', '1:0', '2:1', '2:0', '1:1', '2:2'),
		2 => array('0:0', '0:1', '0:2', '1:2', '1:0', '2:1', '2:0', '1:1', '2:2'),
		3 => array('0:0', '0:1', '0:2', '1:2', '1:0', '2:1', '2:0', '1:1', '2:2'),
		4 => array(0, 1, 2), //期内出现次数，其他都是算遗漏
		5 => array('111', '122', '112', '121', '212', '221', '222', '211'),
		6 => array('11', '22', '12', '21'),
		7 => array(
			'000', '011', '022', '110', '121', '202', '220',
			'001', '012', '100', '111', '122', '210', '221',
			'002', '020', '101', '112', '200', '211', '222',
			'010', '021', '102', '120', '201', '212',
		),
		8 => array(
			'000', '011', '022', '110', '121', '202', '220',
			'001', '012', '100', '111', '122', '210', '221',
			'002', '020', '101', '112', '200', '211', '222',
			'010', '021', '102', '120', '201', '212',
		),
	);

	return $rationArr[$arrid];
}

function GetWeerCfg($arrid = 1){
	// 第1、2、3步：红球位比值 按遗漏
	$weer_cfg_123 = array('0:0', '0:1', '0:2', '1:2', '1:0', '2:1', '2:0', '1:1', '2:2');
	$weer1 = array(
		'1-2' => $weer_cfg_123,
		'2-3' => $weer_cfg_123,
		'3-4' => $weer_cfg_123,
		'4-5' => $weer_cfg_123,
		'5-6' => $weer_cfg_123,
	);

	$weer2 = array(
		'1-3' => $weer_cfg_123,
		'1-4' => $weer_cfg_123,
		'1-5' => $weer_cfg_123,
		'1-6' => $weer_cfg_123,
	);

	$weer3 = array(
		'2-4' => $weer_cfg_123,
		'2-5' => $weer_cfg_123,
		'2-6' => $weer_cfg_123,
		'3-5' => $weer_cfg_123,
		'3-6' => $weer_cfg_123,
	);

	// 第4步：高尾数统计 出现次数
	$weer_cfg_4 = array(0, 1, 2);

	$weer4 = array(
		'1-2' => $weer_cfg_4,
		'3-4' => $weer_cfg_4,
		'5-6' => $weer_cfg_4,
		'2-5' => $weer_cfg_4,
		'1-6' => $weer_cfg_4,
	);

	// 第5步：按位间距奇偶 遗漏
	$weer_cfg_5 = array('111', '122', '112', '121', '212', '221', '222', '211');
	$weer5 = array(
		'按位间距奇偶' => $weer_cfg_5,
	);

	// 第6步：大小数和值 遗漏
	$weer_cfg_6 = array('11', '22', '12', '21');
	$weer6 = array(
		'大小数和值' => $weer_cfg_6,
	);

	// 第7步：首尾和差间距尾数012路 遗漏
	$weer_cfg_7 = array(
		'000', '011', '022', '110', '121', '202', '220',
		'001', '012', '100', '111', '122', '210', '221',
		'002', '020', '101', '112', '200', '211', '222',
		'010', '021', '102', '120', '201', '212',
	);
	sort($weer_cfg_7);
	$weer7 = array(
		'首尾和差间距尾数012路' => $weer_cfg_7,
	);

	// 第8步：位尾数和012路 遗漏
	$weer_cfg_8 = array(
		'000', '011', '022', '110', '121', '202', '220',
		'001', '012', '100', '111', '122', '210', '221',
		'002', '020', '101', '112', '200', '211', '222',
		'010', '021', '102', '120', '201', '212',
	);
	sort($weer_cfg_8);
	$weer8 = array(
		'位尾数和012路' => $weer_cfg_8,
	);

	$weercfg = 'weer' . $arrid;

	return isset($$weercfg) ? $$weercfg : array();
}

function GetWeerData($redarr = array())
{
	//红球012路
	$red012 = array();
	//红球尾数
	$redtail = array();
	//大小和值
	$redbsv = array('big'=>array(), 'small'=>array());
	foreach ($redarr as $pos => $red) {
		$red012[$pos+1] = $red % 3;
		$redtail[$pos+1] = $red % 10;
		$red > 17 && $redbsv['big'][] = $red;
		$red < 18 && $redbsv['small'][] = $red;
	}
	//按位间距
	$redspace = array('12'=>$redarr[1]-$redarr[0], '34'=>$redarr[3]-$redarr[2], '56'=>$redarr[5]-$redarr[4]);
	//首尾和差间距012路
	$redsumtail = array('sum1'=>$redarr[0]+$redarr[5], 'sum2'=>$redarr[5]-$redarr[0], 'sum3'=>array_sum($redtail));

	//位尾数和012路
	$redtailsum = array('12'=>$redtail[1]+$redtail[2], '34'=>$redtail[3]+$redtail[4], '56'=>$redtail[5]+$redtail[6]);


	$data = array();
	foreach (array(1,2,3) as $arrid) {
		$weer = GetWeerCfg($arrid);
		$weer = array_keys($weer);

		$data['weer'.$arrid] = array();
		foreach ($weer as $wbz) {
			$v = explode("-", $wbz);
			$data['weer'.$arrid][$wbz] = $red012[$v[0]].':'.$red012[$v[1]];
		}
	}

	$weer4 = GetWeerCfg(4);
	$weer4 = array_keys($weer4);

	$data['weer4'] = array();
	foreach ($weer4 as $wbz) {
		$v = explode("-", $wbz);
		$data['weer4'][$wbz] = 0;
		foreach ($v as $v2) {
			$redtail[$v2] > 4 && $data['weer4'][$wbz]++;
		}
	}

	$data['weer5'] = array('code'=>'');
	foreach ($redspace as $pos => $rs) {
		$data['weer5'][$pos] = $tmp = $rs % 2 == 0 ? 2 : 1;
		$data['weer5']['code'] .= $tmp;
	}

	$data['weer6'] = array('code'=>'');
	foreach ($redbsv as $bigs => $bsv) {
		$bsv = array_sum($bsv);
		$data['weer6'][$bigs] = $tmp = $bsv % 2 == 0 ? 2 : 1;
		$data['weer6']['code'] .= $tmp;
	}

	$data['weer7'] = array('code'=>'');
	foreach ($redsumtail as $index => $rst) {
		$data['weer7'][$index] = $tmp = $rst % 3;
		$data['weer7']['code'] .= $tmp;
	}

	$data['weer8'] = array('code'=>'');
	foreach ($redtailsum as $index => $rts) {
		$data['weer8'][$index] = $tmp = $rts % 3;
		$data['weer8']['code'] .= $tmp;
	}

	return $data;
}

// 微尔算法-八步过滤
function RedWeerFilter($redarr,$restcfg)
{
	//红球012路
	$red012 = array();
	//红球尾数
	$redtail = array();
	//大小和值
	$redbsv = array('big'=>array(), 'small'=>array());
	foreach ($redarr as $pos => $red) {
		$red012[$pos+1] = $red % 3;
		$redtail[$pos+1] = $red % 10;
		$red > 17 && $redbsv['big'][] = $red;
		$red < 18 && $redbsv['small'][] = $red;
	}
	//按位间距
	$redspace = array('12'=>$redarr[1]-$redarr[0], '34'=>$redarr[3]-$redarr[2], '56'=>$redarr[5]-$redarr[4]);
	//首尾和差间距012路
	$redsumtail = array('sum1'=>$redarr[0]+$redarr[5], 'sum2'=>$redarr[5]-$redarr[0], 'sum3'=>array_sum($redtail));

	//位尾数和012路
	$redtailsum = array('12'=>$redtail[1]+$redtail[2], '34'=>$redtail[3]+$redtail[4], '56'=>$redtail[5]+$redtail[6]);

	$break = false;
	$data = array();
	foreach (array(1,2,3) as $arrid) {
		$weer = GetWeerCfg($arrid);
		$weer = array_keys($weer);

		if(!isset($restcfg['weer'.$arrid])) break;
		$data['weer'.$arrid] = array();
		foreach ($weer as $wbz) {
			$v = explode("-", $wbz);
			$data['weer'.$arrid][$wbz] = $red012[$v[0]].':'.$red012[$v[1]];
			if(!in_array($data['weer'.$arrid][$wbz], $restcfg['weer'.$arrid][$wbz])){
				$break = true;
				break;
			}
		}
		if($break) return false;
	}

	if(isset($restcfg['weer4'])){
		$weer4 = GetWeerCfg(4);
		$weer4 = array_keys($weer4);

		$data['weer4'] = array();
		foreach ($weer4 as $wbz) {
			$v = explode("-", $wbz);
			$data['weer4'][$wbz] = 0;
			foreach ($v as $v2) {
				$redtail[$v2] > 4 && $data['weer4'][$wbz]++;
			}
			if(isset($restcfg['weer4'][$wbz]) && !in_array($data['weer4'][$wbz], $restcfg['weer4'][$wbz])){
				$break = true;
				break;
			}
		}
		if($break) return false;
	}

	if(isset($restcfg['weer5'])){
		$data['weer5'] = '';
		foreach ($redspace as $pos => $rs) {
			$data['weer5'] .= $rs % 2 == 0 ? 2 : 1;
		}
		if(!in_array($data['weer5'], $restcfg['weer5'])){
			$break = true;
		}
		if($break) return false;
	}
	

	if(isset($restcfg['weer6'])){
		$data['weer6'] = '';
		foreach ($redbsv as $bigs => $bsv) {
			$bsv = array_sum($bsv);
			$data['weer6'] .= $bsv % 2 == 0 ? 2 : 1;
		}
		if(!in_array($data['weer6'], $restcfg['weer6'])){
			$break = true;
		}
		if($break) return false;
	}

	if(isset($restcfg['weer7'])){
		$data['weer7'] = '';
		foreach ($redsumtail as $index => $rst) {
			$data['weer7'] .= $rst % 3;
		}
		if(!in_array($data['weer7'], $restcfg['weer7'])){
			$break = true;
		}
		if($break) return false;
	}

	if(isset($restcfg['weer8'])){
		$data['weer8'] = '';
		foreach ($redtailsum as $index => $rts) {
			$data['weer8'] .= $rts % 3;
		}
		if(!in_array($data['weer8'], $restcfg['weer8'])){
			$break = true;
		}
		if($break) return false;
	}
	

	return $redarr;
}

//八步过滤格式化
function FormatWeerCfg($myweercfg){
	$weercfg = $myweercfg;

	$restcfg = array();
	foreach ($weercfg as $index => $wcfg) {
		if(in_array($index, array('weer1','weer2','weer3','weer4'))){
			$tmp_wcfg = array();
			foreach ($wcfg as $index2 => $v) {
				$index2 = str_replace('_', '-', $index2);
				$tmp_wcfg[$index2] = $v;
			}
			$restcfg[$index] = $tmp_wcfg;
		}else{
			$restcfg[$index] = $wcfg;
		}
	}

	return $restcfg;
}

// 红球微尔算法简单版数据
function RedWeerData($redarr)
{
	//红球012路
	$red012 = array();
	//红球尾数
	$redtail = array();
	//大小和值
	$redbsv = array('big'=>array(), 'small'=>array());
	foreach ($redarr as $pos => $red) {
		$red012[$pos+1] = $red % 3;
		$redtail[$pos+1] = $red % 10;
		$red > 16 && $redbsv['big'][] = $red;
		$red < 17 && $redbsv['small'][] = $red;
	}
	//按位间距
	$redspace = array('12'=>$redarr[1]-$redarr[0], '34'=>$redarr[3]-$redarr[2], '56'=>$redarr[5]-$redarr[4]);
	//首尾和差间距012路
	$redsumtail = array('sum1'=>$redarr[0]+$redarr[5], 'sum2'=>$redarr[5]-$redarr[0], 'sum3'=>array_sum($redtail));

	//位尾数和012路
	$redtailsum = array('12'=>$redtail[1]+$redtail[2], '34'=>$redtail[3]+$redtail[4], '56'=>$redtail[5]+$redtail[6]);


	$data = array();
	foreach (array(1,2,3) as $arrid) {
		$weer = GetWeerCfg($arrid);
		$weer = array_keys($weer);

		$data['weer'.$arrid] = array();
		foreach ($weer as $wbz) {
			$v = explode("-", $wbz);
			$data['weer'.$arrid][$wbz] = $red012[$v[0]].':'.$red012[$v[1]];
		}
	}

	$weer4 = GetWeerCfg(4);
	$weer4 = array_keys($weer4);

	$data['weer4'] = array();
	foreach ($weer4 as $wbz) {
		$v = explode("-", $wbz);
		$data['weer4'][$wbz] = 0;
		foreach ($v as $v2) {
			$redtail[$v2] > 4 && $data['weer4'][$wbz]++;
		}
	}

	$data['weer5'] = '';
	foreach ($redspace as $pos => $rs) {
		$data['weer5'] .= $rs % 2 == 0 ? 2 : 1;
	}

	$data['weer6'] = '';
	foreach ($redbsv as $bigs => $bsv) {
		$bsv = array_sum($bsv);
		$data['weer6'] .= $bsv % 2 == 0 ? 2 : 1;
	}

	$data['weer7'] = '';
	foreach ($redsumtail as $index => $rst) {
		$data['weer7'] .= $rst % 3;
	}

	$data['weer8'] = '';
	foreach ($redtailsum as $index => $rts) {
		$data['weer8'] .= $rts % 3;
	}

	return $data;
}