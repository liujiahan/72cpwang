<?php

function insertAll($data, $table_name, &$errmsg = '', $method = "INSERT")
{
	global $dosql;
	$sqlV = '';
	foreach ($data as $k => $v) {
		$sqlV .= '(';
		foreach ($v as $rowK => $rowV) {
			$sqlV .= "'$rowV',";
		}
		$sqlV = rtrim($sqlV, ',') . '),';
	}
	$sql = "$method into " . "$table_name (";
	foreach (array_keys($data[0]) as $k => $v) {
		$sql .= '`' . $v . '`' . ',';
	}
	$sql = rtrim($sql, ',') . ') values ';
	$sql .= rtrim($sqlV, ',');

	try {
		$dosql->ExecNoneQuery($sql);

		return true;
	} catch (\Exception $e) {
		$errmsg = $e->getMessage();
		return false;
	}
}

// 获取十位+个位的和
function getSumVal($red)
{
	$ten   = intval($red / 10);
	$digit = $red % 10;
	$sum   = $ten + $digit;

	if ($sum >= 10) {
		return getSumVal($sum);
	} else {
		return $sum;
	}
}

function getAC($data)
{
	$res = array();
	for ($i = 0; $i < count($data); $i++) {
		for ($j = $i + 1; $j < count($data); $j++) {
			if ($i != $j) {
				$res[] = abs($data[$i] - $data[$j]);
			}
		}
	}
	$res = array_unique($res);
	return count($res) - (6 - 1);
}

function RedSerialNumberV2($data, $lhdata = array(), $num = 0, $flag = 0)
{
	for ($i = $num; $i < count($data); $i++) {
		if (isset($data[$i + 1])) {
			if ($data[$i] + 1 == $data[$i + 1]) {
				$flag = $flag == 0 ? 2 : $flag + 1;

				//判断大于2连号是否存在
				if (isset($data[$i + 2])) {
					RedSerialNumberV2($data, $lhdata, $i + 1, $flag);
				} else {
					//最后flag是否有值的情况
					if ($flag) {
						if (!isset($lhdata[$flag])) {
							$lhdata[$flag] = 0;
						}
						$lhdata[$flag] += 1;
					}
				}
			} else {
				if ($flag) {
					if (!isset($lhdata[$flag])) {
						$lhdata[$flag] = 0;
					}
					$lhdata[$flag] += 1;
				}
				$flag = 0;
				//12位不是连号时，判断23位是不是连号
				if (isset($data[$i + 2])) {
					RedSerialNumberV2($data, $lhdata, $i + 1, $flag);
				}
			}
		}
	}
	return $lhdata;
}

function RedSerialNumber($red)
{
	$data = array(
		2 => array(
			$red[1] - $red[0] == 1 ? 1 : 0,
			$red[2] - $red[1] == 1 ? 1 : 0,
			$red[3] - $red[2] == 1 ? 1 : 0,
			$red[4] - $red[3] == 1 ? 1 : 0,
			$red[5] - $red[4] == 1 ? 1 : 0,
		),
		3 => array(
			$red[1] - $red[0] == 1 && $red[2] - $red[1] == 1 ? 1 : 0,
			$red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 ? 1 : 0,
			$red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 ? 1 : 0,
			$red[4] - $red[3] == 1 && $red[5] - $red[4] == 1 ? 1 : 0,
		),
		4 => array(
			$red[1] - $red[0] == 1 && $red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 ? 1 : 0,
			$red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 ? 1 : 0,
			$red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 && $red[5] - $red[4] == 1 ? 1 : 0,
		),
		5 => array(
			$red[1] - $red[0] == 1 && $red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 ? 1 : 0,
			$red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 && $red[5] - $red[4] == 1 ? 1 : 0,
		),
		6 => array(
			$red[1] - $red[0] == 1 && $red[2] - $red[1] == 1 && $red[3] - $red[2] == 1 && $red[4] - $red[3] == 1 && $red[5] - $red[4] == 1 ? 1 : 0,
		),
	);

	$lhdata = array(2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0);
	// foreach (array(6,5,4,3,2) as $lhindex) {
	foreach (array(2) as $lhindex) {
		$nums = array_sum($data[$lhindex]);
		$islianhao = $nums > 0 ? $nums : 0;
		if ($islianhao) {
			$lhdata[$lhindex] = $islianhao;
			// break;
		}
	}

	//有连号
	// $lhdata[1] = array_sum($lhdata) > 0 ? 1 : 0;

	return $lhdata;
}

function getLianHao($winReds)
{
	$two_num_near   = array();
	$three_num_near = array();
	$four_num_near  = array();
	$five_num_near  = array();
	$allRed = array();
	for ($i = 1; $i < 34; $i++) {
		$i < 10 && $i = '0' . $i;
		$allRed[] = $i;
	}

	foreach ($allRed as $key => $red) {
		if (isset($allRed[$key + 1])) {
			$two_num_near[$allRed[$key] . '_' . $allRed[$key + 1]] = 0;
		}
	}

	foreach ($allRed as $key => $red) {
		if (isset($allRed[$key + 1]) && isset($allRed[$key + 2])) {
			$three_num_near[$allRed[$key] . '_' . $allRed[$key + 1] . '_' . $allRed[$key + 2]] = 0;
		}
	}

	foreach ($allRed as $key => $red) {
		if (isset($allRed[$key + 1]) && isset($allRed[$key + 2]) && isset($allRed[$key + 3])) {
			$four_num_near[$allRed[$key] . '_' . $allRed[$key + 1] . '_' . $allRed[$key + 2] . '_' . $allRed[$key + 3]] = 0;
		}
	}

	foreach ($allRed as $key => $red) {
		if (isset($allRed[$key + 1]) && isset($allRed[$key + 2]) && isset($allRed[$key + 3]) && isset($allRed[$key + 4])) {
			$five_num_near[$allRed[$key] . '_' . $allRed[$key + 1] . '_' . $allRed[$key + 2] . '_' . $allRed[$key + 3] . '_' . $allRed[$key + 4]] = 0;
		}
	}

	$has5 = 0;
	foreach ($winReds as $key => $red) {
		if (isset($winReds[$key + 1]) && isset($winReds[$key + 2]) && isset($winReds[$key + 3]) && isset($winReds[$key + 4]) && isset($five_num_near[$red . '_' . $winReds[$key + 1] . '_' . $winReds[$key + 2] . '_' . $winReds[$key + 3] . '_' . $winReds[$key + 4]])) {
			$has5 = 1;
			$five_num_near[$red . '_' . $winReds[$key + 1] . '_' . $winReds[$key + 2] . '_' . $winReds[$key + 3] . '_' . $winReds[$key + 4]]++;
		}
	}

	$has4 = 0;
	if (!$has5) {
		foreach ($winReds as $key => $red) {
			if (isset($winReds[$key + 1]) && isset($winReds[$key + 2]) && isset($winReds[$key + 3]) && isset($four_num_near[$red . '_' . $winReds[$key + 1] . '_' . $winReds[$key + 2] . '_' . $winReds[$key + 3]])) {
				$has4 = 1;
				$four_num_near[$red . '_' . $winReds[$key + 1] . '_' . $winReds[$key + 2] . '_' . $winReds[$key + 3]]++;
			}
		}
	}

	$has3 = 0;
	if (!$has4) {
		foreach ($winReds as $key => $red) {
			if (isset($winReds[$key + 1]) && isset($winReds[$key + 2]) && isset($three_num_near[$red . '_' . $winReds[$key + 1] . '_' . $winReds[$key + 2]])) {
				$has3 = 1;
				$three_num_near[$red . '_' . $winReds[$key + 1] . '_' . $winReds[$key + 2]]++;
			}
		}
	}

	$has2 = 0;
	if (!$has3) {
		foreach ($winReds as $key => $red) {
			if (isset($winReds[$key + 1]) && isset($two_num_near[$red . '_' . $winReds[$key + 1]])) {
				$has2 = 1;
				$two_num_near[$red . '_' . $winReds[$key + 1]]++;
			}
		}
	}

	$has0 = 0;
	if (!($has2 || $has3 || $has4 || $has5)) {
		$has0 = 1;
	}

	$result = array(0 => $has0, 2 => $has2, 3 => $has3, 4 => $has4, 5 => $has5, 6 => 0);
	return $result;
}

function getCurMiss($cp_dayid = 0)
{
	global $dosql;
	$redMiss  = redMissing($cp_dayid);
	$row      = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
	$miss_sum = 0;
	$cool_hot = array('hot' => 0, 'warm' => 0, 'cool' => 0);

	if (isset($row['id'])) {
		$red_num = explode(',', $row['red_num']);
		foreach ($red_num as $red) {
			$tmp_miss = isset($redMiss[$red]) ? $redMiss[$red] : 0;
			$miss_sum += $tmp_miss;
			if ($tmp_miss >= 0 && $tmp_miss <= 4) {
				$cool_hot['hot']++;
			} else if ($tmp_miss >= 5 && $tmp_miss <= 9) {
				$cool_hot['warm']++;
			} else if ($tmp_miss > 9) {
				$cool_hot['cool']++;
			}
		}
	}
	return array('miss_sum' => $miss_sum, 'cool_hot' => $cool_hot, 'red_miss_arr' => $redMiss);
}

//红球遗漏
function redMissing($cp_dayid = 0)
{
	global $dosql;

	$allRed = array();
	for ($i = 1; $i < 34; $i++) {
		$i < 10 && $i = '0' . $i;
		$allRed[] = $i;
	}

	$redMiss = array();
	$sql = "SELECT * FROM `#@__caipiao_history` ";
	if (!empty($cp_dayid)) {
		$sql .= " WHERE cp_dayid<$cp_dayid";
	}
	$sql .= " ORDER BY cp_dayid DESC";

	$resIndex = 'index_' . rand(10000, 99999);

	$dosql->Execute($sql, $resIndex);
	$i = 0;
	$allball = array();
	while ($row = $dosql->GetArray($resIndex)) {
		$red_num = explode(',', $row['red_num']);
		foreach ($allRed as $tmp_red) {
			if (isset($allball[$tmp_red])) {
				continue;
			}
			if (!isset($redMiss[$tmp_red])) {
				$redMiss[$tmp_red] = 0;
			}
			if (!in_array($tmp_red, $red_num)) {
				$redMiss[$tmp_red]++;
			} else {
				if (!isset($allball[$tmp_red])) {
					$allball[$tmp_red] = 1;
				}
			}
		}
		if (count($allball) == 33) {
			break;
		}
		$i++;
	}
	return $redMiss;
}

//快乐8 红球遗漏
function happy8RedMissing($cp_dayid = 0)
{
	global $dosql;

	$allRed = array();
	for ($i = 1; $i <= 80; $i++) {
		$i < 10 && $i = '0' . $i;
		$allRed[] = $i;
	}

	$redMiss = array();
	$sql = "SELECT * FROM `#@__happy8_history` ";
	if (!empty($cp_dayid)) {
		$sql .= " WHERE cp_dayid<$cp_dayid";
	}
	$sql .= " ORDER BY cp_dayid DESC";

	$resIndex = 'index_' . rand(10000, 99999);

	$dosql->Execute($sql, $resIndex);
	$i = 0;
	$allball = array();
	while ($row = $dosql->GetArray($resIndex)) {
		$red_num = explode(',', $row['opencode']);
		foreach ($allRed as $tmp_red) {
			if (isset($allball[$tmp_red])) {
				continue;
			}
			if (!isset($redMiss[$tmp_red])) {
				$redMiss[$tmp_red] = 0;
			}
			if (!in_array($tmp_red, $red_num)) {
				$redMiss[$tmp_red]++;
			} else {
				if (!isset($allball[$tmp_red])) {
					$allball[$tmp_red] = 1;
				}
			}
		}
		if (count($allball) == 80) {
			break;
		}
		$i++;
	}
	return $redMiss;
}

// 三码组合遗漏
function threeCodeMissing($cp_dayid, $all3code)
{
	global $dosql;

	$all3code_miss = array();
	foreach ($all3code as $code) {
		$all3code_miss[$code] = 0;
	}

	$codes = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_3code` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC", "aaa");
	while ($row = $dosql->GetArray('aaa')) {
		$old_all3code = json_decode($row['all3code'], true);

		foreach ($all3code_miss as $code => $temp_num) {
			if (!in_array($code, $old_all3code) && !isset($codes[$code])) {
				$all3code_miss[$code]++;
				continue;
			}
			if (!isset($codes[$code])) {
				$codes[$code] = 1;
			}
		}
		if (count($all3code_miss) == count($codes)) {
			break;
		}
	}
	return $all3code_miss;
}

// 三码组合其中一码遗漏期数
function threeCodeMissingOnlyOne($code)
{
	global $dosql;

	$all3code_miss = array();
	$all3code_miss[$code] = 0;

	$codes = array();
	$dosql->Execute("SELECT * FROM `#@__caipiao_3code` ORDER BY cp_dayid DESC", "aaa");
	while ($row = $dosql->GetArray('aaa')) {
		$old_all3code = json_decode($row['all3code'], true);

		foreach ($all3code_miss as $code => $temp_num) {
			if (!in_array($code, $old_all3code) && !isset($codes[$code])) {
				$all3code_miss[$code]++;
				continue;
			}
			if (!isset($codes[$code])) {
				$codes[$code] = 1;
			}
		}
		if (count($all3code_miss) == count($codes)) {
			break;
		}
	}
	return $all3code_miss;
}

function getCurTailMiss($cp_dayid = 0)
{
	global $dosql;
	$redMiss  = redTailMissing($cp_dayid);
	$row      = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
	$miss_sum = 0;
	$cool_hot = array('hot' => 0, 'warm' => 0, 'cool' => 0);

	if (isset($row['id'])) {
		$red_num = explode(',', $row['red_num']);
		foreach ($red_num as $red) {
			$redtail = $red % 10;
			$tmp_miss = isset($redMiss[$redtail]) ? $redMiss[$redtail] : 0;
			$miss_sum += $tmp_miss;
			if ($tmp_miss == 0) {
				$cool_hot['hot']++;
			} else if ($tmp_miss >= 1 && $tmp_miss <= 2) {
				$cool_hot['warm']++;
			} else if ($tmp_miss > 2) {
				$cool_hot['cool']++;
			}
		}
	}
	return array('miss_sum' => $miss_sum, 'cool_hot' => $cool_hot, 'red_miss_arr' => $redMiss);
}

//红球尾数遗漏
function redTailMissing($cp_dayid = 0)
{
	global $dosql;

	$allTail = array();
	for ($i = 0; $i < 10; $i++) {
		$allTail[] = $i;
	}

	$redMiss = array();
	$sql = "SELECT * FROM `#@__caipiao_history` ";
	if (!empty($cp_dayid)) {
		$sql .= " WHERE cp_dayid<$cp_dayid";
	}
	$sql .= " ORDER BY cp_dayid DESC";

	$resIndex = 'index_' . rand(10000, 99999);

	$dosql->Execute($sql, $resIndex);
	$i = 0;
	$allball = array();
	while ($row = $dosql->GetArray($resIndex)) {
		$red_num = explode(',', $row['red_num']);
		$redtail = array();
		foreach ($red_num as $red) {
			$tmp = $red % 10;
			if (!in_array($tmp, $redtail)) $redtail[] = $tmp;
		}
		foreach ($allTail as $tail) {
			if (isset($allball[$tail])) {
				continue;
			}
			if (!isset($redMiss[$tail])) {
				$redMiss[$tail] = 0;
			}
			if (!in_array($tail, $redtail)) {
				$redMiss[$tail]++;
			} else {
				if (!isset($allball[$tail])) {
					$allball[$tail] = 1;
				}
			}
		}
		if (count($allball) == 10) {
			break;
		}
		$i++;
	}
	return $redMiss;
}

//蓝球遗漏
function blueMissing($cp_dayid = 0, $num = 123)
{
	global $dosql;

	$allBlue = array();
	for ($i = 1; $i < 17; $i++) {
		$i < 10 && $i = '0' . $i;
		$allBlue[] = $i;
	}

	$blueMiss = array();

	$sql = "SELECT * FROM `#@__caipiao_history` ";
	if (!empty($cp_dayid)) {
		$sql .= " WHERE cp_dayid<$cp_dayid";
	}
	$sql .= " ORDER BY cp_dayid DESC";

	$dosql->Execute($sql, $num);
	$i = 0;
	$allball = array();
	while ($row = $dosql->GetArray($num)) {
		$blue_num = $row['blue_num'];
		foreach ($allBlue as $tmp_blue) {
			if (isset($allball[$tmp_blue])) {
				continue;
			}
			if (!isset($blueMiss[$tmp_blue])) {
				$blueMiss[$tmp_blue] = 0;
			}
			if ($tmp_blue != $blue_num) {
				$blueMiss[$tmp_blue]++;
			} else {
				if (!isset($allball[$tmp_blue])) {
					$allball[$tmp_blue] = 1;
				}
			}
		}
		if (count($allball) == 16) {
			break;
		}
		$i++;
	}
	return $blueMiss;
}

//正选号主选号
function plusminusGetBlue($blue1, $blue2, $blue3)
{
	$first_choose = array(); //正选号
	$second_choose = array(); //主选号

	$blue_sum1 = $blue1 + $blue2 + $blue3;
	$blue_sum2 = abs($blue1 - $blue2 + $blue3);
	$blue_sum3 = abs($blue1 + $blue2 - $blue3);
	$blue_sum4 = abs($blue3 - ($blue1 - $blue2));

	$blue_sum_arr = array($blue_sum1, $blue_sum2, $blue_sum3, $blue_sum4);
	$new_blue_sum_arr = array();
	foreach ($blue_sum_arr as $blue_sum) {
		$blue_sum > 16 && $blue_sum = $blue_sum % 10;
		$blue_sum == 0 && $blue_sum = 10;
		$first_choose[] = $blue_sum;
		$new_blue_sum_arr[] = $blue_sum;
	}

	foreach ($new_blue_sum_arr as $blue_sum) {
		if ($blue_sum == 1) {
			$second_choose[] = 2;
			$second_choose[] = 16;
		} else if ($blue_sum == 16) {
			$second_choose[] = 1;
			$second_choose[] = 15;
		} else {
			$second_choose[] = $blue_sum - 1;
			$second_choose[] = $blue_sum + 1;
		}
	}
	return array('first_choose' => array_unique($first_choose), 'second_choose' => array_unique($second_choose));
}

//1、 A+B 绝杀法
//绝杀公式：L=A+B
function killBlue1($blue1, $blue2)
{
	$kill_blue = 0;
	$blue_sum = $blue1 + $blue2;
	if ($blue_sum < 10) {
		$kill_blue = $blue_sum;
	} else if ($blue_sum >= 10) {
		$yushu = $blue_sum % 10;
		$kill_blue = $yushu == 0 ? 10 : $yushu;
	}
	return $kill_blue;
}


// 2、 A+16 绝杀法
// 绝杀公式：L=A+16
function killBlue2($blue1)
{
	$kill_blue = 0;
	$blue_sum  = $blue1 + 16;
	$yushu     = $blue_sum % 10;
	$kill_blue = $yushu == 0 ? 10 : $yushu;
	return $kill_blue;
}

// 3、 A+B+C 绝杀法
// 绝杀公式：L=A+B+C
function killBlue3($blue1, $blue2, $blue3)
{
	$kill_blue = 0;
	$blue_sum  = $blue1 + $blue2 + $blue3;
	$yushu     = $blue_sum % 10;
	$kill_blue = $yushu == 0 ? 10 : $yushu;
	return $kill_blue;
}

// 4 A+B+C+D 绝杀法
// 绝杀公式：L=A+B+C+D
function killBlue4($blue1, $blue2, $blue3, $blue4)
{
	$kill_blue = 0;
	$blue_sum  = $blue1 + $blue2 + $blue3 + $blue4;
	$yushu     = $blue_sum % 10;
	$kill_blue = $yushu == 0 ? 10 : $yushu;
	return $kill_blue;
}

// 5、 红球+篮球 绝杀法
// 绝杀公式：L=红球+篮球
function killBlue5($blue1, $red_1_6)
{
	$kill_blue = 0;
	$blue_sum  = $red_1_6 + $blue1;
	$yushu     = $blue_sum % 10;
	$kill_blue = $yushu == 0 ? 10 : $yushu;
	return $kill_blue;
}

/*6、上期篮球 S+G 绝杀法
绝杀公式： L=S+G
L=本期绝杀篮球
S=上期篮球的十位数字
G=上期篮球的个位数字
绝杀原理：1）上期篮球大于10，十位和个位直接相加，取和绝杀 
2）上期篮球小于或等于10，先加16，如果相加后尾数
为0时，再加16，后将得数的十位和个位相加，相加
后的和为绝杀数。*/
function killBlue6($blue1)
{
	$kill_blue = 0;
	if ($blue1 > 10) {
		$kill_blue = intval($blue1 / 10) + $blue1 % 10;
	} else if ($blue1 <= 10) {
		$blue1 = $blue1 + 16;
		if ($blue1 % 10 == 0) {
			$blue1 = $blue1 + 16;
		}
		$blue_10d = intval($blue1 / 10);
		$blue_1d  = $blue1 % 10;
		$kill_blue = $blue_10d + $blue_1d;
	}
	return $kill_blue;
}

/*
7 上期篮球 S-G 绝杀法
绝杀公式：L=S-G
L=本期绝杀篮球
S=上期篮球十位数字
G=上期篮球个位数字
绝杀原理：上期篮球大于10时，十位数字和个位数字直接相减。上期篮球
小于或等于10时，先加16，如果相加后尾数为0，在加16，然后
将得数的十位和个位相减。相减后不论正负数，取绝对值作为
绝杀号。相减后差为0，都绝杀10
*/
function killBlue7($blue1)
{
	$kill_blue = 0;
	if ($blue1 > 10) {
		$kill_blue = intval($blue1 / 10) - $blue1 % 10;
	} else if ($blue1 <= 10) {
		$blue1 = $blue1 + 16;
		if ($blue1 % 10 == 0) {
			$blue1 = $blue1 + 16;
		}
		$blue_10d = intval($blue1 / 10);
		$blue_1d  = $blue1 % 10;
		$kill_blue = $blue_10d - $blue_1d;
	}
	$kill_blue = abs($kill_blue);
	$kill_blue = $kill_blue == 0 ? 10 : $kill_blue;
	return $kill_blue;
}

/*
8、上期篮球 S*G 绝杀法
绝杀公式：L=S*G
L=本期要绝杀的篮球
S=上期篮球的十位数字
G=上期篮球的个位数字
绝杀原理：上期篮球大于10时，十位数字和个位数字直接相乘。上期
篮球等于10时，加16，将得数的十位和个位数字相乘，积
为本期要杀的篮球号码。上期篮球小于10时，直接乘以16
积为16时，绝杀16，积大于16时，取个位即尾数进行绝杀
*/
function killBlue8($blue1)
{
	$kill_blue = 0;
	if ($blue1 > 10) {
		$kill_blue = intval($blue1 / 10) * $blue1 % 10;
	} else if ($blue1 < 10) {
		$blue1 = $blue1 * 16;
		if ($blue1 == 16) {
			$kill_blue = 16;
		} else if ($blue1 > 16) {
			$kill_blue = $blue1 % 10 == 0 ? 10 : $blue1 % 10;
		}
	} else if ($blue1 == 10) {
		$blue1 = $blue1 + 16;
		$kill_blue = intval($blue1 / 10) * $blue1 % 10;
	}
	return $kill_blue;
}



//用15减去上期蓝球号码，得出的数的尾数就是下期要杀的蓝号尾数。15 19 21
function NewkillBlue1($sqblue, $num = 15)
{
	$killBlue = array();
	if (!$sqblue) {
		return $killBlue;
	}

	$tailnum = abs($num - $sqblue) % 10;
	$tailnum && $killBlue[] = $tailnum;
	$tailnum + 10 <= 16 && $killBlue[] = $tailnum + 10;

	return $killBlue;
}

//用上两期蓝号的头和尾相加的数的尾数，即为下期要杀的尾数。
function NewkillBlue2($sqblue1, $sqblue2)
{
	$killBlue = array();
	if (!($sqblue1 && $sqblue2)) {
		return $killBlue;
	}

	$header = intval($sqblue2 / 10);
	$tailnum = $sqblue1 % 10;

	$tailnum = $header + $tailnum;
	$tailnum && $killBlue[] = $tailnum;
	$tailnum + 10 <= 16 && $killBlue[] = $tailnum + 10;

	return $killBlue;
}

//用上两期蓝号的尾和头相加的数的尾数，即为下期要杀的尾数。
function NewkillBlue3($sqblue1, $sqblue2)
{
	$killBlue = array();
	if (!($sqblue1 && $sqblue2)) {
		return $killBlue;
	}

	$header = intval($sqblue1 / 10);
	$tailnum = $sqblue2 % 10;

	$tailnum = $header + $tailnum;
	$tailnum && $killBlue[] = $tailnum;
	$tailnum + 10 <= 16 && $killBlue[] = $tailnum + 10;

	return $killBlue;
}

//用上二期蓝号尾相加得出的数的尾数，即为下期要杀的尾数。
function NewkillBlue4($sqblue1, $sqblue2)
{
	$killBlue = array();
	if (!($sqblue1 && $sqblue2)) {
		return $killBlue;
	}

	$tailnum1 = $sqblue1 % 10;
	$tailnum2 = $sqblue2 % 10;

	$tailnum = $tailnum1 + $tailnum2;
	$tailnum && $killBlue[] = $tailnum;
	$tailnum + 10 <= 16 && $killBlue[] = $tailnum + 10;

	return $killBlue;
}

//用上期蓝号尾与隔一期蓝号尾相加得出的数的尾数，即为下期要杀的尾数。
function NewkillBlue44($sqblue1, $sqblue3)
{
	$killBlue = array();
	if (!($sqblue1 && $sqblue3)) {
		return $killBlue;
	}

	$tailnum1 = $sqblue1 % 10;
	$tailnum3 = $sqblue3 % 10;

	$tailnum = $tailnum1 + $tailnum3;
	$tailnum && $killBlue[] = $tailnum;
	$tailnum + 10 <= 16 && $killBlue[] = $tailnum + 10;

	return $killBlue;
}

//用上期蓝号乘以2得出的数的尾数，即为下期要杀的尾数。
//用上期蓝号乘以4得出的数的尾数，即为下期要杀的尾数。
function NewkillBlue5($sqblue1, $num = 2)
{
	$killBlue = array();
	if (!$sqblue1) {
		return $killBlue;
	}

	$tailnum = ($sqblue1 * $num) % 10;

	$tailnum && $killBlue[] = $tailnum;
	$tailnum + 10 <= 16 && $killBlue[] = $tailnum + 10;

	return $killBlue;
}

//用上期蓝号加7或减7，注意蓝号大于14则减7，小于14则加7，得出的数的尾数，即为下期要杀的尾数。
function NewkillBlue6($sqblue1, $num = 7)
{
	$killBlue = array();
	if (!$sqblue1) {
		return $killBlue;
	}

	// $tmpnum = $sqblue1;

	// $sqblue1 > 12 && $tmpnum -= $num;
	// $sqblue1 < 12 && $tmpnum += $num;

	// $tailnum = $tmpnum % 10;
	// $tailnum && $killBlue[] = $tailnum;
	// $tailnum+10 <= 16 && $killBlue[] = $tailnum+10;

	// $tmpnum = $sqblue1;

	// $sqblue1 > 13 && $tmpnum -= $num;
	// $sqblue1 < 13 && $tmpnum += $num;

	// $tailnum = $tmpnum % 10;
	// $tailnum && $killBlue[] = $tailnum;
	// $tailnum+10 <= 16 && $killBlue[] = $tailnum+10;

	$tmpnum = $sqblue1;

	$sqblue1 > 14 && $tmpnum -= $num;
	$sqblue1 < 14 && $tmpnum += $num;

	$tailnum = $tmpnum % 10;
	$tailnum && $killBlue[] = $tailnum;
	$tailnum + 10 <= 16 && $killBlue[] = $tailnum + 10;

	// $tmpnum = $sqblue1;

	// $sqblue1 > 15 && $tmpnum -= $num;
	// $sqblue1 < 15 && $tmpnum += $num;

	// $tailnum = $tmpnum % 10;
	// $tailnum && $killBlue[] = $tailnum;
	// $tailnum+10 <= 16 && $killBlue[] = $tailnum+10;

	$killBlue = array_unique($killBlue);
	sort($killBlue);

	return $killBlue;
}

// 用上期蓝号加2得出的数的尾数，即为下期要杀的蓝号尾数。
// 用上期蓝号加6等于的数的尾数，就是下期蓝号要杀的尾数。 2 / 6
function NewkillBlue7($sqblue1, $num = 2)
{
	$killBlue = array();
	if (!$sqblue1) {
		return $killBlue;
	}

	$tailnum = ($sqblue1 + $num) % 10;

	$tailnum && $killBlue[] = $tailnum;
	$tailnum + 10 <= 16 && $killBlue[] = $tailnum + 10;

	return $killBlue;
}

// NewkillBlue1($sqblue1, 15);
// NewkillBlue1($sqblue1, 19);
// NewkillBlue1($sqblue1, 21);
// NewkillBlue2($sqblue1, $sqblue2);
// NewkillBlue3($sqblue1, $sqblue2);
// NewkillBlue4($sqblue1, $sqblue2);
// NewkillBlue5($sqblue1, 2);
// NewkillBlue5($sqblue1, 4);
// NewkillBlue6($sqblue1, 6);
// NewkillBlue7($sqblue1, 2);
// NewkillBlue7($sqblue1, 6);