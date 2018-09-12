<?php
?>
<html>
<head>
<title>php</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body> 
<?php 
/* *
 * 功能：支付回调文件
 * 版本：1.0
 * 日期：2015-03-26
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码。
 */
require_once "Mobaopay.Config.php";
require_once "lib/MobaoPay.class.php";
// 请求数据赋值
$data = "";
$data['apiName'] = $_REQUEST["apiName"];
// 通知时间
$data['notifyTime'] = $_REQUEST["notifyTime"];
// 支付金额(单位元，显示用)
$data['tradeAmt'] = $_REQUEST["tradeAmt"];
// 商户号
$data['merchNo'] = $_REQUEST["merchNo"];
// 商户参数，支付平台返回商户上传的参数，可以为空
$data['merchParam'] = $_REQUEST["merchParam"];
// 商户订单号
$data['orderNo'] = $_REQUEST["orderNo"];
// 商户订单日期
$data['tradeDate'] = $_REQUEST["tradeDate"];
// Mo宝支付订单号
$data['accNo'] = $_REQUEST["accNo"];
// Mo宝支付账务日期
$data['accDate'] = $_REQUEST["accDate"];
// 订单状态，0-未支付，1-支付成功，2-失败，4-部分退款，5-退款，9-退款处理中
$data['orderStatus'] = $_REQUEST["orderStatus"];
// 签名数据
$data['signMsg'] = $_REQUEST["signMsg"];
//print_r( $data);
// 初始化
$cMbPay = new MbPay($mbp_key, $mobaopay_gateway);
// 准备准备验签数据
$str_to_sign = $cMbPay->prepareSign($data);
// 验证签名
$resultVerify = $cMbPay->verify($str_to_sign, $data['signMsg']);
//var_dump($data);
if ($resultVerify) {
    if ($data['orderStatus'] == '1') {
        //b
        echo $data['orderStatus'];
        $db_host = "localhost";
        $db_dbname = "xx_ssc";
        $db_user = "fucai";
        $db_pwd = "chuanshuo2013#@!^%$";
        $conn = mysql_connect($db_host, $db_user, $db_pwd);
        if (!$conn) {
            die("Can not connect:" . mysql_error());
        }
        $dbconn = mysql_select_db($db_dbname);
        if (!$dbconn) {
            die("Can not select this database:" . mysql_error($conn));
        }
        @session_start();
        //启动session会话
        mysql_query("SET NAMES 'utf8'");
        //设置字符集和页面代码统一
        $r6_Order = $data['orderNo'];
        $r3_Amt = $data['tradeAmt'];
        $ssql = "select * from gygy_member_recharge where state=0 and rechargeId={$r6_Order}";
        $sresult = mysql_query($ssql);
        if ($num = mysql_num_rows($sresult)) {
            //echo"查找到订单<br>";
            $rss = mysql_fetch_array($sresult);
            //订单数组赋值
            $sql_u = "select * from gygy_members where uid={$rss['uid']}";
            $uresult = mysql_query($sql_u);
            if ($unum = mysql_num_rows($uresult)) {
                //echo"查找到用户记录<br>";
                $rsu = mysql_fetch_array($uresult);
                //y用户赋值
                $rechargeTime = time();
                $afmoney = $rsu["coin"] + $r3_Amt;
                $sql_o = "update gygy_member_recharge set state=2,rechargeAmount={$r3_Amt},coin={$rsu['coin']},rechargeTime={$rechargeTime} where rechargeId={$r6_Order}";
                // echo $sql_o;
                mysql_query($sql_o);
                $sql_2 = "insert into gygy_coin_log (uid,type,playedID,coin,userCoin,fcoin,liqType,actionUID,actionTime,ActionIP,info,extfield0,extfield1,extfield2)values('" . $rss["uid"] . "','0','0','" . $r3_Amt . "','" . $afmoney . "','0','1','0','" . $rechargeTime . "','0','墨宝充值','" . $r6_Order . "','" . $r6_Order . "','')";
                mysql_query($sql_2);
                //  echo "<br>".$sql_2;
                $sql_u = "update gygy_members set coin=coin+{$r3_Amt} where uid={$rss['uid']}";
                //  echo "<br>".$sql_u;
                mysql_query($sql_u);
            }
        }
        mysql_close();
        //e
    }
    if ('1' == $_REQUEST["notifyType"]) {
        $url = "notify.php";
        Header("Location: {$url}");
        return true;
    }
    // 签名验证通过
    echo "支付成功" . '<br>';
    echo "商户订单号 " . $data['orderNo'] . '<br>';
    echo "商户订单日期 " . $data['tradeDate'] . '<br>';
    echo "商户参数 " . $data['merchParam'] . '<br>';
    echo "Mo宝支付订单号 " . $data['accNo'] . '<br>';
    echo "Mo宝支付账务日期 " . $data['accDate'] . '<br>';
    echo "支付金额 " . $data['tradeAmt'] . "元" . '<br>';
    echo "订单状态 ";
    /*商户需要在此处判定通知中的订单状态做后续处理*/
    /*由于页面跳转同步通知和异步通知均发到当前页面，所以此处还需要判定商户自己系统中的订单状态，避免重复处理。*/
    return true;
} else {
    // 签名验证失败
    echo "验证签名失败";
    return false;
}
$url = "/index.php";
Header("Location: {$url}");
?>
</body>
</html><?php 