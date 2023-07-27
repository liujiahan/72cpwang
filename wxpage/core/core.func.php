<?php

// 阶乘  
function factorial($n) {  
    return array_product(range(1, $n));  
}  
  
// 排列数  
function A($n, $m) {  
    return factorial($n)/factorial($n-$m);  
}  
  
// 组合数  
function C($n, $m) {  
    return A($n, $m)/factorial($m);  
}  
  
// 排列  
function arrangement($a, $m) {  
    $r = array();  
  
    $n = count($a);  
    if ($m <= 0 || $m > $n) {  
        return $r;  
    }  
  
    for ($i=0; $i<$n; $i++) {  
        $b = $a;  
        $t = array_splice($b, $i, 1);  
        if ($m == 1) {  
            $r[] = $t;  
        } else {  
            $c = arrangement($b, $m-1);  
            foreach ($c as $v) {  
                $r[] = array_merge($t, $v);  
            }  
        }  
    }  
  
    return $r;  
}  
  
// 组合  
function combination($a, $m) {  
    $r = array();  
  
    $n = count($a);  
    if ($m <= 0 || $m > $n) {  
        return $r;  
    }  
  
    for ($i=0; $i<$n; $i++) {  
        $t = array($a[$i]);  
        if ($m == 1) {  
            $r[] = $t;  
        } else {  
            $b = array_slice($a, $i+1);  
            $c = combination($b, $m-1);  
            foreach ($c as $v) {  
                $r[] = array_merge($t, $v);  
            }  
        }  
    }  
  
    return $r;  
}

// ====== 测试 ======  
// $a = array("A", "B", "C", "D");  
  
// $r = arrangement($a, 2);  
// var_dump($r);  
  
// $r = A(4, 2);  
// echo $r."\n";  
  
// $r = combination($a, 2);  
// var_dump($r);  
  
// $r = C(4, 2);  
// echo $r."\n";  