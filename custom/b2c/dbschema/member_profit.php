<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['member_profit']=array (
  'columns' =>
  array (
    'member_profit_id' =>
    array (
       'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
    ),
    'profit_name' =>
    array (
      'type' => 'varchar(100)',
      'default' => '0.00',
      'required' => true,
      'editable' => true,
      'label' => app::get('b2c')->_('佣金规则'),
       'in_list' => true,
      'default_in_list' => true,
    ),
    'default_profit' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'editable' => true,
       'label' => app::get('b2c')->_('是否默认'),
      'filtertype' => 'bool',
      'filterdefault' => 'true',
      'in_list' => true,
      'default_in_list' => true,
    ),
     'profit1' =>
    array (
      'type' => 'decimal(5,2)',
      'comment' => app::get('b2c')->_('佣金比例'),
      'label' => app::get('b2c')->_('佣金比例'),
      'match' => '[0-9\\.]+',
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'discount' =>
    array (
      'type' => 'decimal(5,2)',
      'comment' => app::get('b2c')->_('折扣率'),
      'label' => app::get('b2c')->_('折扣率'),
      'match' => '[0-9\\.]+',
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'tag_id' =>
    array (
      'type' => 'table:tag@desktop',
      'required' => true,
      'label' => '商品标签',
      'comment' => '商品标签',
      'editable' => false,
      'width' =>200,
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'start_time' =>
    array (
      'label' => app::get('b2c')->_('生效时间'),
      'width' => 75,
      'type' => 'time',
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
      'comment' => app::get('b2c')->_('生效时间'),
    ),
    'end_time' =>
    array (
      'label' => app::get('b2c')->_('失效时间'),
      'width' => 75,
      'type' => 'time',
      'editable' => false,
      'filtertype' => 'time',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
      'comment' => app::get('b2c')->_('失效时间'),
    ),
   ),
  'engine' => 'innodb',
  'version' => '$Rev: 44523 $',
  'comment' => app::get('b2c')->_('会员佣金表'),
);
