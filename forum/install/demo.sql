CREATE DATABASE IF NOT EXISTS manykit DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `liaoliao_admin` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `user` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `purview` text COLLATE utf8_unicode_ci NOT NULL,
  `isAdmin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `liaoliao_big_block`
--

CREATE TABLE IF NOT EXISTS `liaoliao_big_block` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `bzone` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `liaoliao_mark1`
--

CREATE TABLE IF NOT EXISTS `liaoliao_mark1` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `tid` smallint(4) NOT NULL,
  `marker` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `pic` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `count` tinyint(4) NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `liaoliao_mark2`
--

CREATE TABLE IF NOT EXISTS `liaoliao_mark2` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `rid` smallint(4) NOT NULL,
  `tid` smallint(4) NOT NULL,
  `marker` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `pic` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `count` tinyint(4) NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Table structure for table `liaoliao_plugin`
--

CREATE TABLE IF NOT EXISTS `liaoliao_plugin` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Table structure for table `liaoliao_reply`
--

CREATE TABLE IF NOT EXISTS `liaoliao_reply` (
  `id2` int(4) NOT NULL AUTO_INCREMENT,
  `zuozhe1` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content1` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `rid` int(4) DEFAULT NULL,
  `fuzhuid` int(4) DEFAULT NULL,
  `num2` int(4) NOT NULL COMMENT '回复次数',
  `time2` datetime NOT NULL,
  `face1` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timezc2` datetime NOT NULL,
  `fatieshu2` smallint(4) NOT NULL,
  `parentid2` int(4) NOT NULL,
  `laud2` int(4) NOT NULL DEFAULT '0',
  `laud2_ips` mediumtext NOT NULL,
  `last_modify2` datetime NOT NULL,
  PRIMARY KEY (`id2`),
  KEY `rid` (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `liaoliao_reply`
--

INSERT INTO `liaoliao_reply` (`id2`, `zuozhe1`, `content1`, `rid`, `fuzhuid`, `num2`, `time2`, `face1`, `timezc2`, `fatieshu2`, `parentid2`) VALUES
(1, NULL, NULL, NULL, 1, 0, '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `liaoliao_small_block`
--

CREATE TABLE IF NOT EXISTS `liaoliao_small_block` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `szone` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `mark` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon_url` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `bid` int(4) NOT NULL,
  `sid` int(4) NOT NULL,
  `ssort` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `liaoliao_online`
--

CREATE TABLE IF NOT EXISTS `liaoliao_online` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `lasttime` int(4) NOT NULL,
  `user` varchar(7) NOT NULL,
  `zone` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `liaoliao_talk`
--

CREATE TABLE IF NOT EXISTS `liaoliao_talk` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `zuozhe` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `num1` int(4) NOT NULL DEFAULT '0',
  `timeup` datetime NOT NULL,
  `time1` datetime NOT NULL,
  `face` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timezc1` datetime NOT NULL,
  `fatieshu1` smallint(4) NOT NULL,
  `parentid` int(4) NOT NULL,
  `lock_status` tinyint(1) NOT NULL,
  `is_visible` tinyint(1) NOT NULL,
  `is_grap` tinyint(1) NOT NULL,
  `is_question` tinyint(1) NOT NULL,
  `question_bid` int(4) NOT NULL DEFAULT '0',
  `laud` int(4) NOT NULL DEFAULT '0',
  `laud_ips` mediumtext NOT NULL,
  `last_modify1` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `liaoliao_user`
--

CREATE TABLE IF NOT EXISTS `liaoliao_user` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `user` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `complete` int(4) NOT NULL,
  `face` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  `fatieshu` smallint(4) NOT NULL,
  `bid` int(4) NOT NULL DEFAULT '0',
  `codes` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ip_addr` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_group` tinyint(1) NOT NULL,
  `openid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cookieid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `lasttime` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- 表的结构 `liaoliao_user_group`
--

CREATE TABLE IF NOT EXISTS `liaoliao_user_group` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `purview` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- 表的结构 `liaoliao_vote`
--

CREATE TABLE IF NOT EXISTS `liaoliao_vote` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `rid` int(4) NOT NULL,
  `comb` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- 表的结构 `liaoliao_vote_ips`
--

CREATE TABLE IF NOT EXISTS `liaoliao_vote_ips` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `vid` int(4) NOT NULL,
  `ips` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- 表的结构 `liaoliao_count`
--

CREATE TABLE IF NOT EXISTS `liaoliao_count` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `user_count` text NOT NULL,
  `post_count` text NOT NULL,
  `week_order` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--
--
-- 表的结构 `liaoliao_message`
--

CREATE TABLE IF NOT EXISTS `liaoliao_message` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `mfrom` varchar(7) NOT NULL,
  `mto` varchar(7) NOT NULL,
  `mcon` varchar(100) NOT NULL,
  `time` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- 表的结构 `liaoliao_message_status`
--

CREATE TABLE IF NOT EXISTS `liaoliao_message_status` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `muser` varchar(7) NOT NULL,
  `mstatus` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- 表的结构 `liaoliao_favor`
--

CREATE TABLE IF NOT EXISTS `liaoliao_favor` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `user` varchar(7) NOT NULL,
  `favor` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- 表的结构 `liaoliao_topic_limit`
--

CREATE TABLE IF NOT EXISTS `liaoliao_topic_limit` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `user` varchar(7) NOT NULL,
  `time` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- 表的结构 `liaoliao_jubao`
--

CREATE TABLE IF NOT EXISTS `liaoliao_jubao` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `jubao_to` varchar(7) NOT NULL,
  `jubao_reason` varchar(30) NOT NULL,
  `jubao_from` varchar(7) NOT NULL,
  `jubao_url` varchar(100) NOT NULL,
  `time` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for table `liaoliao_reply`
--
ALTER TABLE `liaoliao_reply`
  ADD CONSTRAINT `reply_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `liaoliao_talk` (`id`) ON DELETE CASCADE;
  
--
-- Constraints for table `liaoliao_vote_ips`
--
ALTER TABLE `liaoliao_vote_ips`
  ADD CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`vid`) REFERENCES `liaoliao_vote` (`id`) ON DELETE CASCADE;