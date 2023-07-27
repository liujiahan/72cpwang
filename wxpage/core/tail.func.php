<?php

/**
 * 递归多数组排列组合
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
function arrCombination($arr){
    if(count($arr) >= 2){
        $tmparr = array();
        $arr1 = array_shift($arr);
        $arr2 = array_shift($arr);
        foreach($arr1 as $k1 => $v1){
            foreach($arr2 as $k2 => $v2){
                $tmparr[] = $v1.'.'.$v2;
            }
        }
        array_unshift($arr, $tmparr);
        $arr = arrCombination($arr);
    }else{
        return $arr;
    }
    return $arr;
}

/**
 * 尾数对应的红球数组
 * @param [type] $tails [description]
 */
function TailReds($tails){
    $tailReds = array();
    foreach ($tails as $tail) {
        $tailReds[$tail] = array();

        $tail != 0 && $tailReds[$tail][] = $tail;
        $tailReds[$tail][] = $tail+10;
        $tailReds[$tail][] = $tail+20;
        $tail + 30 <= 33 && $tailReds[$tail][] = $tail+30;
    }
    return $tailReds;
}

/**
 * 多维数组排列组合，并整理双色球6红格式
 * @param  [type] $tailReds [description]
 * @return [type]           [description]
 */
function allTailReds($tailReds){
    $alldata = arrCombination($tailReds)[0];

    foreach ($alldata as $key => &$tmp) {
        $tmp = explode('.', $tmp);
        sort($tmp);
        foreach ($tmp as &$v) {
            $v < 10 && $v = '0'.$v;
        }
        $tmp = implode('.', $tmp);
    }
    return $alldata;
}

/**
 * 六尾所有的排列组合
 * @param [type] $tails [description]
 */
function Red6Tail($tails){
    $tailReds = TailReds($tails);
    return allTailReds($tailReds);
}

/**
 * 五尾所有的排列组合
 * @param [type] $tails [description]
 */
function Red5Tail($tails){
    $tailReds = TailReds($tails);

    $alldata = array();
    $tailReds = array_values($tailReds);
    foreach ($tailReds as $k1 => $v1tail) {
        $allV1 = combination($v1tail, 2);

        $otherTail = array();
        foreach ($tailReds as $k2 => $v2tail) {
            if($k1 == $k2) continue;

            $otherTail[] = $v2tail;
        }
        
        foreach ($allV1 as $k3 => $v3tail) {
            $tmptail = array_merge($otherTail, array(array(implode('.',$v3tail))));
            $tmpreds = allTailReds($tmptail);
            $alldata = array_merge($alldata, $tmpreds);
        }
    }

    return $alldata;
}

/**
 * 四尾所有的排列组合
 * @param [type] $tails [description]
 */
function Red4Tail($tails){
    $tailReds = TailReds($tails);
    $tailReds = array_values($tailReds);
    $alldata = array();
    foreach ($tailReds as $k1 => $v1tail) {
        if(count($v1tail) <=3 ){
            $allV1 = array($v1tail);
        }else{
            $allV1 = combination($v1tail, 3);
        }

        $otherTail = array();
        foreach ($tailReds as $k2 => $v2tail) {
            if($k1 == $k2) continue;

            $otherTail[] = $v2tail;
        }

        foreach ($allV1 as $k3 => $v3tail) {
            $tmptail = array_merge($otherTail, array(array(implode('.',$v3tail))));
            $tmpreds = allTailReds($tmptail);
            $alldata = array_merge($alldata, $tmpreds);
        }
    }

    $tailKeys = array_keys($tailReds);
    $tailKeys = combination($tailKeys, 2);

    foreach ($tailKeys as $tk) {
        $allV1 = combination($tailReds[$tk[0]], 2);
        $allV2 = combination($tailReds[$tk[1]], 2);

        $allV12 = array();
        foreach ($allV1 as $v1) {
            foreach ($allV2 as $v2) {
                $allV12[] = array_merge($v1, $v2);
            }
        }

        $otherTail = array();
        foreach ($tailReds as $k2 => $v2tail) {
            if(in_array($k2, $tk)) continue;

            $otherTail[] = $v2tail;
        }

        foreach ($allV12 as $k3 => $v3tail) {
            $tmptail = array_merge($otherTail, array(array(implode('.',$v3tail))));
            $tmpreds = allTailReds($tmptail);
            $alldata = array_merge($alldata, $tmpreds);
        }
    }

    return $alldata;
}

/**
 * 三尾所有的排列组合
 * @param [type] $tails [description]
 */
function Red3Tail($tails){
    $tailReds = TailReds($tails);
    $tailReds = array_values($tailReds);
    $alldata = array();
    $has4 = false;
    foreach ($tailReds as $k1 => $v1tail) {
        if(count($v1tail)==4) {
            $has4 = true;
            break;
        }
    }

    $total = 0;
    foreach ($tailReds as $k1 => $v1tail) {
        foreach ($tailReds as $k2 => $v2tail) {
            foreach ($tailReds as $k3 => $v3tail) {
                if($k1 == $k2 || $k1 == $k3 || $k2 == $k3) continue;
                $total++;

                if($has4){
                    if(count($v1tail) == 4 ){
                        $allV1 = array(array(implode(".",$v1tail)));
                    }else{
                        $allV1 = combination($v1tail, 1);
                    }

                    $allV2 = combination($v2tail, 1);
                    $allV3 = combination($v3tail, 1);

                    $tmptail = array();
                    foreach ($allV1 as $v1) {
                        foreach ($allV2 as $v2) {
                            foreach ($allV3 as $v3) {
                            $tmptail = array_merge(array($v1), array($v2), array($v3));
                                $tmpreds = allTailReds($tmptail);
                                $alldata = array_merge($alldata, $tmpreds);
                            }
                        }
                    }
                }

                if(count($v1tail) == 3 ){
                    $allV1 = array(array(implode(".",$v1tail)));
                }else{
                    $allV1 = combination($v1tail, 3);
                }

                $allV2 = combination($v2tail, 2);
                $allV3 = combination($v3tail, 1);

                $tmptail = array();
                foreach ($allV1 as $v1) {
                    foreach ($allV2 as $v2) {
                        foreach ($allV3 as $v3) {
                            $tmptail = array_merge(array(array(implode(".",$v1))), array(array(implode(".",$v2))), array(array(implode(".",$v3))));
                            $tmpreds = allTailReds($tmptail);
                            $alldata = array_merge($alldata, $tmpreds);
                        }
                    }
                }

                $allV1 = combination($v1tail, 2);
                $allV2 = combination($v2tail, 2);
                $allV3 = combination($v3tail, 2);

                $tmptail = array();
                foreach ($allV1 as $v1) {
                    foreach ($allV2 as $v2) {
                        foreach ($allV3 as $v3) {
                            $tmptail = array_merge(array(array(implode(".",$v1))), array(array(implode(".",$v2))), array(array(implode(".",$v3))));
                            $tmpreds = allTailReds($tmptail);
                            $alldata = array_merge($alldata, $tmpreds);
                        }
                    }
                }
            }
        }
    }
    return array_unique($alldata);
}