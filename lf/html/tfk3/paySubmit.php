<?php

header("Content-type: text/html; charset=utf-8");

$p0_Cmd ="Buy";//业务类型
 
$p1_MerId ="10652";//商户编号
 
$p2_Order = $_REQUEST["p2_Order"];//商户订单号
 
$p3_Amt = $_REQUEST["p3_Amt"];//支付金额
 
$p4_Cur = "CNY";//交易币种
 
$p5_Pid = "";//商品名称
 
$p6_Pcat ="";//商品种类
 
$p7_Pdesc = "";//商品描述
 
$p8_Url = "http://".$_SERVER["HTTP_HOST"]."/tfk3/payReturn.php";//通知商户地址
 
$p9_SAF = "0";//送货地址(保留字段)
 
$pa_MP = $_REQUEST["pa_MP"];//商户扩展信息
 
$pd_FrpId ="gsyh";//支付渠道
 
$pr_NeedResponse ="1";//应答机制
 
$Sjt_Paytype ="b";//支付类型
 
$Sjt_UserName ="b";//支付用户
 
$key = "1e34a818c6dbf236ee724767acd1939e";   //密钥
 
$hmacstr = $p0_Cmd.$p1_MerId.$p2_Order.$p3_Amt.$p4_Cur.$p5_Pid.$p6_Pcat.$p7_Pdesc.$p8_Url.$p9_SAF.$pa_MP.$pd_FrpId.$pr_NeedResponse.$key;
 
$hmac = MD5($hmacstr);


?>

<form name="Form1" id="Form1" method="post" action="http://www.yyjxw.net/Payapi_Index_Pay.html">
<input type="hidden" name="p0_Cmd" value="Buy">
<input type="hidden" name="p1_MerId" value="<?php echo $p1_MerId; ?>">
<input type="hidden" name="p2_Order" value="<?php echo $p2_Order; ?>">
<input type="hidden" name="p3_Amt" value="<?php echo $p3_Amt; ?>">
<input type="hidden" name="p4_Cur" value="CNY">
<input type="hidden" name="p5_Pid" value="<?php echo $p5_Pid; ?>">
<input type="hidden" name="p6_Pcat" value="<?php echo $p6_Pcat; ?>">
<input type="hidden" name="p7_Pdesc" value="<?php echo $p7_Pdesc; ?>">
<input type="hidden" name="p8_Url" value="<?php echo  $p8_Url?>">
<input type="hidden" name="p9_SAF" value="0">
<input type="hidden" name="pa_MP" value="<?php echo $pa_MP; ?>">
<input type="hidden" name="pd_FrpId" value="<?php echo $pd_FrpId; ?>">
<input type="hidden" name="pr_NeedResponse" value="1">
<input type="hidden" name="Sjt_Paytype" value="b">
<input type="hidden" name="Sjt_UserName" value="<?php echo $Sjt_UserName; ?>" >
<input type="hidden" name="hmac" value="<?php echo $hmac; ?>">
<input type="submit" value="提 交">
</form>
<script type="text/javascript">
document.forms["Form1"].submit();
</script>