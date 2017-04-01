<?php
/**
 * 提现银行实体类
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['member_order_info']=array (
  'columns' =>
  array (
    'id' =>
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
    'member_id' =>
    array (
      'default' => 0,
      'label' => app::get('b2c')->_('会员ID'),
      'sdfpath' => 'members/member_id',
      'width' => 75,
      'order' => 40,
      'type' => 'table:members',
      'editable' => true,
      'filtertype' => 'bool',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
   'order_id' =>
    array (
      'default' => 0,
      'label' => app::get('b2c')->_('订单id'),
      'sdfpath' => 'orders/order_id',
      'width' => 75,
      'order' => 40,
      'type' => 'table:orders',
      'editable' => true,
      'filtertype' => 'bool',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
   ),
  'engine' => 'innodb',
  'version' => '$Rev: 44523 $',
  'comment' => app::get('b2c')->_('业务员会员信息表'),
);
