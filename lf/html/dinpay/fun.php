<?php
	$con = mysql_connect("localhost","qt","yzllsyt");
	if (!$con)
	  {
	  die('Could not connect: ' . mysql_error());
	  }
	
	mysql_select_db("div", $con);
	
	//Change("385593","90");
	
	function Change($rid,$rmoney)
	{
		$addtime = time();
	 	$result2 = mysql_query("select * from ssc_member_recharge where rechargeId='{$rid}'");
		$row = mysql_fetch_array($result2);
		if($row['state']=='0')
		{      
	        mysql_query("update ssc_members set coin=coin+{$rmoney} where  uid={$row['uid']}");
	        mysql_query("update ssc_member_recharge set state='1',rechargeAmount={$rmoney},actionTime={$addtime} where  rechargeId='".$rid."'"); 			

        }
		
	}
	
	
	
	
?>