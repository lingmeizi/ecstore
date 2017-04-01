<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['member_award']=array (
  'columns' =>
  array (
    'member_award_id' =>
    array (
       'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
    ),
    'start_money' =>
    array (
      'type' => 'money',
      'comment' => app::get('b2c')->_('起始金额'),
      'label' => app::get('b2c')->_('起始金额'),
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),     
   'end_money' =>
    array (
      'type' => 'money',
      'comment' => app::get('b2c')->_('最高金额'),
      'label' => app::get('b2c')->_('最高金额'),
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ), 
    'award_discount' =>
    array (
      'type' => 'decimal(5,2)',
      'comment' => app::get('b2c')->_('奖励折扣率'),
      'label' => app::get('b2c')->_('奖励折扣率'),
      'match' => '[0-9\\.]+',
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
   ),
  'engine' => 'innodb',
  'version' => '$Rev: 44523 $',
  'comment' => app::get('b2c')->_('会员佣金表'),
);
