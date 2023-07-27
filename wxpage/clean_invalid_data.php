<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/core.func.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/core.func.php';
require_once dirname(__FILE__) . '/core/wuxing.func.php';
require_once dirname(__FILE__) . '/core/choosered.func.php';
require_once dirname(__FILE__) . '/core/wuxing.func.php';

$cp_dayid = !empty($cp_dayid) ? $cp_dayid : 0;
if(!$cp_dayid){
	exit('err.');
}

$id = array($cp_dayid);

//$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_history` WHERE cp_dayid in (" . implode(',', $id) . ")");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_3code` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_3code_missing` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_baihe` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_blue_choose` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_blue_fx` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_blue_whkill` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_blue_wuxing` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_blue_xsh` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_cool_hot` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_gold_analysis` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_kill_blue` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_newkill_blue2` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_red_edgecode` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_red_location_cross` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_red_percent` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_red_pinlv_fenqu` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_red_space_periods` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_red_tail` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_tail_cool_hot` WHERE cp_dayid>=$cp_dayid");
$dosql->ExecNoneQuery("DELETE FROM `lz_caipiao_wuxing` WHERE cp_dayid>=$cp_dayid");
