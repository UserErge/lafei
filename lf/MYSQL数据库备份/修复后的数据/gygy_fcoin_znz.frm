TYPE=VIEW
query=select `b`.`id` AS `betId`,`b`.`type` AS `type`,`b`.`playedId` AS `playedId`,`b`.`qz_uid` AS `qz_uid`,`b`.`qz_username` AS `qz_username`,`b`.`actionNo` AS `actionNo`,`b`.`qz_time` AS `qz_Time`,`l`.`info` AS `info`,`l`.`liqType` AS `liqType`,`l`.`fcoin` AS `fcoin` from (`lafei`.`gygy_coin_log` `l` join `lafei`.`gygy_bets` `b`) where ((`b`.`id` = `l`.`extfield0`) and (`b`.`isDelete` = 0) and (`b`.`lotteryNo` = \'\') and (`l`.`liqType` = 100))
md5=2bf47a4c6456a56c9f5a431b4731ba1f
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=1
with_check_option=0
timestamp=2017-01-23 19:58:18
create-version=1
source=select `b`.`id` AS `betId`,`b`.`type` AS `type`,`b`.`playedId` AS `playedId`,`b`.`qz_uid` AS `qz_uid`,`b`.`qz_username` AS `qz_username`,`b`.`actionNo` AS `actionNo`,`b`.`qz_time` AS `qz_Time`,`l`.`info` AS `info`,`l`.`liqType` AS `liqType`,`l`.`fcoin` AS `fcoin` from (`gygy_coin_log` `l` join `gygy_bets` `b`) where ((`b`.`id` = `l`.`extfield0`) and (`b`.`isDelete` = 0) and (`b`.`lotteryNo` = \'\') and (`l`.`liqType` = 100))
client_cs_name=utf8
connection_cl_name=utf8_general_ci
view_body_utf8=select `b`.`id` AS `betId`,`b`.`type` AS `type`,`b`.`playedId` AS `playedId`,`b`.`qz_uid` AS `qz_uid`,`b`.`qz_username` AS `qz_username`,`b`.`actionNo` AS `actionNo`,`b`.`qz_time` AS `qz_Time`,`l`.`info` AS `info`,`l`.`liqType` AS `liqType`,`l`.`fcoin` AS `fcoin` from (`lafei`.`gygy_coin_log` `l` join `lafei`.`gygy_bets` `b`) where ((`b`.`id` = `l`.`extfield0`) and (`b`.`isDelete` = 0) and (`b`.`lotteryNo` = \'\') and (`l`.`liqType` = 100))
