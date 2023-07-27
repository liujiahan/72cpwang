<?php

$code = isset($_GET['code']) ? $_GET['code'] : '08 18 28';

?>
<html>

<head>
    <title>下订单购买</title>
</head>

<body>
    <!-- <a href="bocpay://www.boc.cn/mobile?param= PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiID8+PHJlc3BvbnNlPjxoZWFkPg0KPHJlc3BvbnNlQ29kZT5PSzwvcmVzcG9uc2VDb2RlPg0KPHJlc3BvbnNlSW5mbz7miJDlip88L3Jlc3BvbnNlSW5mbz4NCjwvaGVhZD4NCjxib2R5Pg0KPGRpc3BhdGNoZXJUeXBlPkQwMDItNDwvZGlzcGF0Y2hlclR5cGU+DQo8ZGlzcGF0Y2hlclNlcT4xMDY5MDI1OTI1IyMxMzg2NTk3OTwvZGlzcGF0Y2hlclNlcT4NCjxzZWN1cml0eURhdGE+bTVDb3NhMThLZ25nckhKTERWd0FxOW5FOFJ4aFZ4c25FMFcwQ0t3MVcvSWRsbmozS0FZQm9sRlV6RTQ3aFlhcWp2UkVKOFlvT1hSbzhmdzBWcWZSVFJFeE5NL1l0Y3RoUkhodHlsZ1FMZjVLbzNOQUJMeGd2Vld1dkprSko0ZkdJN085djFwbHM3bFJ2bFlZVWdlYWp3RFgyZGhOS0kzSDlOM2xoZEZGeUtvPTwvc2VjdXJpdHlEYXRhPg0KPC9ib2R5Pg0KPC9yZXNwb25zZT4=">唤起手机银行</a> -->
    <form method="post" name="paysubmit" action="//ssq.17500.cn/tools/zdyfb.html" id="paysubmit">
        <i>输入任意红球：</i><input type="text" name="qqhm" value="<?php echo $code ?>" size="20">
        <i>输入任意蓝球：</i><input type="text" name="hqhm" value="" size="20">
        <input type="submit" style="display:none;">
    </form>
    <script type="text/javascript">
        document.forms["paysubmit"].submit();
    </script>
</body>

</html>
<script type="text/javascript">
    document.forms["paysubmit"].submit();
</script>