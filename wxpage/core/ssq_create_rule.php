<?php

return array(
	//奇偶比
	'odd_even' => array(
		'1' => array(1, 5),
		'2' => array(2, 4),
		'3' => array(3, 3),
		'4' => array(4, 2),
		'5' => array(5, 1),
	),//大小比
	'big_small' => array(
		'1' => array(5, 1),
		'2' => array(4, 2),
		'3' => array(3, 3),
		'4' => array(2, 4),
		'5' => array(1, 5),
	),
	//除3余数出球个数
	'divide_3' => array(
		'0' => array(0, 1, 2, 3, 4),
		'1' => array(0, 1, 2, 3, 4),
		'2' => array(0, 1, 2, 3, 4),
	),
	//除6余数出球个数
	'divide_6' => array(
		'0' => array(0, 1, 2, 3),
		'1' => array(0, 1, 2, 3),
		'2' => array(0, 1, 2, 3),
		'3' => array(0, 1, 2, 3),
		'4' => array(0, 1, 2, 3),
		'5' => array(0, 1, 2, 3),
	),
	//冷热出球
	'cool_hot' => array(
		'at_least' => array('hot' => 2),
		'at_most'  => array('warm' => 3, 'cool' => 3),
		'before'   => array(
			'6_hot' => array(
				array('hot'=>4, 'warm'=>2, 'cool'=>0),
				array('hot'=>4, 'warm'=>1, 'cool'=>1),
			),
			'5_hot' => array(
				array('hot'=>4, 'warm'=>2, 'cool'=>0),
				array('hot'=>3, 'warm'=>2, 'cool'=>1),
			),
			'4_hot' => array(
				array('hot'=>5, 'warm'=>1, 'cool'=>0),
				array('hot'=>4, 'warm'=>1, 'cool'=>1),
				array('hot'=>3, 'warm'=>2, 'cool'=>1),
				array('hot'=>4, 'warm'=>2, 'cool'=>0),
				array('hot'=>4, 'warm'=>0, 'cool'=>2),
				array('hot'=>2, 'warm'=>4, 'cool'=>0),
			),
			'4_hot' => array(
				array('hot'=>5, 'warm'=>1, 'cool'=>0),
				array('hot'=>4, 'warm'=>1, 'cool'=>1),
				array('hot'=>3, 'warm'=>2, 'cool'=>1),
				array('hot'=>4, 'warm'=>2, 'cool'=>0),
			),
			'3_hot' => array(
				array('hot'=>4, 'warm'=>2, 'cool'=>0),
				array('hot'=>5, 'warm'=>1, 'cool'=>0),
				array('hot'=>3, 'warm'=>2, 'cool'=>1),
				array('hot'=>3, 'warm'=>1, 'cool'=>2),
				array('hot'=>3, 'warm'=>0, 'cool'=>3),
				array('hot'=>2, 'warm'=>2, 'cool'=>2),
				array('hot'=>2, 'warm'=>1, 'cool'=>3),
				array('hot'=>2, 'warm'=>3, 'cool'=>1),
			),
			'2_hot' => array(
				array('hot'=>4, 'warm'=>2, 'cool'=>0),
				array('hot'=>5, 'warm'=>1, 'cool'=>0),
				array('hot'=>6, 'warm'=>0, 'cool'=>0),
				array('hot'=>3, 'warm'=>2, 'cool'=>1),
				array('hot'=>3, 'warm'=>1, 'cool'=>2),
				array('hot'=>3, 'warm'=>0, 'cool'=>3),
			)
		),
	),
	//和值区间走势
	'sum' => array(
		'0' => array(21, 49),
		'1' => array(50, 59),
		'2' => array(60, 69),
		'3' => array(70, 79),
		'4' => array(80, 89),
		'5' => array(90, 99),
		'6' => array(100, 109),
		'7' => array(110, 119),
		'8' => array(120, 129),
		'9' => array(130, 139),
		'10' => array(140, 183),
	),
);