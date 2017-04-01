<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class b2c_finder_member_sale{   

    //添加提现通过按钮
//    var $column_audit= '审核';
//    public function column_audit($row){
//         $member =app::get('b2c')->model('member_sale')->getRow('status,order_id',array('sale_id'=>$row['sale_id']));
//         $value =app::get('b2c')->model('orders')->getRow('pay_status',array("order_id"=>$member['order_id'])); 
//        if($member["status"]==1)
//        {
//            $return="审核通过";
//        }else{
//            //如果订单支付状态非已付款,部分退款，按钮显示灰色
//           if($value["pay_status"]==1||$value["pay_status"]==5||$value["pay_status"]==4){
//                 $target = '{onComplete:function(){if (finderGroup&&finderGroup[\'' . $_GET['_finder']['finder_id'] . '\']) finderGroup[\'' . $_GET['_finder']['finder_id'] . '\'].refresh();}}';
//                 $return = '<a target="'.$target.'" href="index.php?app=b2c&ctl=admin_member_sale&act=audit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['sale_id'] .'">'.app::get('b2c')->_('审核').'</a>';
//           }else{
//               $return = '<a  readonly="true" style="color:#CCC">'.app::get('b2c')->_('审核').'</a>';
//             }
//        }
//        return $return;
//    }  
    
    
    
        //添加提现通过按钮
    var $column_audit= '审核';
    public function column_audit($row){
         $member =app::get('b2c')->model('member_sale')->getRow('status,order_id',array('sale_id'=>$row['sale_id']));
         $value =app::get('b2c')->model('orders')->getRow('pay_status',array("order_id"=>$member['order_id'])); 
        if($member["status"]==1)
        {
            $return="已到账";
        }else{
           $return="未到账";
        }
        return $return;
    }  
    
    
    
   var $column_status= '订单状态';
    public function column_status($row){
         $value =app::get('b2c')->model('orders')->getRow('status,pay_status,ship_status',array("order_id"=>$row['order_id'])); 
        return  app::get('b2c')->model('member_sale')->getOrderStatus($value);
    }  
     
}
