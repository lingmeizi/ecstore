<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class b2c_mdl_member_award extends dbeav_model{
    var $defaultOrder = array('member_award_id', ' ASC');
    function save(&$aData, $mustUpdate = NULL, $mustInsert = false){
        return parent::save($aData);
    }
    
    //根据订单金额查询是否有奖励金额
    function getAwardProfit($amount)
    {
       $aData = $this->getList('*');
       foreach ($aData as $key => $value) {
           if($value["start_money"]<$amount)
           {
               if($value["end_money"]==0)
               {
                   $profit=$value["award_discount"];
               }else if($amount<$value["end_money"])
               {
                   $profit=$value["award_discount"];
               }
           }
       }
       return $profit;
    }
}
