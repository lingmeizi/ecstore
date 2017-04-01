<?php
/**
 * 后台自动任务，收货确认七天后的订单自动分佣
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_tasks_member_sale extends base_task_abstract implements base_interface_task{

    public function exec($params=null){
       $member_drp=app::get('b2c')->model('member_drp');
       //查询确认收货的订单（并且有佣金明细数据的未审核）
      $sql=" select received_time,order_id from sdb_b2c_orders where order_id in (select  order_id  from sdb_b2c_member_sale  where status=0) and received_status='1'";
      $row = kernel::database()->select($sql); 
      foreach ($row as $key => $value) {
        //查询提现或者分佣金的金额
       $memberlog  = app::get('b2c')->model('member_sale')->getList("member_id,money,sale_id",array("order_id"=>$value["order_id"]));
       foreach ($memberlog as $k => $v) {
            //查询对应用户的金额然后更新
            $user_amount  = $member_drp->getRow("user_money",array("member_id"=>$v["member_id"]));
            $dif_amount=$user_amount["user_money"]+$v["money"];
             $sdf= array( 'user_money'   =>$dif_amount);
             $logsdf=array('status'   =>1);
            //修改佣金状态
            $status=app::get('b2c')->model('member_sale')->update($logsdf,array("sale_id"=>$v["sale_id"]));
            //修改用户佣金总额
            $result=$member_drp->update($sdf,array('member_id' => $v["member_id"]));
       }
      }
    }
}