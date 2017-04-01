<?php
/**
 * 分销商销售业绩报表
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class b2c_mdl_member_salereport extends dbeav_model{

        function __construct($app){
         parent::__construct($app);
        $this->app = $app;
        $this->columns = array(
                        'rowno'=>array('label'=>app::get('b2c')->_('序号'),'width'=>200),
                        'real_name'=>array('label'=>app::get('b2c')->_('分销商'),'width'=>200),
                        'brand_name'=>array('label'=>app::get('b2c')->_('商品品牌'),'width'=>100),
                        'name'=>array('label'=>app::get('b2c')->_('商品'),'width'=>100),
                        'nums'=>array('label'=>app::get('b2c')->_('销售量'),'width'=>100),
                        'amount'=>array('label'=>app::get('b2c')->_('销售额'),'width'=>100, 'type'=>'money'),
                        't_payed'=>array('label'=>app::get('b2c')->_('付款时间'),'filtertype' => 'bool','filterdefault' => 'true','width'=>100,'type'=>'time'),
                        'member_drp_id'=>array('label'=>app::get('b2c')->_('分销商'),'filtertype' => 'normal','filterdefault' => 'true','width'=>200,'type' => 'table:member_drp', 'sdfpath' => 'member_drp/member_drp_id','in_list' => false),
                        'brand_id'=>array('label'=>app::get('b2c')->_('商品品牌'),'filtertype' => 'normal','filterdefault' => 'true','width'=>200,'type' => 'table:brand', 'sdfpath' => 'brand/brand_id','in_list' => false),
                        'tag_id'=>array('label'=>app::get('b2c')->_('商品标签'),'filtertype' => 'normal','filterdefault' => 'true','width'=>200,'type' => 'table:tag@desktop','sdfpath' => 'tag/tag_id','in_list' => false),
                   );
        $this->list = array(
                 'rowno'=>array('label'=>app::get('b2c')->_('序号'),'width'=>200),
                'real_name'=>array('label'=>app::get('b2c')->_('分销商'),'width'=>200),
                'brand_name'=>array('label'=>app::get('b2c')->_('商品品牌'),'width'=>100),
                'name'=>array('label'=>app::get('b2c')->_('商品'),'width'=>100),
                'nums'=>array('label'=>app::get('b2c')->_('销售量'),'width'=>100),
                'amount'=>array('label'=>app::get('b2c')->_('销售额'),'width'=>100, 'type'=>'money'),
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
//          $sql="select real_name, sum(amount) as amount, sum(nums) as nums, brand_name,name from 
//        (select d.real_name, m.member_id,  i.amount, i.nums, b.brand_name,g.name,t.tag_name,p.t_payed,b.brand_id,t.tag_id,d.member_drp_id  from sdb_b2c_members m
//        INNER JOIN sdb_b2c_member_order_info oi on m.member_id=oi.member_id
//        INNER  JOIN sdb_b2c_member_drp d on m.member_id=d.member_id
//        inner join sdb_b2c_orders  o on o.order_id=oi.order_id
//        inner join sdb_b2c_order_items  i on i.order_id=o.order_id
//        INNER JOIN sdb_b2c_goods  g on  g.goods_id=i.goods_id 
//        INNER JOIN sdb_b2c_brand b on b.brand_id=g.brand_id  
//        inner join sdb_ectools_order_bills bi on bi.rel_id=o.order_id
//        INNER JOIN sdb_ectools_payments p on  bi.bill_id=p.payment_id 
//        INNER JOIN sdb_desktop_tag_rel  r on  r.rel_id=g.goods_id 
//        INNER JOIN sdb_desktop_tag  t on  t.tag_id=r.tag_id ) as sdb_b2c_member_salereport where ".$this->_filter($filter)." 
//        GROUP BY brand_name,name,member_id order by  member_id ";//判断是业务员并且有过订单佣金信息的
        $sql="select  (@rowNO := @rowNo+1) AS rowno,real_name, brand_name,name,sum(nums) as nums,sum(amount) as amount from 
        (select d.real_name, s.member_id,  i.amount, i.nums, b.brand_name,g.name,b.brand_id,s.tag_id,p.t_payed,d.member_drp_id from sdb_b2c_member_sale s
        INNER  JOIN sdb_b2c_member_drp d on s.member_id=d.member_id
        inner join sdb_b2c_order_items  i on i.order_id=s.order_id
        INNER JOIN sdb_b2c_goods  g on  g.goods_id=s.goods_id  and g.goods_id=i.goods_id
        INNER JOIN sdb_b2c_brand b on b.brand_id=g.brand_id  and s.status=1
        inner join sdb_ectools_order_bills bi on bi.rel_id=s.order_id
        INNER JOIN sdb_ectools_payments p on  bi.bill_id=p.payment_id  ) as sdb_b2c_member_salereport ,(select @rowNo:=0) as it where ".$this->_filter($filter)." 
        GROUP BY brand_name,name,member_id order by  rowno ";//判断是业务员并且有过订单佣金信息的
        
        $data=$this->db->selectLimit($sql,$limit,$offset);
        $userObject = kernel::single('b2c_user_object');
        foreach ($data as $key=>$value)
        {
             $data[$key]["rowno"]=$value["rowno"];
            $data[$key]["real_name"]=$value["real_name"];
            $data[$key]["brand_name"]=$value["brand_name"];
            $data[$key]["name"]=$value["name"];
            $data[$key]["nums"]=$value["nums"];
            $data[$key]["amount"]=$value["amount"];
        }
        return $data;
    }
    
    public function count($filter=null){
        $sql="select real_name, sum(amount) as amount, sum(nums) as nums, brand_name,name from 
        (select d.real_name, s.member_id,  i.amount, i.nums, b.brand_name,g.name,b.brand_id,s.tag_id,p.t_payed,d.member_drp_id from sdb_b2c_member_sale s
        INNER  JOIN sdb_b2c_member_drp d on s.member_id=d.member_id
        inner join sdb_b2c_order_items  i on i.order_id=s.order_id
        INNER JOIN sdb_b2c_goods  g on  g.goods_id=s.goods_id  and g.goods_id=i.goods_id
        INNER JOIN sdb_b2c_brand b on b.brand_id=g.brand_id  and s.status=1
        inner join sdb_ectools_order_bills bi on bi.rel_id=s.order_id
        INNER JOIN sdb_ectools_payments p on  bi.bill_id=p.payment_id  ) as sdb_b2c_member_salereport where ".$this->_filter($filter)." 
        GROUP BY brand_name,name,member_id order by  member_id ";//判断是业务员并且有过订单佣金信息的
        $row = $this->db->select($sql);
        return intval(sizeof($row));
    }
}
