<?php	require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('amazeweer');

//初始化参数
$tbname = '#@__caipiao_weermy_blue';
$gourl  = 'amazeweer_blue.php';
$action = isset($action) ? $action : '';

if($action == 'add')
{
	$row = $dosql->GetOne("SELECT * FROM `$tbname` WHERE cp_dayid='$cp_dayid'");

	if(isset($row['id'])){
		ShowMsg("本期蓝号已设定！", '-1');
		exit;
	}

	$blueinfo = array();
	$gailvarr = array();
	$gailvs   = array();

	$index = 1;
	foreach ($blues as $v) {
		if(empty($v['blue']) || empty($v['gailv'])) continue;

		$gailv = array();
		for ($i=1 ; $i <= $v['gailv'] ; $i++ ) { 
			$gailv[] = $index;
		}
		$blueinfo[$index] = $v['blue'];
		$gailvarr[$index] = $v['gailv'];

		$gailvs = array_merge($gailvs, $gailv);

		$index++;
	}

	$blueinfo = serialize($blueinfo);
	$gailvarr = serialize($gailvarr);
	$gailvs   = serialize($gailvs);

	$bluematch = isset($bluematch) && $bluematch == 1 ? 1 : 0;

	$rest = $dosql->ExecNoneQuery("INSERT INTO `$tbname` (cp_dayid, bluenum, bluematch, blueinfo, gailvarr, gailvs) VALUES ('$cp_dayid', '$bluenum', '$bluematch', '$blueinfo', '$gailvarr', '$gailvs')");
	if($rest){
		ShowMsg("蓝号设定成功！", $gourl);
		exit;
	}
}

else if($action == 'blue_update')
{
	if($id)
	{
		$blueinfo = array();
		$gailvarr = array();
		$gailvs   = array();

		$index = 1;
		foreach ($blues as $v) {
			if(empty($v['blue']) || empty($v['gailv'])) continue;

			$gailv = array();
			for ($i=1 ; $i <= $v['gailv'] ; $i++ ) { 
				$gailv[] = $index;
			}
			$blueinfo[$index] = $v['blue'];
			$gailvarr[$index] = $v['gailv'];

			$gailvs = array_merge($gailvs, $gailv);

			$index++;
		}

		$bluematch = isset($bluematch) && $bluematch == 1 ? 1 : 0;

		$blueinfo = serialize($blueinfo);
		$gailvarr = serialize($gailvarr);
		$gailvs   = serialize($gailvs);

		$dosql->ExecNoneQuery("UPDATE `$tbname` SET bluematch='$bluematch', bluenum='$bluenum', blueinfo='$blueinfo', gailvs='$gailvs', gailvarr='$gailvarr' WHERE id='$id'");
	}
	header("location:$gourl");
	exit();
}

else if($action == 'del')
{
	$dosql->ExecNoneQuery("DELETE FROM `$tbname` WHERE `userid`='$userid' ");
	header("location:$gourl");
	exit();
	
}


//无条件返回
else
{
    header("location:$gourl");
	exit();
}
?>