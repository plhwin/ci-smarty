-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 08 月 19 日 11:30
-- 服务器版本: 5.6.12
-- PHP 版本: 5.5.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `ci_smarty`
--

-- --------------------------------------------------------

--
-- 表的结构 `ci_admin_logs`
--

CREATE TABLE IF NOT EXISTS `ci_admin_logs` (
  `logid` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `adminid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发生时间',
  `description` varchar(1000) NOT NULL COMMENT '描述',
  PRIMARY KEY (`logid`),
  KEY `idx_at` (`adminid`,`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ci_admin_user`
--

CREATE TABLE IF NOT EXISTS `ci_admin_user` (
  `adminid` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `adminname` varchar(32) NOT NULL COMMENT '管理员登录名',
  `password` varchar(32) NOT NULL COMMENT '管理员密码',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `realname` varchar(32) NOT NULL COMMENT '管理员真实姓名',
  `privileges` text NOT NULL COMMENT '权限',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '管理员状态，0=禁止,1=正常',
  PRIMARY KEY (`adminid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- --------------------------------------------------------

--
-- 表的结构 `ci_member`
--

CREATE TABLE IF NOT EXISTS `ci_member` (
  `uid` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '会员uid',
  `nickname` varchar(30) NOT NULL COMMENT '会员昵称',
  `inviteuid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '邀请人uid',
  `invitenum` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '该会员邀请的会员总数',
  `userpwd` varchar(32) NOT NULL COMMENT '登录密码',
  `authpwd` varchar(32) NOT NULL COMMENT '验证密码',
  `salt` varchar(6) NOT NULL COMMENT '密码盐,给密码加点盐',
  `email` varchar(50) NOT NULL COMMENT '邮箱地址',
  `emailcert` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '邮箱是否通过验证，0=否，1=是',
  `regtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `regip` varchar(15) NOT NULL COMMENT '注册IP',
  `lastlogintime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次登录时间',
  `lastloginip` varchar(15) NOT NULL COMMENT '最后一次登录IP',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会员状态,0=禁用，1=正常',
  PRIMARY KEY (`uid`),
  KEY `idx_invitenum` (`invitenum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员表';

-- --------------------------------------------------------

--
-- 表的结构 `ci_session`
--

CREATE TABLE IF NOT EXISTS `ci_session` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员UID',
  `nickname` char(30) NOT NULL COMMENT '会员昵称',
  `authpwd` char(32) NOT NULL COMMENT '验证密码',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后活动时间',
  `ip` char(15) NOT NULL COMMENT 'IP地址',
  PRIMARY KEY (`uid`),
  KEY `idx_lastactivity` (`lastactivity`),
  KEY `idx_ip` (`ip`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
