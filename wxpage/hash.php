<?php

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

$redis->set("liang","苟利国家生死以");
// 存储哈希表,数组会成为哈希表的field,value
$redis->hmset("stars:jay",array("name"=>'周杰伦','age'=>33,'lover'=>"蓝球"));
// 获取哈希表的值
echo $data=$redis->hgetall("stars:jay");