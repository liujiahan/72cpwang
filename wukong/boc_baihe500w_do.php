<?php 
require_once(dirname(__FILE__).'/inc/config.inc.php');
require_once(SNRUNNING_ROOT.'/library/excel/ExcelData.php');

$excel = new ExcelData();

if($action == 'baihe_500w'){
	$filePath = $_POST['excel'];
	//Excel 数据字段
	$fields = array(
		'cp_dayid', 'ssq', 'datafrom'
	);									

	$dataList = $excel->read($filePath, $fields);

	if(empty($dataList)){
		exit('空数据，请重新上传文件！');
	}

	$sqlData = array();
	foreach ($dataList as $row) {
		$cp_dayid = $row['cp_dayid'];
		$ssq      = $row['ssq'];
		$datafrom = $row['datafrom'];

		$sqlData[] = '("'.$cp_dayid.'","'.$ssq.'","'.$datafrom.'")';
	}

	$total = 0;
	if(!empty($sqlData)){
		$tmpt = count($sqlData);
		$sqlData = implode(',', $sqlData);

		$sql = "INSERT INTO `lz_caipiao_weermy_500w` (cp_dayid,ssq,datafrom) VALUES " . $sqlData;
		if($dosql->ExecNoneQuery($sql)){
			$total += $tmpt;
		}
	}
	echo '上传完毕，写入'.$total.'条';

	exit;
}