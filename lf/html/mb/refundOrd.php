﻿<?php
	/* *
 * 退款调试入口页面
 * 版本：1.0
 * 日期：2015-03-26
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码。
 */
	$time		= time();
	$tradeDate	= date("Ymd",$time);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>久付支付商户接口范例-退款</title>
	<!--
	<link rel="stylesheet" type="text/css" href="styles.css">
	<link href="Styles/mobaopay.css" type="text/css" rel="stylesheet" />
	-->
</head>
<body>
    <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" style="border: solid 1px #107929">
        <tr>
            <td>
                <table width="100%" border="0" align="center" cellpadding="5" cellspacing="1">
                    <tr>
                        <td height="30" align="left">
                            <a href="http://www.9payonline.com/">
                                <img src="http://www.9payonline.com/Content/WebSkin/images/logo.png"
                                    width="141" height="47" border="0" alt="久付支付LOGO" /></a>
                        </td>
                    </tr>
                    <tr>
                        <td height="30" colspan="2" bgcolor="#6BBE18">
                            <span style="color: #FFFFFF"><a href="index.php">感谢您使用久付支付平台</a></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" bgcolor="#CEE7BD">
                            久付支付订单退款请求接口演示：
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form method="post" action="refund.php">
                            <table>
                                <tr>
									<td align="left" width="30%">
										&nbsp;&nbsp;订单号
									</td>
									<td align="left">
										&nbsp;&nbsp;<input size="50" type="text" name="orderNo" id="orderNo" value="" />
									</td>
								</tr>
								<tr>
									<td align="left" width="30%">
										&nbsp;&nbsp;交易日期
									</td>
									<td align="left">
										&nbsp;&nbsp;<input size="50" type="text" name="tradeDate" id="tradeDate" value='<?php echo $tradeDate; ?>' />
									</td>
								</tr>
								<tr>
									<td align="left" width="30%">
										&nbsp;&nbsp;退款金额
									</td>
									<td align="left">
										&nbsp;&nbsp;<input size="50" type="text" name="amt" id="amt" value="0.01" />
									</td>
								</tr>
								<tr>
									<td align="left" width="30%">
										&nbsp;&nbsp;交易摘要
									</td>
									<td align="left">
										&nbsp;&nbsp;<input size="50" type="text" name="tradeSummary" id="tradeSummary" value="退款测试" />
									</td>
								</tr>
                                <tr>
                                    <td align="left">
                                        &nbsp;
                                    </td>
                                    <td align="left">
                                        &nbsp;&nbsp;<input type="submit" value="马上提交" />
                                    </td>
                                </tr>
                            </table>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td height="5" bgcolor="#6BBE18" colspan="2">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
