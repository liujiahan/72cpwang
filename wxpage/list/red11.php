<?php

require_once dirname(__FILE__).'/../../include/config.inc.php';
require_once dirname(__FILE__).'/../core/ssq.config.php';

$redcfg = array();
for ($i=1; $i <= 11; $i++) { 
	$i < 10 && $i = '0'.$i;
	$redcfg[$i] = array();
}

foreach ($redcfg as $red => $v) {
	$shi = intval($red / 10);
	$ge  = $red % 10;
	$redcfg[$red][] = ($shi+1)*10+$ge+1;
	$redcfg[$red][] = ($shi+2)*10+$ge+2;
}

$max = $dosql->GetOne("SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_history`");
$nextid = $max['cp_dayid'];
$nextid = nextCpDayId($nextid);

$title = $nextid . "期 红球11对数表 和 最大红球减最小红球差值绝杀红球";

$pgnum = 20;

$limit = $pgnum + 1;

$result = array();
// $dosql->Execute("SELECT id, cp_dayid, red_num, blue_num FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $limit");
$dosql->Execute("SELECT id, cp_dayid, red_num, blue_num FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
while($row = $dosql->GetArray()){
	$row['red_num'] = explode(',', $row['red_num']);
	$result[] = $row;
}

$jueshaTotal = 0;
$jueshaOk = 0;

$list = array();
$js_list = array();
foreach ($result as $k => $v) {
	$maxred = $v['red_num'][5];
	$minred = $v['red_num'][0];
	$blue = $v['blue_num'];
	$diffnum = $maxred - $blue;
	if($diffnum == 0){
		$diffnum = 10;
	}
	$diffnum1 = intval($diffnum / 10);
	$diffnum2 = $diffnum % 10;

	$sum = $diffnum1 + $diffnum2;
	$sum < 10 && $sum = '0' . $sum;
	$killRed = $redcfg[$sum];

	$next_cpdayid = isset($result[$k+1]['cp_dayid']) ? $result[$k+1]['cp_dayid'] : $v['cp_dayid'] + 1;
	if($k == count($result)-1) $next_cpdayid = $nextid;
	$tmp = array();
	$tmp['cp_dayid'] = $next_cpdayid;
	$tmp['killred']  = $killRed;
	$tmp['winnum']   = 0;
	$tmp['open']     = 0;

	$tmp2 = array();
	$tmp2['cp_dayid'] = $next_cpdayid;
	$tmp2['killred']  = $maxred - $minred;
	$tmp2['winnum']   = 0;
	$tmp2['open']     = 0;

	if(isset($result[$k+1])){
		$tmp['open'] = 1;
		$nextRed = $result[$k+1]['red_num'];
		foreach ($killRed as $red) {
			if(!in_array($red, $nextRed)){
				$tmp['winnum']++;
			}
		}

		$tmp2['open'] = 1;
		if(!in_array($tmp2['killred'], $nextRed)){
			$tmp2['winnum'] = 1;
		}
	}
	$list[] = $tmp;
	$js_list[] = $tmp2;
}

$total = 0;
$kill2ok = 0;
$kill1ok = 0;
$kill0ok = 0;
foreach ($list as $k => $v) {
	if($v['open'] == 1){
		$total++;
		$v['winnum'] == 2 && $kill2ok++;
		$v['winnum'] == 1 && $kill1ok++;
		$v['winnum'] == 0 && $kill0ok++;
	}
}

$js_total2 = 0;
$js_killwin = 0;
foreach ($js_list as $k => $v) {
	if($v['open'] == 1){
		$js_total2++;
		$v['winnum'] == 1 && $js_killwin++;
	}
}

// echo round($js_killwin / $js_total2, 4) * 100 . '%';die;

$list = array_slice($list, count($list) - 21, count($list));

$js_list = array_slice($js_list, count($js_list) - 21, count($js_list));

// print_r($js_list);die;

// echo $total;
// echo "<br/>";
// echo '全部杀对 '.$kill2ok.'次 占比：' . round($kill2ok / $total, 4) * 100 . '%';
// echo "<br/>";
// echo '杀对1个 '.$kill1ok.'次 占比：' . round($kill1ok / $total, 4) * 100 . '%';
// echo "<br/>";
// echo '全部杀错 '.$kill0ok.'次 占比：' . round($kill0ok / $total, 4) * 100 . '%';
// echo "<br/>";


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>
</head>
<body>
<h3><?php echo $title ?></h3>

<p><strong>杀法一：绝杀红球 = 红球最大号码 - 蓝球号码的得数，得数十位和个位数相加，对照红球11对数表杀2码（当持续多期正确的时候，反之大家要作为胆码）</strong></p>
<h1>红球11对数表</h1>
<p>
<?php foreach ($redcfg as $red => $redv) { ?>
	<?php if($red % 3 == 0){ ?>
	<?php echo $red . ' = ' . implode(' ', $redv);  ?></p><p>
	<?php }else{ ?>
	<?php echo $red . ' = ' . implode(' ', $redv);  ?>&nbsp;&nbsp;&nbsp;&nbsp;
	<?php } ?>
<?php } ?>
</p>
<p><strong>=====近<?php echo $pgnum; ?>期杀法一绝杀红球结果======</strong></p>

<?php 
foreach ($list as $v) {
	$str = '';
	$v['winnum'] == 2 && $str = '正确';
	$v['winnum'] == 1 && $str = '杀错1个';
	$v['winnum'] == 0 && $v['open'] == 1 && $str = '杀错2个';
	$v['open'] == 0 && $str = '等待验证......';
	echo "<p>{$v['cp_dayid']}期 杀{$v['killred'][0]} {$v['killred'][1]} （{$str}）</p>";
}
 ?>
<h1>杀法一数据统计：在双色球共计<?php echo $total ?>期当中</h1>
<blockquote>
	<?php 
	$kill2rate = round($kill2ok / $total, 4) * 100 . '%';
	$kill1rate = round($kill1ok / $total, 4) * 100 . '%';
	$kill0rate = round($kill0ok / $total, 4) * 100 . '%';
	$kill21rate = ($kill2rate + $kill1rate) . '%' ;
	echo '<p>全部杀对 '.$kill2ok.'次 占比：' . $kill2rate . '</p>';
	echo '<p>杀对1个 '.$kill1ok.'次 占比：' . $kill1rate . '</p>';
	echo '<p>全部杀错 '.$kill0ok.'次 占比：' . $kill0rate . '</p>';
	?>
</blockquote>
<h1><strong>杀法一结论</strong></h1>
<p><strong>全部杀对 + 杀对一个的概率是<?php echo $kill21rate ?>，全部杀错占<?php echo $kill0rate ?>，数据的参考价值大家自我斟酌。（瞅准杀错时机，把握胆码，轻松拿下红球。）</strong></p>

<!-- 绝杀2开始 -->
<p>&nbsp;</p>
<p><strong>========杀法二介绍和统计========</strong></p>
<p><strong>杀法二：绝杀红球 = 红球最大码 - 红球最小码（当持续多期正确的时候，反之大家要作为胆码）</strong></p>
<p><strong>=====近<?php echo $pgnum; ?>期杀法二绝杀红球结果======</strong></p>

<?php 
foreach ($js_list as $v) {
	$str = '';
	$v['winnum'] == 1 && $str = '正确';
	$v['winnum'] == 0 && $str = '杀错';
	$v['open'] == 0 && $str = '等待验证......';
	echo "<p>{$v['cp_dayid']}期 杀{$v['killred']} （{$str}）</p>";
}
 ?>
<h1>杀法二数据统计：在双色球共计<?php echo $js_total2 ?>期当中</h1>
<blockquote>
<?php 
$jskillrate = round($js_killwin / $js_total2, 4) * 100 . '%';
echo '<p>杀对 '.$js_killwin.'次 占比：' . $jskillrate . '</p>';
?>
</blockquote>
<h1><strong>杀法二结论</strong></h1>
<p><strong>杀法二正确率占<?php echo $jskillrate ?>，虽然不是太高，但依然有参考价值，我们只要把握住时机，期期杀准确，时时把握胆码。</strong></p>

<?php 
$kill1 = end($list);
$kill2 = end($js_list);
?>
<h1>本期主杀：<?php echo implode(" ", $kill1['killred']) . ' ' . $kill2['killred'] ?></h1>
<p>如果数据对你有所帮助，请持续关注！本资料也会持续更新。</p>
<!-- <p>&nbsp;</p> -->
</body>
</html>