<?php
 
/**
 * Class Lottery
 */
class Lottery
{
    //接口提交地址
    private static $submitUrl = 'http://apis.juhe.cn/lottery';
 
    //申请的彩票接口AppKey
    private static $appkey = '7b626104938bc64aa41a14ede96a6a20';
 
    /**
     * 获取支持彩票列表
     */
    public static function getLotteryTypes()
    {
        $urlPath = '/types';
        $params = [
            'key' => self::$appkey
        ];
        $paramsString = http_build_query($params);
 
        $requestUrl = self::$submitUrl.$urlPath;
        $content = self::juheCurl($requestUrl, $paramsString);
        $result = json_decode($content, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
 
    /**
     * 获取彩票某一期开奖结果,默认最新一期
     * @param $lotteryId 彩票ID
     * @param string $lotteryNo 彩票期数，默认最新一期
     * @return bool|mixed
     */
    public static function getLotteryRes($lotteryId, $lotteryNo = "")
    {
        $urlPath = '/query';
 
        $params = [
            'key' => self::$appkey,
            'lottery_id' => $lotteryId,
            'lottery_no' => $lotteryNo
        ];
        $paramsString = http_build_query($params);
 
        $requestUrl = self::$submitUrl.$urlPath;
        $content = self::juheCurl($requestUrl, $paramsString);
        $result = json_decode($content, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
 
    /**
     * 获取历史开奖结果
     * @param $lotteryId 彩票ID
     * @param int $pageSize 每页返回条数
     * @param int $page 当前页数
     */
    public static function getLotteryHistroyRes($lotteryId, $pageSize = 10, $page = 1)
    {
        $urlPath = '/history';
 
        $params = [
            'key' => self::$appkey,
            'lottery_id' => $lotteryId,
            'page_size' => $pageSize,
            'page' => $page,
        ];
        $paramsString = http_build_query($params);
 
        $requestUrl = self::$submitUrl.$urlPath;
        $content = self::juheCurl($requestUrl, $paramsString);
        $result = json_decode($content, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
 
    /**
     * 中奖计算器/判断号码是否中奖
     * @param $lotteryId 彩票ID
     * @param $lotteryRes 投注号码
     * @param string $lotteryNo 投注期号，默认最新一期
     * @return bool|mixed
     */
    public static function getBonus($lotteryId, $lotteryRes, $lotteryNo='')
    {
        $urlPath = '/bonus';
 
        $params = [
            'key' => self::$appkey,
            'lottery_id' => $lotteryId,
            'lottery_res' => $lotteryRes,
            'lottery_no' => $lotteryNo,
        ];
        $paramsString = http_build_query($params);
 
        $requestUrl = self::$submitUrl.$urlPath;
        $content = self::juheCurl($requestUrl, $paramsString);
        $result = json_decode($content, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
 
    /**
     * 发起接口网络请求
     * @param $url
     * @param bool $params
     * @param int $ispost
     * @return bool|mixed
     */
    public static function juheCurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url.'?'.$params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}
