<?php


require_once dirname(__FILE__).'/../include/config.inc.php';
// echo GetIP();die;
require_once dirname(__FILE__).'/core/core.func.php';

require_once dirname(__FILE__).'/core/wuxing.func.php';
require_once dirname(__FILE__).'/core/choosered.func.php';
require_once dirname(__FILE__).'/core/wuxing.func.php';
require_once dirname(__FILE__).'/core/fourMagic.func.php';
// require_once dirname(__FILE__).'/core/weer.config.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/redindexV2.func.php';
require_once dirname(__FILE__).'/core/weer.func.php';

function RedSerialNumber($red){
	$data = array(
		2 => array(
			$red[1]-$red[0] == 1 ? 1 : 0,
			$red[2]-$red[1] == 1 ? 1 : 0,
			$red[3]-$red[2] == 1 ? 1 : 0,
			$red[4]-$red[3] == 1 ? 1 : 0,
			$red[5]-$red[4] == 1 ? 1 : 0,
		),
		3 => array(
			$red[1]-$red[0] == 1 && $red[2] - $red[1] == 1 ? 1 : 0,
			$red[2]-$red[1] == 1 && $red[3] - $red[2] == 1 ? 1 : 0,
			$red[3]-$red[2] == 1 && $red[4] - $red[3] == 1 ? 1 : 0,
			$red[4]-$red[3] == 1 && $red[5] - $red[4] == 1 ? 1 : 0,
		),
		4 => array(
			$red[1]-$red[0] == 1 && $red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 ? 1 : 0,
			$red[2]-$red[1] == 1 && $red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 ? 1 : 0,
			$red[3]-$red[2] == 1 && $red[4] - $red[3] == 1 && $red[5] - $red[4] == 1 ? 1 : 0,
		),
		5 => array(
			$red[1]-$red[0] == 1 && $red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 ? 1 : 0,
			$red[2]-$red[1] == 1 && $red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 && $red[5] - $red[4] == 1 ? 1 : 0,
		),
		6 => array(
			$red[1]-$red[0] == 1 && $red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 && $red[5] - $red[4] == 1 ? 1 : 0,
		),
	);

	$lhdata = array(2=>0,3=>0,4=>0,5=>0,6=>0);
	foreach (array(6,5,4,3,2) as $lhindex) {
		$islianhao = array_sum($data[$lhindex]) > 0 ? 1 : 0;
		if($islianhao){
			$lhdata[$lhindex] = $islianhao;
			break;
		}
	}

	$lhdata[1] = array_sum($lhdata) > 0 ? 0 : 1;

	return $lhdata;
}

$red = array(19, 202, 221, 2222, 23, 224);
$lianhao = RedSerialNumber($red);
print_r($lianhao);