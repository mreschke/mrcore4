-- MySQL dump 10.13  Distrib 5.5.24, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: mrcore4
-- ------------------------------------------------------
-- Server version	5.5.24-8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_badge_item`
--

DROP TABLE IF EXISTS `tbl_badge_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_badge_item` (
  `badge_id` int(11) NOT NULL AUTO_INCREMENT,
  `badge` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `default_topic_id` int(11) DEFAULT NULL,
  `topic_count` int(11) NOT NULL,
  PRIMARY KEY (`badge_id`),
  KEY `badge` (`badge`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_badge_link`
--

DROP TABLE IF EXISTS `tbl_badge_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_badge_link` (
  `topic_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  PRIMARY KEY (`topic_id`,`badge_id`),
  KEY `badge_id1_fk_constraint` (`badge_id`),
  CONSTRAINT `badge_id1_fk_constraint` FOREIGN KEY (`badge_id`) REFERENCES `tbl_badge_item` (`badge_id`) ON DELETE CASCADE,
  CONSTRAINT `topic_id7_fk_constraint` FOREIGN KEY (`topic_id`) REFERENCES `tbl_topic` (`topic_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_log`
--

DROP TABLE IF EXISTS `tbl_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `url` varchar(255) NOT NULL,
  `agent` varchar(1000) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `detail` varchar(4000) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_date_index` (`date`),
  KEY `user_id_log_fk_constraint` (`user_id`),
  CONSTRAINT `user_id_log_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_perm_group_item`
--

DROP TABLE IF EXISTS `tbl_perm_group_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_perm_group_item` (
  `perm_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(50) NOT NULL,
  `description` varchar(1000) NOT NULL,
  PRIMARY KEY (`perm_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_perm_group_link`
--

DROP TABLE IF EXISTS `tbl_perm_group_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_perm_group_link` (
  `user_id` int(11) NOT NULL,
  `perm_group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`perm_group_id`),
  KEY `perm_group_id1_fk_constraint` (`perm_group_id`),
  CONSTRAINT `perm_group_id1_fk_constraint` FOREIGN KEY (`perm_group_id`) REFERENCES `tbl_perm_group_item` (`perm_group_id`) ON DELETE CASCADE,
  CONSTRAINT `user_id4_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_perm_item`
--

DROP TABLE IF EXISTS `tbl_perm_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_perm_item` (
  `perm_id` int(11) NOT NULL,
  `short` varchar(50) NOT NULL,
  `description` varchar(1000) NOT NULL,
  PRIMARY KEY (`perm_id`),
  KEY `perm_item_short_index` (`short`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_perm_link`
--

DROP TABLE IF EXISTS `tbl_perm_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_perm_link` (
  `topic_id` int(11) NOT NULL,
  `perm_group_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL,
  PRIMARY KEY (`topic_id`,`perm_group_id`,`perm_id`),
  KEY `perm_group_id2_fk_constraint` (`perm_group_id`),
  KEY `perm_id1_fk_constraint` (`perm_id`),
  CONSTRAINT `perm_group_id2_fk_constraint` FOREIGN KEY (`perm_group_id`) REFERENCES `tbl_perm_group_item` (`perm_group_id`) ON DELETE CASCADE,
  CONSTRAINT `perm_id1_fk_constraint` FOREIGN KEY (`perm_id`) REFERENCES `tbl_perm_item` (`perm_id`) ON DELETE CASCADE,
  CONSTRAINT `topic_id8_fk_constraint` FOREIGN KEY (`topic_id`) REFERENCES `tbl_topic` (`topic_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_post`
--

DROP TABLE IF EXISTS `tbl_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_uuid` char(32) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_on` datetime NOT NULL,
  `indexed_on` datetime NOT NULL,
  `is_comment` tinyint(1) NOT NULL,
  `has_exec` tinyint(1) NOT NULL DEFAULT '0',
  `has_html` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `uuid_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `body` longtext NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `updated_by2_fk_constraint` (`updated_by`),
  KEY `created_by2_fk_constraint` (`created_by`),
  KEY `topic_id5_fk_constraint` (`topic_id`),
  KEY `deleted_index` (`deleted`),
  KEY `is_comment_index` (`is_comment`),
  CONSTRAINT `created_by2_fk_constraint` FOREIGN KEY (`created_by`) REFERENCES `tbl_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `topic_id5_fk_constraint` FOREIGN KEY (`topic_id`) REFERENCES `tbl_topic` (`topic_id`) ON DELETE CASCADE,
  CONSTRAINT `updated_by2_fk_constraint` FOREIGN KEY (`updated_by`) REFERENCES `tbl_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_post_lock`
--

DROP TABLE IF EXISTS `tbl_post_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_post_lock` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `locked_on` datetime NOT NULL,
  PRIMARY KEY (`post_id`,`user_id`),
  CONSTRAINT `post_id5_fk_constraint` FOREIGN KEY (`post_id`) REFERENCES `tbl_post` (`post_id`) ON DELETE CASCADE,
  CONSTRAINT `user_id5_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_post_index`
--

DROP TABLE IF EXISTS `tbl_post_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_post_index` (
  `post_id` int(11) NOT NULL,
  `word` varchar(100) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`word`),
  CONSTRAINT `post_id_index_fk_constraint` FOREIGN KEY (`post_id`) REFERENCES `tbl_post` (`post_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_read`
--

DROP TABLE IF EXISTS `tbl_read`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_read` (
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`topic_id`),
  KEY `topic_id_fk_constraint` (`topic_id`),
  CONSTRAINT `topic_id_fk_constraint` FOREIGN KEY (`topic_id`) REFERENCES `tbl_topic` (`topic_id`) ON DELETE CASCADE,
  CONSTRAINT `user_id_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_tag_item`
--

DROP TABLE IF EXISTS `tbl_tag_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tag_item` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  `default_topic_id` int(11) DEFAULT NULL,
  `topic_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_tag_link`
--

DROP TABLE IF EXISTS `tbl_tag_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_tag_link` (
  `topic_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`topic_id`,`tag_id`),
  KEY `tag_id1_fk_constraint` (`tag_id`),
  CONSTRAINT `tag_id1_fk_constraint` FOREIGN KEY (`tag_id`) REFERENCES `tbl_tag_item` (`tag_id`) ON DELETE CASCADE,
  CONSTRAINT `topic_id6_fk_constraint` FOREIGN KEY (`topic_id`) REFERENCES `tbl_topic` (`topic_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_topic`
--

DROP TABLE IF EXISTS `tbl_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_topic` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_on` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `teaser` varchar(4000) NOT NULL,
  PRIMARY KEY (`topic_id`),
  KEY `created_by_fk_constraint` (`created_by`),
  KEY `updated_by_fk_constraint` (`updated_by`),
  KEY `deleted_index` (`deleted`),
  CONSTRAINT `created_by_fk_constraint` FOREIGN KEY (`created_by`) REFERENCES `tbl_user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `updated_by_fk_constraint` FOREIGN KEY (`updated_by`) REFERENCES `tbl_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_topic_stat`
--

DROP TABLE IF EXISTS `tbl_topic_stat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_topic_stat` (
  `topic_id` int(11) NOT NULL,
  `view_count` int(11) NOT NULL,
  `comment_count` int(11) NOT NULL,
  PRIMARY KEY (`topic_id`),
  CONSTRAINT `topic_id4_fk_constraint` FOREIGN KEY (`topic_id`) REFERENCES `tbl_topic` (`topic_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `signature` varchar(4000) DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `password` varchar(50) NOT NULL,
  `avatar` varchar(50) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `last_login_on` datetime NOT NULL,
  `disabled` tinyint(1) NOT NULL,
  `perm_create` tinyint(1) NOT NULL,
  `perm_admin` tinyint(1) NOT NULL,
  `perm_exec` tinyint(1) NOT NULL,
  `perm_html` tinyint(1) NOT NULL,
  `global_topic_id` int(11) NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `created_by3_fk_constraint` (`created_by`),
  CONSTRAINT `created_by3_fk_constraint` FOREIGN KEY (`created_by`) REFERENCES `tbl_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_user_stat`
--

DROP TABLE IF EXISTS `tbl_user_stat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user_stat` (
  `user_id` int(11) NOT NULL,
  `topic_count` int(11) NOT NULL,
  `comment_count` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_id3_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_watch`
--

DROP TABLE IF EXISTS `tbl_watch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_watch` (
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `subscribe` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`,`topic_id`),
  KEY `topic_id2_fk_constraint` (`topic_id`),
  CONSTRAINT `topic_id2_fk_constraint` FOREIGN KEY (`topic_id`) REFERENCES `tbl_topic` (`topic_id`) ON DELETE CASCADE,
  CONSTRAINT `user_id2_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-10-19 18:04:29
