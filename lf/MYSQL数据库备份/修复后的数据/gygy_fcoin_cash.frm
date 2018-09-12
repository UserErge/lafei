TYPE=VIEW
query=select `r`.`id` AS `rid`,`l`.`uid` AS `uid`,`r`.`actionTime` AS `actionTime`,`l`.`info` AS `info`,`l`.`liqType` AS `liqType`,`l`.`fcoin` AS `fcoin` from (`lafei`.`gygy_member_cash` `r` join `lafei`.`gygy_coin_log` `l`) where ((`l`.`extfield0` = `r`.`id`) and (`r`.`state` = 1) and (`r`.`isDelete` = 0) and (`l`.`liqType` = 106))
md5=401aa4294300e485d8b1ef0cdda12571
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=1
with_check_option=0
timestamp=2017-01-23 19:58:18
create-version=1
source=select `r`.`id` AS `rid`,`l`.`uid` AS `uid`,`r`.`actionTime` AS `actionTime`,`l`.`info` AS `info`,`l`.`liqType` AS `liqType`,`l`.`fcoin` AS `fcoin` from (`gygy_member_cash` `r` join `gygy_coin_log` `l`) where ((`l`.`extfield0` = `r`.`id`) and (`r`.`state` = 1) and (`r`.`isDelete` = 0) and (`l`.`liqType` = 106))
client_cs_name=utf8
connection_cl_name=utf8_general_ci
view_body_utf8=select `r`.`id` AS `rid`,`l`.`uid` AS `uid`,`r`.`actionTime` AS `actionTime`,`l`.`info` AS `info`,`l`.`liqType` AS `liqType`,`l`.`fcoin` AS `fcoin` from (`lafei`.`gygy_member_cash` `r` join `lafei`.`gygy_coin_log` `l`) where ((`l`.`extfield0` = `r`.`id`) and (`r`.`state` = 1) and (`r`.`isDelete` = 0) and (`l`.`liqType` = 106))
