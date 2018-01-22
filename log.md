###商品分类表：
CREATE TABLE `cmf_category` (
  `category_id` mediumint(5) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `category_name` varchar(40) NOT NULL COMMENT '分类名称',
  `pid` mediumint(9) NOT NULL DEFAULT '0' COMMENT '父ID 默认为一级分类0',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品分类表';
###商品表
create table cmf_goods (
`goods_id` INT(10)  not null primary key auto_increment comment '自增主键',
`goods_name` VARCHAR(40) not null comment '商品名称',
`goods_price` DECIMAL(7,2) not null comment '商品价格',
`goods_brief` varchar(255) not null default ' ' comment '商品简介',
`goods_content` MEDIUMTEXT   comment '商品详情',
`goods_img` MEDIUMTEXT comment  '商品图片多张图片以;分割',
`goods_stu` TINYINT(1) not null default 1 comment '商品状态 1：正常 0隐藏'
) ENGINE=INNODB default charset=utf8  comment '商品表';

###商品 and 分类 中间表
CREATE TABLE `cmf_goods_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `g_id` int(10) NOT NULL COMMENT '商品ID',
  `c_id` mediumint(5) NOT NULL COMMENT '分类ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品&&分类关联表';

###商品分类 联查视图
CREATE VIEW `cmf_goods_category_view ` AS 
SELECT
cmf_goods.goods_name,
cmf_goods.goods_price,
cmf_goods.goods_brief,
cmf_goods.goods_content,
cmf_goods.goods_stu,
cmf_goods.goods_img,
cmf_goods_category.g_id,
cmf_goods_category.c_id,
cmf_category.category_name
FROM
cmf_goods
LEFT JOIN cmf_goods_category ON cmf_goods_category.g_id = cmf_goods.goods_id
LEFT JOIN cmf_category ON cmf_category.category_id = cmf_goods_category.c_id ;

###分类属性表

CREATE TABLE `cmf_category_attribute` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `c_id` mediumint(5) NOT NULL COMMENT '分类ID',
  `attr_name` varchar(40) NOT NULL COMMENT '属性名称',
  `sell_price` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '加价',
  PRIMARY KEY (`id`),
  KEY `cate_attr_fk` (`c_id`),
  CONSTRAINT `cate_attr_fk` FOREIGN KEY (`c_id`) REFERENCES `cmf_category` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类属性表';



###商品&&属性 关联表   sku
create table cmf_goods_attribute(
`id` INT(10) not null auto_increment PRIMARY key comment '自增主键',
`g_id` INT(10) not null comment '商品ID' ,
`attr_id` VARCHAR(40) not null comment '属性ID 多个属性使用;分割',
`goods_number` MEDIUMINT(5)  not null  DEFAULT 9999 comment '货品库存'
) ENGINE = INNODB DEFAULT CHARSET=UTF8 COMMENT '分类属性表';