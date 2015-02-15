/*
tbl_user fixtures
*/
LOCK TABLES `tbl_user` WRITE;
INSERT INTO `tbl_user` VALUES (1,'anonymous@mreschke.com','anonymous','anonymous','anonymous','anonymous',NULL,'Anonymous (Public) Account','techie','avatar_user1.png',1,now(),now(),'1900-01-01 12:00:00',1,0,0,0,0,0);
INSERT INTO `tbl_user` VALUES (2,'admin@admin.com','Admin','Admin','Site Administrator','admin','~/Admin','Administrator','admin','avatar_user1.png',2,now(),now(),'1900-01-01 12:00:00',0,1,1,1,1,7);
UNLOCK TABLES;


/*
tbl_user_stat fixtures
*/
LOCK TABLES `tbl_user_stat` WRITE;
INSERT INTO `tbl_user_stat` VALUES (2,2,0);
UNLOCK TABLES;


/*
tbl_topic fixtures
*/
LOCK TABLES `tbl_topic` WRITE;
INSERT INTO `tbl_topic` VALUES
    (1,2,now(),2,now(),0,0,'Home','Welcome to mrcore4'),
	(2,2,now(),2,now(),0,1,'Global',''),
	(3,2,now(),2,now(),0,0,'Help',''),
	(4,2,now(),2,now(),0,1,'User Info',''),
	(5,2,now(),2,now(),0,1,'Searchbox',''),
	(6,2,now(),2,now(),0,1,'Template',''),
	(7,2,now(),2,now(),0,1,'Admin Global','');
UNLOCK TABLES;


/*
tbl_post fixtures
*/
LOCK TABLES `tbl_post` WRITE;
INSERT INTO `tbl_post` VALUES
    (1,'8f09896e673e58c9b308727617298b1a',1,2,now(),2,now(),'1900-01-01 12:00:00',0,0,0,0,0,0,'Home','Welcome to mrcore4'),
    (2,'a0542ed0e8e97392798dfb28af368d88',2,2,now(),2,now(),'1900-01-01 12:00:00',0,0,0,0,1,0,'Global',''),
    (3,'912ec803b2ce49e4a541068d495ab570',3,2,now(),2,now(),'1900-01-01 12:00:00',0,0,0,0,0,0,'Help','For mrcore help see http://mrcore.mreschke.com'),
    (4,'e5dcee668901760a820c893f879890b0',4,2,now(),2,now(),'1900-01-01 12:00:00',0,0,0,0,1,0,'User Info','?'),
    (5,'ead3bfef0880dd74fc070cd29f362266',5,2,now(),2,now(),'1900-01-01 12:00:00',0,0,0,0,1,0,'Searchbox','?'),
    (6,'901e3298056facee2bfa481a6649455f',6,2,now(),2,now(),'1900-01-01 12:00:00',0,0,0,0,1,0,'Template','<info>
[[toc]]
</info>

+ Summary
'),
    (7,'018ff4a8a66164b69c9ac5fd236ce66f',7,2,now(),2,now(),'1900-01-01 12:00:00',0,0,0,0,1,0,'Admin Global','');
UNLOCK TABLES;


/*
tbl_badge_item fixtures
*/
LOCK TABLES `tbl_badge_item` WRITE;
INSERT INTO `tbl_badge_item` VALUES (1,'SITE','badge1.png',null,0);
UNLOCK TABLES;


/*
tbl_badge_link fixtures
*/
LOCK TABLES `tbl_badge_link` WRITE;
INSERT INTO `tbl_badge_link` VALUES (1,1), (2,1), (3,1), (4,1), (5,1), (6,1), (7,1);
UNLOCK TABLES;


/*
tbl_tag_item fixtures
*/
LOCK TABLES `tbl_tag_item` WRITE;
INSERT INTO `tbl_tag_item` VALUES (1,'site',null,null,7);
INSERT INTO `tbl_tag_item` VALUES (2,'template',null,null,0);
INSERT INTO `tbl_tag_item` VALUES (3,'fixme','tag3.png',null,0);
INSERT INTO `tbl_tag_item` VALUES (4,'obsolete','tag4.png',null,0);
INSERT INTO `tbl_tag_item` VALUES (5,'production','tag5.png',null,0);
INSERT INTO `tbl_tag_item` VALUES (6,'book','tag5.png',null,0);
UNLOCK TABLES;


/*
tbl_tag_link fixtures
*/
LOCK TABLES `tbl_tag_link` WRITE;
INSERT INTO `tbl_tag_link` VALUES (1,1), (2,1), (3,1), (4,1), (5,1), (6,1), (7,1);
UNLOCK TABLES;


/*
tbl_perm_group_item fixtures
*/
LOCK TABLES `tbl_perm_group_item` WRITE;
INSERT INTO `tbl_perm_group_item` VALUES (1,'Public','Public users. All users should be in this group');
INSERT INTO `tbl_perm_group_item` VALUES (2,'Users','All registered users should be in this group');
UNLOCK TABLES;


/*
tbl_perm_group_link fixtures
*/
LOCK TABLES `tbl_perm_group_link` WRITE;
INSERT INTO `tbl_perm_group_link` VALUES (1,1); -- anon to public
INSERT INTO `tbl_perm_group_link` VALUES (2,1); -- mreschke -> public
INSERT INTO `tbl_perm_group_link` VALUES (2,2); -- mreschke -> users
UNLOCK TABLES;


/*
tbl_perm_item fixtures
*/
LOCK TABLES `tbl_perm_item` WRITE;
INSERT INTO `tbl_perm_item` VALUES (1,'READ','Read topic');
INSERT INTO `tbl_perm_item` VALUES (2,'WRITE','Write topic (edit, delete)');
INSERT INTO `tbl_perm_item` VALUES (3,'COMMENT','Comment on topic');
-- INSERT INTO `tbl_perm_item` VALUES (4,'FILES','Manage files (upload, delete)');
UNLOCK TABLES;


/*
tbl_perm_link fixtures
*/
LOCK TABLES `tbl_perm_link` WRITE;
INSERT INTO `tbl_perm_link` VALUES (1,1,1); -- public read (so all can read)
INSERT INTO `tbl_perm_link` VALUES (3,1,1); -- public read (so all can read)
UNLOCK TABLES;

