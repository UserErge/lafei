<?php
header("Content-type: text/html; charset=utf-8");

$Sjt_MerchantID = $_REQUEST["Sjt_MerchantID"];//商户编号
$Sjt_Username = $_REQUEST["Sjt_Username"];//支付用户名
$Sjt_TransID = $_REQUEST["Sjt_TransID"];//订单号
$Sjt_Return = $_REQUEST["Sjt_Return"];//订单状态
$Sjt_Error = $_REQUEST["Sjt_Error"];//错误代码
$Sjt_factMoney = $_REQUEST["Sjt_factMoney"];//交易金额
$Sjt_SuccTime = $_REQUEST["Sjt_SuccTime"];//交易时间
$Sjt_BType = $_REQUEST["Sjt_BType"];//交易结果返回类型，即通知类型
$Sjt_Sign = $_REQUEST["Sjt_Sign"];//验证签名
$key = "1e34a818c6dbf236ee724767acd1939e";

$Sign = md5($Sjt_MerchantID.$Sjt_Username.$Sjt_TransID.$Sjt_Return.$Sjt_Error.$Sjt_factMoney.$Sjt_SuccTime.$Sjt_BType.$key);


$logName="qt.txt";
$james=fopen($logName,"a+");
fwrite($james,"\r\n".date("Y-m-d H:i:s")."|ht|Sjt_Return=".$Sjt_Return."|Sjt_TransID[".$Sjt_TransID."]|Sjt_factMoney[".$Sjt_factMoney."]|Sjt_Sign[".$Sjt_Sign.'--'.$Sign."]");
fclose($james);

if($Sjt_Sign == $Sign){
    if($Sjt_BType == 1){//浏览器重定向
        $db_host="localhost";
        $db_dbname="xx_ssc";
        $db_user="root";
        $db_pwd="root";//请核实数据库登录信息是否正确
        $conn=mysql_connect($db_host,$db_user,$db_pwd);
        if(!$conn){
            die("Can not connect:".mysql_error());
        }
        $dbconn=mysql_select_db($db_dbname);
        if(!$dbconn){
            die("Can not select this database:".mysql_error($conn));
        }
        @session_start();//启动session会话
        mysql_query("SET NAMES 'utf8'");//设置字符集和页面代码统一
        $r6_Order=$Sjt_TransID;
        $r3_Amt=$Sjt_factMoney;
        $ssql="select * from gygy_member_recharge where state=0 and rechargeId=$r6_Order";
        $sresult=mysql_query($ssql);
        if($num=mysql_num_rows($sresult)){
            //echo"查找到订单<br>";
            $rss=mysql_fetch_array($sresult);//订单数组赋值
            $sql_u="select * from gygy_members where uid=$rss[uid]";
            $uresult=mysql_query($sql_u);
            if($unum=mysql_num_rows($uresult)){
                //echo"查找到用户记录<br>";
                $rsu=mysql_fetch_array($uresult);//y用户赋值
                $rechargeTime=time();
                $afmoney=$rsu["coin"]+$r3_Amt;
                $sql_o="update gygy_member_recharge set state=9,rechargeAmount=$r3_Amt,coin=$rsu[coin],rechargeTime=$rechargeTime,info='在线支付' where rechargeId=$r6_Order";
                // echo $sql_o;
                mysql_query($sql_o);
                 
                $sql_2="insert into gygy_coin_log (uid,username,type,playedID,coin,userCoin,fcoin,typea,liqType,actionUID,actionTime,ActionIP,info,extfield0,extfield1,extfield2)values('".$rss["uid"]."','".$rsu["username"]."','0','0','".$r3_Amt."','".$afmoney."','0','1','42','0','".$rechargeTime."','0','在线支付','".$rss["id"]."','".$r6_Order."','')";
                 
                mysql_query($sql_2);
                //  echo "<br>".$sql_2;
                $sql_u="update gygy_members set coin=coin+$r3_Amt  where uid=$rss[uid]";
                //echo "<br>".$sql_u;
                mysql_query($sql_u);
                 
            }
        
        }	
        mysql_close();
        
        
        echo "充值成功！<br>";
        echo "订单号：".$Sjt_TransID."<br>";
        echo "充值时间：".$Sjt_SuccTime;
        echo "<script>alert('支付成功');window.close();</script>";
        
    }else{
        if($Sjt_BType == 2){//点对点通讯
            echo "OK";
            
            $db_host="localhost";
            $db_dbname="xx_ssc";
            $db_user="root";
            $db_pwd="root";//请核实数据库登录信息是否正确
            $conn=mysql_connect($db_host,$db_user,$db_pwd);
            if(!$conn){
                die("Can not connect:".mysql_error());
            }
            $dbconn=mysql_select_db($db_dbname);
            if(!$dbconn){
                die("Can not select this database:".mysql_error($conn));
            }
            @session_start();//启动session会话
            mysql_query("SET NAMES 'utf8'");//设置字符集和页面代码统一
            $r6_Order=$Sjt_TransID;
            $r3_Amt=$Sjt_factMoney;
            $ssql="select * from gygy_member_recharge where state=0 and rechargeId=$r6_Order";
            $sresult=mysql_query($ssql);
            if($num=mysql_num_rows($sresult)){
                //echo"查找到订单<br>";
                $rss=mysql_fetch_array($sresult);//订单数组赋值
                $sql_u="select * from gygy_members where uid=$rss[uid]";
                $uresult=mysql_query($sql_u);
                if($unum=mysql_num_rows($uresult)){
                    //echo"查找到用户记录<br>";
                    $rsu=mysql_fetch_array($uresult);//y用户赋值
                    $rechargeTime=time();
                    $afmoney=$rsu["coin"]+$r3_Amt;
                    $sql_o="update gygy_member_recharge set state=9,rechargeAmount=$r3_Amt,coin=$rsu[coin],rechargeTime=$rechargeTime,info='在线支付' where rechargeId=$r6_Order";
                    // echo $sql_o;
                    mysql_query($sql_o);
                     
                    $sql_2="insert into gygy_coin_log (uid,username,type,playedID,coin,userCoin,fcoin,typea,liqType,actionUID,actionTime,ActionIP,info,extfield0,extfield1,extfield2)values('".$rss["uid"]."','".$rsu["username"]."','0','0','".$r3_Amt."','".$afmoney."','0','1','42','0','".$rechargeTime."','0','在线支付','".$rss["id"]."','".$r6_Order."','')";
                     
                    mysql_query($sql_2);
                    //  echo "<br>".$sql_2;
                    $sql_u="update gygy_members set coin=coin+$r3_Amt  where uid=$rss[uid]";
                    //echo "<br>".$sql_u;
                    mysql_query($sql_u);
                     
                }
            
            }
            mysql_close();
            
            
            
        }
    }

}else{

   echo '数据签名失败，请与管理员联系！';


}

