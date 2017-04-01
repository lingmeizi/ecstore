<?php

/*
 *ecstore配置定时任务(如每10分钟一次),执行发货读取操作,
 * 读取跨境通的订单的发货状态, 生成ecstore的发货单, 并更新对应对单的发货状态
 * 测试
 */
class b2c_tasks_order_sOTrace extends base_task_abstract implements base_interface_task{

     public function exec($params=null){
        $obj_order = app::get("b2c")->model('orders');
        $oCorp = app::get("b2c")->model('dlycorp');
        $order_id="170221145884830";
        $arr_order = $obj_order->getRow('*',array('order_id'=>$order_id));
        $sdf =$arr_order;
        $sdf['logi_no'] ='170221145884830';
        $sdf['opid'] = 1;
        $sdf['opname'] ='admin';
        logger::info("校验参数如下：");
        logger::info('signin-data:'.var_export($sdf,true));
         $this->controller->begin();
        $obj_checkorder = kernel::service('b2c_order_apps', array('content_path'=>'b2c_order_checkorder'));
        if (!$obj_checkorder->check_order_delivery($sdf['order_id'],$sdf,$message))
        {
            logger::info("校验错误====：");
            $this->controller->end();
        }
        // 处理支付单据.
        $objB2c_delivery = b2c_order_delivery::getInstance(app::get("b2c"), app::get("b2c")->model('delivery'));
        $conOrder=kernel::single('b2c_ctl_admin_order');
         logger::info("fahuo======：");
        logger::info('signin-data:'.var_export($conOrder,true));
        if ($objB2c_delivery->generate($sdf, $conOrder, $message))
        {

            if($order_object = kernel::service('b2c_order_rpc_async')){
                $order_object->modifyActive($sdf['order_id']);
            }
            $obj_delivery_time = app::get('b2c')->model('order_delivery_time');
            $arr_delivery_time = array('order_id'=>$sdf['order_id'],'delivery_time'=>time()+10*24*3600);
            $obj_delivery_time->save($arr_delivery_time);
            //$obj_coupon = kernel::single("b2c_coupon_order");
            //if( $obj_coupon ){
            //    $obj_coupon->use_c($sdf['order_id']);
            //}
            if($arr_order['source'] == 'penker'){
                $logi_name = $oCorp->getRow('name',array('corp_id'=>$sdf['logi_id']));
                $express_info = array(
                    'eno' => $sdf['logi_no'],
                    'ename' => $logi_name['name'],
                    );
                $arr_pengker = array(
                    'order_id' => $sdf['order_id'],
                    'ship_status' => 1,
                    'express_info' => json_encode($express_info),
                    );
                kernel::single('penker_service_order')->update($arr_pengker);
            }
            $this->controller->end();
        }
        else
        {
            $this->controller->end();
        }
    }
}