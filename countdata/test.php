<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/core.func.php';
require_once dirname(__FILE__).'/../wxpage/core/wuxing.func.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';
require_once dirname(__FILE__).'/../wxpage/core/wuxing.func.php';
require_once dirname(__FILE__).'/../wxpage/core/fourMagic.func.php';
require_once dirname(__FILE__).'/../wxpage/core/suanfa.func.php';
require_once dirname(__FILE__).'/../wxpage/core/redindexV2.func.php';
require_once dirname(__FILE__).'/../wxpage/core/weer.func.php';

$curw = date("w");
echo in_array($curw, array(0,2,4)) && (date("H")>=21 && date("i")>30 || date("H")>21) ? 1 : 0;
