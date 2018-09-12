TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER `upo` AFTER UPDATE ON `my18_cz_temp` FOR EACH ROW BEGIN \nset @o_time=NEW.dksj;set @O_id=NEW.O_id;set @M_name=NEW.dkrm;set @U_id=NEW.userid;set @addmoney=NEW.dkje;set @Zhuangtai=NEW.state;set @p_type=NEW.ptype;set @dkind=NEW.dkind;set @xingwei=NEW.xingwei;set @huikuanren=NEW.huikuanren;set @H_fee=NEW.H_fee;\ninsert into my18_pay_temp (O_time , O_id, XingWei, M_name, U_id, addmoney, Zhuangtai, H_fee, P_type, dkind, huikuanren) values(@o_time,@O_id,@xingwei,@M_name,@U_id,@addmoney,@Zhuangtai,@H_fee,@p_type,@dkind,@huikuanren);End'
sql_modes=1342177280
definers='root@localhost'
client_cs_names='utf8'
connection_cl_names='utf8_general_ci'
db_cl_names='gbk_chinese_ci'
