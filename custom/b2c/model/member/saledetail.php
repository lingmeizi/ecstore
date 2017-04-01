<?php
/**
 * 提现model
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class b2c_mdl_member_saledetail extends dbeav_model{
    var $defaultOrder = array('m.member_id', ' ASC');
    //关联dbschema
        function __construct($app){
         parent::__construct($app);
        $this->app = $app;
        $this->columns = array(
                        'rowno'=>array('label'=>app::get('b2c')->_('序号'),'width'=>200),
                        'real_name'=>array('label'=>app::get('b2c')->_('分销商'),'width'=>200),
                        'member_id'=>array('label'=>app::get('b2c')->_('会员'),'width'=>200),
                        'order_id'=>array('label'=>app::get('b2c')->_('订单号'),'width'=>200),
                        'final_amount'=>array('label'=>app::get('b2c')->_('订单金额'),'width'=>100),
                        'money'=>array('label'=>app::get('b2c')->_('佣金金额'),'width'=>100, 'type'=>'money'),
                        'brand_name'=>array('label'=>app::get('b2c')->_('商品品牌'),'width'=>100),
                        'name'=>array('label'=>app::get('b2c')->_('商品'),'width'=>100),
                        'received_status'=>array('label'=>app::get('b2c')->_('收货状态'),'width'=>100),
                        't_payed'=>array('label'=>app::get('b2c')->_('付款时间'),'filtertype' => 'bool','filterdefault' => 'true','width'=>100,'type'=>'time'),
                        'member_drp_id'=>array('label'=>app::get('b2c')->_('分销商'),'filtertype' => 'normal','filterdefault' => 'true','width'=>200,'type' => 'table:member_drp', 'sdfpath' => 'member_drp/member_drp_id','in_list' => false),
                        'brand_id'=>array('label'=>app::get('b2c')->_('商品品牌'),'filtertype' => 'normal','filterdefault' => 'true','width'=>200,'type' => 'table:brand', 'sdfpath' => 'brand/brand_id','in_list' => false),
                        'tag_id'=>array('label'=>app::get('b2c')->_('商品标签'),'filtertype' => 'normal','filterdefault' => 'true','width'=>200,'type' => 'table:tag@desktop','sdfpath' => 'tag/tag_id','in_list' => false),
                   );
        $this->list = array(
                       'rowno'=>array('label'=>app::get('b2c')->_('序号'),'width'=>200),
                       'real_name'=>array('label'=>app::get('b2c')->_('分销商'),'width'=>200),
                        'member_id'=>array('label'=>app::get('b2c')->_('会员'),'width'=>200),
                        'order_id'=>array('label'=>app::get('b2c')->_('订单号'),'width'=>200),
                        'final_amount'=>array('label'=>app::get('b2c')->_('订单金额'),'width'=>100),
                        'money'=>array('label'=>app::get('b2c')->_('佣金金额'),'width'=>100, 'type'=>'money'),
                        'brand_name'=>array('label'=>app::get('b2c')->_('商品品牌'),'width'=>100),
                        'name'=>array('label'=>app::get('b2c')->_('商品'),'width'=>100),
                        'received_status'=>array('label'=>app::get('b2c')->_('收货状态'),'width'=>100),
                   );
        $this->schema = array(
                'default_in_list'=>array_keys($this->list),
                'in_list'=>array_keys($this->list),
                'idColumn'=>'rowno',
                'columns'=>$this->columns
            );
    }
    
    function get_schema(){
        return $this->schema;
    }

 
    function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null){
//        $sql="select * from (select  d.real_name,m.member_id,o.order_id ,o.final_amount,s.money,o.received_status,b.brand_name,b.brand_id,t.tag_id,d.member_drp_id, g.name,p.t_payed  from sdb_b2c_members m
//        INNER  JOIN sdb_b2c_member_drp d on m.member_id=d.member_id
//        INNER JOIN sdb_b2c_member_order_info oi on m.member_id=oi.member_id
//        inner join sdb_b2c_orders  o on o.order_id=oi.order_id
//        inner join sdb_ectools_order_bills bi on bi.rel_id=o.order_id
//        INNER JOIN sdb_ectools_payments p on  bi.bill_id=p.payment_id 
//        INNER JOIN sdb_b2c_member_sale  s on s.order_id=o.order_id
//        inner join sdb_b2c_order_items  i on i.order_id=o.order_id
//        INNER JOIN sdb_b2c_goods  g on  g.goods_id=i.goods_id 
//        INNER JOIN sdb_desktop_tag_rel  r on  r.rel_id=g.goods_id 
//        INNER JOIN sdb_desktop_tag  t on  t.tag_id=r.tag_id 
//        inner JOIN sdb_b2c_brand b on b.brand_id=g.brand_id  and bi.pay_object='order' and bi.bill_type='payments') as sdb_b2c_member_saledetail where ".$this->_filter($filter);//判断是业务员并且有过订单佣金信息的
        $sql="select (@rowNO := @rowNo+1) AS rowno,real_name,member_id,order_id,final_amount,money,brand_name,name,received_status,t_payed,member_drp_id,brand_id,tag_id 
        from (select   d.real_name,s.member_id,o.order_id ,o.final_amount,s.money,b.brand_name,g.name,o.received_status, p.t_payed,d.member_drp_id, b.brand_id,s.tag_id from sdb_b2c_member_sale s
        INNER  JOIN sdb_b2c_member_drp d on s.member_id=d.member_id
        inner join sdb_b2c_orders  o on o.order_id=s.order_id
        inner join sdb_ectools_order_bills bi on bi.rel_id=o.order_id
        INNER JOIN sdb_ectools_payments p on  bi.bill_id=p.payment_id 
        inner join sdb_b2c_order_items  i on i.order_id=o.order_id
        INNER JOIN sdb_b2c_goods  g on  g.goods_id=s.goods_id  and g.goods_id=i.goods_id
        inner JOIN sdb_b2c_brand b on b.brand_id=g.brand_id  and bi.pay_object='order' and bi.bill_type='payments' and s.status=1) as sdb_b2c_member_saledetail ,(select @rowNo:=0) as it where ".$this->_filter($filter)."";//判断是业务员并且有过订单佣金信息的
        $data=$this->db->selectLimit($sql,$limit,$offset);
        $userObject = kernel::single('b2c_user_object');
        foreach ($data as $key=>$value)
        {
            $data[$key]["rowno"]=$value["rowno"];
            $data[$key]["real_name"]=$value["real_name"];
            //$data[$key]["member_name"]=$value["member_id"];
            $data[$key]["member_id"]=$userObject->get_member_name(null,$value["member_id"]);
            $data[$key]["order_id"]=$value["order_id"];
            $data[$key]["final_amount"]=$value["final_amount"];
            $data[$key]["money"]=$value["money"];
            $data[$key]["brand_name"]=$value["brand_name"];
            $data[$key]["name"]=$value["name"];
            $data[$key]["received_status"]=$value["received_status"]==0?"未收货":"已收货";
        }
        return $data;
    }
    
     public function count($filter=null){
        $sql="select count(*) as count from (select  d.real_name,s.member_id,o.order_id ,o.final_amount,s.money,o.received_status,b.brand_name,b.brand_id,s.tag_id,d.member_drp_id, g.name,p.t_payed  from sdb_b2c_member_sale s
        INNER  JOIN sdb_b2c_member_drp d on s.member_id=d.member_id
        inner join sdb_b2c_orders  o on o.order_id=s.order_id
        inner join sdb_ectools_order_bills bi on bi.rel_id=o.order_id
        INNER JOIN sdb_ectools_payments p on  bi.bill_id=p.payment_id 
        inner join sdb_b2c_order_items  i on i.order_id=o.order_id
        INNER JOIN sdb_b2c_goods  g on  g.goods_id=s.goods_id  and g.goods_id=i.goods_id
        inner JOIN sdb_b2c_brand b on b.brand_id=g.brand_id  and bi.pay_object='order' and bi.bill_type='payments' and s.status=1) as sdb_b2c_member_saledetail where ".$this->_filter($filter);//判断是业务员并且有过订单佣金信息的
        $row = $this->db->select($sql);
        return intval($row[0]['count']);
    }
    
    //重新定义搜索搜索条件
//     function _filter($filter,$tableAlias=null,$baseWhere=null){
//         var_dump($filter);
//     }
}
