-- 加盟课表
DROP TABLE IF EXISTS `dlm_franchise_course`;
CREATE TABLE `dlm_franchise_course` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '加盟标题',
  `subtitle` varchar(100) NOT NULL DEFAULT '' COMMENT '加盟副标题',
  `banner` varchar(255) NOT NULL DEFAULT '' COMMENT 'banner图，存json格式',
  `details` text COMMENT '详情介绍',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0:否 1:是',
  `created_at` datetime NOT NULL DEFAULT '' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '' COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='加盟课表';


-- 体验课程表
DROP TABLE IF EXISTS `dlm_experience_course`;
CREATE TABLE `dlm_experience_course` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '课程名称',
  `introduction` varchar(255) NOT NULL DEFAULT '' COMMENT '课程简介',
  `banner` varchar(255) NOT NULL DEFAULT '' COMMENT 'banner图，存json格式',
  `details` text COMMENT '详情介绍',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序，数字越大排名越靠前',
  `original_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原价',
  `experience_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '体验价',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '上下架 1:上架 2:下架',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0:否 1:是',
  `created_at` datetime NOT NULL DEFAULT '' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '' COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='体验课程表';


-- 会员表
DROP TABLE IF EXISTS `dlm_member`;
CREATE TABLE `dlm_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '微信唯一标识',
  `nickname` varchar(50) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '会员昵称',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '会员头像',
  `created_at` datetime NOT NULL DEFAULT '' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '' COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员表';


-- 推广员表
DROP TABLE IF EXISTS `dlm_promoter`;
CREATE TABLE `dlm_promoter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nickname` varchar(50) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '会员昵称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推广人数',
  `total_commission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '累计佣金',
  `no_settled_commission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '待结算佣金',
  `commission_balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '佣金余额',
  `total_withdraw` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '累计提现',
  `created_at` datetime NOT NULL DEFAULT '' COMMENT '加入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='推广员表';


-- 购买记录表
DROP TABLE IF EXISTS `dlm_purchase_history`;
CREATE TABLE `dlm_purchase_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '会员头像',
  `nickname` varchar(50) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '会员昵称',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '体验课状态 1:已购买 2:已面试 3:正在体验 4:体验完成',
  `created_at` datetime NOT NULL DEFAULT '' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '' COMMENT '更改时间',
  PRIMARY KEY (`id`)
  KEY `idx_name` (`name`)
  KEY `idx_mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购买记录表';


-- 加盟申请记录表
DROP TABLE IF EXISTS `dlm_franchise_apply`;
CREATE TABLE `dlm_franchise_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `apple_at` datetime NOT NULL DEFAULT '' COMMENT '申请时间',
  `lately_handle_at` datetime NOT NULL DEFAULT '' COMMENT '最近处理时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '加盟进度状态 1:信息已提交 2:资质已审核 3:教师培训 4:已开课 5:加盟完成 6:已结算返佣',
  `created_at` datetime NOT NULL DEFAULT '' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '' COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='加盟申请记录表';


-- 提现表
DROP TABLE IF EXISTS `dlm_withdraw`;
CREATE TABLE `dlm_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额',
  `withdraw_at` datetime NOT NULL DEFAULT '' COMMENT '提现时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1:待审核 2:已审核',
  `created_at` datetime NOT NULL DEFAULT '' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '' COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='提现表';


-- 轮播图
DROP TABLE IF EXISTS `dlm_banner`;
CREATE TABLE `dlm_banner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '轮播图地址',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序，数字越大排名越靠前',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0:否 1:是',
  `created_at` datetime NOT NULL DEFAULT '' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '' COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='提现表';


-- 管理员表
DROP TABLE IF EXISTS `dlm_admin`;
CREATE TABLE `dlm_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) NOT NULL DEFAULT '' COMMENT '密码盐',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1、正常 2、禁用 -1、删除',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='管理员表';