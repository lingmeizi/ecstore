<?php
/**
 * 订单购买佣金分成
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['member_sale']=array (
  'columns' =>
  array (
    'sale_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      'width' => 110,
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
    ),
      'order_id' =>
    array (
      'default' => 0,
      'label' => app::get('b2c')->_('订单号'),
      'sdfpath' => 'orders/order_id',
      'searchtype' => 'has',
      'width' => 75,
      'order' => 40,
      'type' => 'table:orders',
      'editable' => true,
      'filtertype' => 'bool',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'goods_id' =>
    array (
      'default' => 0,
      'label' => app::get('b2c')->_('商品名称'),
      'sdfpath' => 'goods/goods_id',
      'searchtype' => 'has',
      'width' => 75,
      'order' => 40,
      'type' => 'table:goods',
      'editable' => true,
      'filtertype' => 'bool',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true
    ),
   'tag_id' =>
    array (
      'default' => 0,
      'label' => app::get('b2c')->_('标签'),
      'type' => 'table:tag@desktop',
      'sdfpath' => 'tag/tag_id',
      'width' => 75,
      'order' => 40,
      'editable' => true
    ),
    'member_id' =>
    array (
      'default' => 0,
      'label' => app::get('b2c')->_('用户'),
      'sdfpath' => 'members/member_id',
      'searchtype' => 'has',
      'width' => 75,
      'order' => 40,
      'type' => 'table:members',
      'editable' => true,
      'filtertype' => 'bool',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
   'member_name' =>
    array (
      'default' => 0,
      'label' => app::get('b2c')->_('用户名'),
      'searchtype' => 'has',
      'width' => 75,
      'order' => 40,
      'type' => 'varchar(50)',
      'editable' => true,
      'filtertype' => 'bool',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'money' =>
    array (
      'type' => 'money',
      'default' => '0',
      'label' => app::get('b2c')->_('佣金金额'),
      'required' => true,
      'width' => 110,
      'filtertype' => 'number',
      'editable' => false,
      'in_list' => true,
    ),
      
    'amount' =>
    array (
      'type' => 'money',
      'default' => '0',
      'label' => app::get('b2c')->_('商品金额'),
      'required' => true,
      'width' => 110,
      'filtertype' => 'number',
      'editable' => false,
      'in_list' => true,
    ),
    'change_time' =>
    array (
      'label' => app::get('b2c')->_('操作时间'),
      'width' => 75,
      'type' => 'time',
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
      'comment' => app::get('b2c')->_('操作时间'),
    ),
   'status' =>
    array (
      'type' => 'tinyint(1)',
      'default' => 0,
      'searchtype' => 'has',
      'required' => true,
      'label' => app::get('b2c')->_('是否支付'),
      'comment' => app::get('b2c')->_('0 未分成/未支付     1 已分成/已经支付 '),
      'width' => 110,
      'editable' => false,
      'in_list' => false,
    ),
   ),
  'engine' => 'innodb',
  'version' => '$Rev: 44523 $',
  'comment' => app::get('b2c')->_('会员佣金信息'),
);
