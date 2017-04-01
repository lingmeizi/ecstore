<?php
/**
 * 提现申请
 * 提现生成一条佣金记录，并更新佣金总额
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['member_back']=array (
  'columns' =>
  array (
    'back_id' =>
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
    'money' =>
    array (
      'type' => 'money',
      'default' => '0',
      'label' => app::get('b2c')->_('提现金额'),
      'required' => true,
      'width' => 110,
      'filtertype' => 'number',
      'editable' => false,
      'in_list' => true,
    ),
    'balance_money' =>
    array (
      'type' => 'money',
      'default' => '0',
      'label' => app::get('b2c')->_('余额'),
      'required' => true,
      'width' => 110,
      'filtertype' => 'number',
      'editable' => false,
      'in_list' => true,
    ),
    'pay_money' =>
    array (
      'type' => 'money',
      'default' => '0',
      'label' => app::get('b2c')->_('支付金额'),
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
  'change_type' =>
    array (
      'type' => 'tinyint(1)',
      'default' => 0,
      'required' => true,
      'label' => app::get('b2c')->_('类别'),
      'comment' => app::get('b2c')->_('0 提现申请    1 管理员后台操作佣金 '),
      'width' => 110,
      'editable' => false,
      'in_list' => false,
    ),
  'status' =>
    array (
      'type' => 'tinyint(1)',
      'default' => 0,
      'required' => true,
      'label' => app::get('b2c')->_('是否支付'),
      'comment' => app::get('b2c')->_('0 未分成/未支付     1 已分成/已经支付 '),
      'width' => 110,
      'editable' => false,
      'in_list' => false,
    ),
   'bank_info' =>
    array (
      'label' => app::get('b2c')->_('收款信息'),
      'searchtype' => 'has',
      'comment' => app::get('b2c')->_('提现银行信息'),
      'type' => 'text',
      'width' => 75,
      'in_list' => true,
    ),
   'member_id' =>
    array (
      'default' => 0,
      'label' => app::get('b2c')->_('用户id'),
      'sdfpath' => 'members/member_id',
      'width' => 75,
      'order' => 40,
      'type' => 'table:members',
      'editable' => true,
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
   ),
  'engine' => 'innodb',
  'version' => '',
  'comment' => app::get('b2c')->_('会员佣金信息'),
);
