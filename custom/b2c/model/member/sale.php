<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class b2c_mdl_member_sale extends dbeav_model{
    var $defaultOrder = array('sale_id', ' desc');
    function save(&$aData, $mustUpdate = NULL, $mustInsert = false){
        return parent::save($aData);
    }
    

    /**
      * 提交订单更新佣金信息
     * @param $order_id 订单号
     * $order_goods订单商品明细
     */
    public function update_order_sale($order_id,$member_id){
        $profitModel=app::get("b2c")->model('member_profit');
        $o_tag = app::get('desktop')->model('tag_rel');
        $userObject = kernel::single('b2c_user_object');
        $user_name=$userObject->get_member_name(null,$member_id);
        if(isset($order_id)&&isset($member_id)){
            $order_items=app::get("b2c")->model('order_items')->getList("goods_id,amount",array("order_id"=>$order_id));
            if($order_items)
            {
            foreach($order_items as $key=>$val){
               $arr_tags = $o_tag->getRow('tag_id',array('rel_id'=>$val["goods_id"],'tag_type'=>'goods','app_id'=>'b2c','tag_id|noequal'=>'0'));//根据商品id查询标签id
                if($arr_tags)
                {
                   //根据标签查询佣金规则。
                   $profit=$profitModel->getRow("*",array("tag_id"=>$arr_tags["tag_id"],"start_time|sthan"=>time(),"end_time|than"=>time()));
                    $amount=$this->getAllAmount($member_id,$arr_tags["tag_id"]);
                   $discount=$profit['profit1'];
                   //如果历史金额
                   $mem_award = $this->app->model('member_award')->getAwardProfit($amount);
                   $discount=$discount+$mem_award;
                   // 分销商每个商品利润相加
                    $sale_money= $val['amount']*$discount;
                      if($member_id&&$sale_money>0){
                            /* 插入帐户变动记录 */
                            $account_log = array(
                                'members'   =>array("member_id"=>$member_id) ,//用户名
                                'member_name'   =>$user_name,//用户名
                                'orders'   =>array("order_id"=>$order_id) ,//订单号
                                'goods'   =>array("goods_id"=>$val["goods_id"]) ,//商品id
                                'tag'   =>array("tag_id"=>$arr_tags["tag_id"]) ,//订单号
                                'money'    => $sale_money,//分成金额
                                'amount' =>$val['amount'],//商品金额
                                'change_time'   => time(), //订单时间                   
                            );
                            //如果上级分成写入数据库
                            $this->save($account_log);
                        }
                }
              }
             }
            //插入数据到用户与订单关联表，用于查询我的下级订单
            $sql = 'INSERT INTO sdb_b2c_member_order_info (member_id,order_id) VALUES ('.$member_id.','.$order_id.' )';
            $this->db->exec($sql);
        }  
    }
    
    /**
      * 经销商的下级会员购买商品按折扣计算
     * @param $member_id 会员id
     */
    public function update_order_discount($goods){
       $profitModel=app::get("b2c")->model('member_profit');
       $o_tag = app::get('desktop')->model('tag_rel');
        $userObject = kernel::single('b2c_user_object');
        if(isset($goods)){
            foreach($goods as $key=>$val){
               $arr_tags = $o_tag->getRow('tag_id',array('rel_id'=>$val["params"]["goods_id"],'tag_type'=>'goods','app_id'=>'b2c','tag_id|noequal'=>'0'));//根据商品id查询标签id
                if($arr_tags)
                {
                   //根据标签查询佣金规则。
                   $profit=$profitModel->getRow("*",array("tag_id"=>$arr_tags["tag_id"],"start_time|sthan"=>time(),"end_time|than"=>time()));
                    // 分销商每个商品利润相加
                   $sale_money+= $val['subtotal']*$profit['discount'];
                }
              }
        } 
        return $sale_money;
    }
    
    /**
      * 退款退款退款更新佣金信息
     * @param $order_id 订单号
     * @param $member_id 退款用户
     * @param $refund_money 退款金额
     */
    public function update_order_sale_refund($order_id,$member_id,$refund_money){
        //根据订单号查询
         $profitModel=app::get("b2c")->model('member_profit');
         $orders_amount =$this->getList('*',array('order_id'=>$order_id,"money|than"=>0));
         //如果存在分佣信息，就退佣金
         if($orders_amount)
         {
            $members =app::get("b2c")->model('members')->getRow('member_pre_id',array('member_id'=>$member_id));
            foreach ($orders_amount as $key => $value) {
               $profit_id= $value["profit_id"];//根据id查询佣金规则，判断是用户的上一级就用profit1,否则就用profit2;
               $profit=$profitModel->getRow('*',array('member_profit_id'=>$profit_id));
               if($value["member_id"]==$members["member_pre_id"])
               {
                   $money=  $refund_money/100*$profit['profit1'];
               }else{
                   $money=  $refund_money/100*$profit['profit2'];
               }
               $date["money"]='-'.$money;//佣金金额
               $date["order_money"]= $refund_money;//订单金额==退款金额
               $date["orders"]=array("order_id"=>$order_id);
               $date["members"]=array("member_id"=>$value["member_id"]) ;
               $date["member_name"]=$value["member_name"];
               $date["member_profit"]=array("member_profit_id"=>$profit_id);
               $date["profit_name"]=$value["profit_name"];
               $date["change_time"]=time();
               $this->save($date);
            }
         }
    }
    
    
    
    
    
    
    
    
    /**
     * 佣金明细数据
     * @params string member id
     * @params string page number
     */
    public function fetchBysale($member_id, $nPage=1, $filter_pre=array(),$limit=10)
    {
        if (!$limit)
          $limit = 10;
          $limitStart = ($nPage-1) * $limit;
         if (isset($member_id))
         {
            $filter = array('member_id' => $member_id);
           $res =$this->getList('*',$filter, $limitStart, $limit, 'change_time DESC');
           if($res)
           {
             foreach ($res as $k => $v) {
                $res[$k]['order_id'] = $v['order_id'];
                $res[$k]['change_type'] = $v['change_type'];
                $res[$k]['type'] = $v['change_type'] == 0 ? '佣金分成' : '佣金提现';
                $res[$k]['change_time'] =date('Y-m-d H:i:s', $v['change_time']);
                if($v['change_type'] == 0)
                {
                $res[$k]['change_desc'] =$v['change_desc'];
                }else{
                 $res[$k]['change_desc'] =$v['bank_info'] ;
                }
                $res[$k]['money'] = abs($v['money']);
                 $res[$k]['bank_info'] = $v['bank_info'];
                $res[$k]['status'] = $v['status'] == 0 ?  "<font style='font-weight:bold;color:red'>等待处理</font>": "<font style='font-weight:bold'>成功</font>";
                if($v['order_id'] > 0){
                          $value =app::get('b2c')->model('orders')->getRow('order_id,member_id,status,ship_status,pay_status',array("order_id"=>$v['order_id'])); 
                          $username=app::get('pam')->model('members')->getRow('login_account', array('member_id'=>$value["member_id"], 'login_type'=>'local'));
                          $res[$k]['username']=$username["login_account"];
//                         $status =app::get('b2c')->model('orders')->trasform_status("status",  $value['status']);
//                         $pay_status =$value['pay_status']==null?"未付款":app::get('b2c')->model('orders')->trasform_status("pay_status",  $value['pay_status']);
//                         $shipping_status =$value['ship_status']==null?"未发货":app::get('b2c')->model('orders')->trasform_status("ship_status", $value['ship_status']);
                           $res[$k]['order_status'] =$this->getOrderStatus($value);				
                }
            }
           }
        }
        // 生成分页组建
        $countRd = $this->count($filter);
        $total = ceil($countRd/$limit);
        $current = $nPage;
        $token = '';
        $arrPager = array(
            'current' => $current,
            'total' => $total,
            'token' => $token,
        );
        $arrdata['data'] = $res;
        $arrdata['pager'] = $arrPager;
        return $arrdata;
    }
        //查询金额
    function getmoney($member,$status)
    {
        $sql="select sum(money) as money from sdb_b2c_member_sale where member_id=$member and status=$status";
        $row=$this->db->selectrow($sql);
        return $row["money"];
    }
    
    //查询已付款未确认金额
    function getNOSureMoney($member,$status)
    {
        //$sql="select sum(money) as money from sdb_b2c_member_sale where member_id=$member and status=$status";
        $sql="select sum(s.money) as money from sdb_b2c_member_sale s ".
" inner join sdb_b2c_orders   o  on s.order_id=o.order_id and s.member_id=$member and s.status=$status and o.pay_status in(2,4,5)";
        $row=$this->db->selectrow($sql);
        return $row["money"];
    }
    
    //查询某个经销商的历史销售某种商品标签的历史销售总额
    function getAllAmount($member,$tag_id)
    {
       $sql="SELECT sum(amount) as amount FROM sdb_b2c_member_sale  where member_id=$member and tag_id=$tag_id and status=1";
        $row=$this->db->selectrow($sql);
        return $row["amount"];
    }
    
    function getOrderStatus($sdf_order)
    {
      switch ($sdf_order['status']) {
            case 'active':
                $data['orderStatus']='活动订单';
                break;
            case 'dead':
                $data['orderStatus']='已作废';
                break;
            case 'finish':
                $data['orderStatus']='已完成';
                break;
            default:
                $data['orderStatus']='活动订单';
                break;
        }
        //支付状态
        switch ($sdf_order['pay_status']) {
            case 0:
                $data['payStatus']='未支付';
                break;
            case 1:
                $data['payStatus']='已支付';
                break;
            case 2:
                $data['payStatus']='已付款至到担保方';
                break;
            case 3:
                $data['payStatus']='部分付款';
                break;
            case 4:
                $data['payStatus']='部分退款';
                break;
            case 5:
                $data['payStatus']='全额退款';
                break;
            default:
                break;
        }
        //发货状态
        switch ($sdf_order['ship_status']) {
            case 0:
                $data['shipStatus']='未发货';
                break;
            case 1:
                $data['shipStatus']='已发货';
                break;
            case 2:
                $data['shipStatus']='部分发货';
                break;
            case 3:
                $data['shipStatus']='部分退货';
                break;
            case 4:
                $data['shipStatus']='已退货';
                break;
            default:
                break;
        }
      return $data['orderStatus']." ".$data['payStatus']." ".$data['shipStatus'];
    }
}
