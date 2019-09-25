-- 加盟课表
DROP TABLE IF EXISTS `dlm_franchise_course`;
CREATE TABLE `dlm_franchise_course` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '加盟标题',
  `subtitle` varchar(100) NOT NULL DEFAULT '' COMMENT '加盟副标题',
  `banner` json DEFAULT NULL COMMENT 'banner图，存json格式',
  `details` text COMMENT '详情介绍',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0:否 1:是',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='加盟课表';


-- 体验课程表
DROP TABLE IF EXISTS `dlm_experience_course`;
CREATE TABLE `dlm_experience_course` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '课程名称',
  `introduction` varchar(255) NOT NULL DEFAULT '' COMMENT '课程简介',
  `banner` json DEFAULT NULL COMMENT 'banner图，存json格式',
  `details` text COMMENT '详情介绍',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序，数字越大排名越靠前',
  `original_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原价',
  `experience_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '体验价',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '上下架 1:上架 2:下架',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0:否 1:是',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='体验课程表';


-- 会员表
DROP TABLE IF EXISTS `dlm_member`;
CREATE TABLE `dlm_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '微信唯一标识',
  `nickname` varchar(50) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '会员昵称',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '会员头像',
  `mobile` varchar(20) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '手机号',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
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
  `created_at` datetime DEFAULT NULL COMMENT '加入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='推广员表';


-- 购买记录表
DROP TABLE IF EXISTS `dlm_purchase_history`;
CREATE TABLE `dlm_purchase_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `experience_course_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '体验课ID',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '会员头像',
  `nickname` varchar(50) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '会员昵称',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '体验课状态 1:已购买 2:已面试 3:正在体验 4:体验完成',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购买记录表';


-- 加盟申请记录表
DROP TABLE IF EXISTS `dlm_franchise_apply`;
CREATE TABLE `dlm_franchise_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `franchise_course_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '加盟课程ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0：男 1：女',
  `age` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '年龄',
  `province` varchar(50) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(50) NOT NULL DEFAULT '' COMMENT '市',
  `area` varchar(50) NOT NULL DEFAULT '' COMMENT '区',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `apple_at` datetime DEFAULT NULL COMMENT '申请时间',
  `lately_handle_at` datetime DEFAULT NULL COMMENT '最近处理时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '加盟进度状态 1:信息已提交 2:资质已审核 3:教师培训 4:已开课 5:加盟完成 6:已结算返佣',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='加盟申请记录表';


-- 提现表
DROP TABLE IF EXISTS `dlm_withdraw`;
CREATE TABLE `dlm_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `apply_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '申请金额',
  `bank_name` varchar(50) NOT NULL DEFAULT '' COMMENT '银行名称',
  `branch_name` varchar(50) NOT NULL DEFAULT '' COMMENT '支行名称',
  `bank_account` varchar(50) NOT NULL DEFAULT '' COMMENT '银行账户',
  `withdraw_at` datetime DEFAULT NULL COMMENT '提现时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1:待审核 2:已审核',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='提现表';


-- 轮播图表
DROP TABLE IF EXISTS `dlm_banner`;
CREATE TABLE `dlm_banner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '轮播图地址',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导航类型 0、纯图片 1、体验课程 2、加盟课程',
  `type_relation_id` int(10) NOT NULL DEFAULT '0' COMMENT '导航目标ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序，数字越大排名越靠前',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0:否 1:是',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='轮播图表';


-- 管理员表
DROP TABLE IF EXISTS `dlm_admin`;
CREATE TABLE `dlm_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) NOT NULL DEFAULT '' COMMENT '密码盐',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1、正常 2、禁用 -1、删除',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='管理员表';

-- 设置表
DROP TABLE IF EXISTS `dlm_settings`;
CREATE TABLE `dlm_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '设置名称',
  `key` varchar(255) NOT NULL DEFAULT '' COMMENT '配置key',
  `value` varchar(255) NOT NULL DEFAULT '' COMMENT '配置value',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0:否 1:是',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='设置表';

-- 导航设置表
DROP TABLE IF EXISTS `dlm_navigation_settings`;
CREATE TABLE `dlm_navigation_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '导航名称',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '导航图标',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '导航类型 1、内容页 2、小程序跳转 3、电话拨打',
  `type_relation` varchar(100) NOT NULL DEFAULT '' COMMENT '导航目标',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='导航设置表';


-- 文章表
DROP TABLE IF EXISTS `dlm_article`;
CREATE TABLE `dlm_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '详情介绍',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0:否 1:是',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='文章表';


-- 微信支付记录
DROP TABLE IF EXISTS `dlm_wx_pay_log`;
CREATE TABLE `dlm_wx_pay_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `out_trade_no` varchar(32) NOT NULL DEFAULT '' COMMENT '商户订单号',
  `total_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单总金额，单位为分',
  `attach` varchar(255) NOT NULL DEFAULT '' COMMENT '附加数据',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '支付状态 1:待支付 2:已支付',
  `request_params` text COMMENT 'json_encode 请求信息',
  `response_params` text COMMENT 'json_encode 返回信息',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='微信支付记录';


-- 体验课进度明细
DROP TABLE IF EXISTS `dlm_experience_progress`;
CREATE TABLE `dlm_experience_progress` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买人id',
    `experience_course_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '体验课ID',
    `purchase_history_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买记录ID',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '体验课状态 1:已购买 2:已面试 3:正在体验 4:体验完成',
    `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
    `processing_at` datetime DEFAULT NULL COMMENT '处理时间',
    `created_at` datetime DEFAULT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='体验课进度明细';


-- 加盟课进度明细
DROP TABLE IF EXISTS `dlm_franchise_course_progress`;
CREATE TABLE `dlm_franchise_course_progress` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买人id',
  `franchise_course_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '体验课ID',
  `franchise_apply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请加盟ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '加盟进度状态 1:信息已提交 2:资质已审核 3:教师培训 4:已开课 5:加盟完成 6:已结算返佣',
  `remark` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `processing_at` datetime DEFAULT NULL COMMENT '处理时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='加盟课进度明细';


-- 推广员
DROP TABLE IF EXISTS `dlm_guider`;
CREATE TABLE `dlm_guider` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `nickname` varchar(100) NOT NULL COMMENT '会员姓名',
  `mobile` varchar(20)  NOT NULL DEFAULT '' COMMENT '手机号',
  `team_join_size` bigint(18) unsigned NOT NULL DEFAULT '0' COMMENT '团队体验课人数',
  `team_experience_size` bigint(18) unsigned NOT NULL DEFAULT '0' COMMENT '团队加盟人数',
  `total_comission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '累计佣金',
  `expect_comission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '待结算佣金',
  `comission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '佣金余额',
  `total_withdraw_comission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '累计提现',
  `add_guider_at` datetime DEFAULT NULL COMMENT '加入时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推广员表';


-- 推广员佣金明细表
DROP TABLE IF EXISTS `dlm_guider_detail`;
CREATE TABLE `dlm_guider_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `source` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单来源 1、体验课 2、加盟课',
  `source_order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来源订单id',
  `comission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '佣金',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:未结算, 1:已结算',
  `remark` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `settled_at` datetime DEFAULT NULL COMMENT '结算时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推广员订单表';


alter table `dlm_franchise_course` add `rebate_commission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '佣金返利';
alter table `dlm_experience_course` add `rebate_commission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '佣金返利';