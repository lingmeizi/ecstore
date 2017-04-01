<?php
/**
 * 后台订单提现和销售订单管理
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_member_sale extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member_sale';

    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    //会员提现列表显示
    function index(){
        $actions_base['title'] = app::get('b2c')->_('佣金明细管理');
        $actions_base['use_buildin_export'] = true;//是否到处导出
        $actions_base['use_buildin_filter'] = true;//是否使用高级筛选
        $actions_base['use_view_tab'] = true;
        $this->finder('b2c_mdl_member_sale',$actions_base);
    }
//    
//     function _views(){
//         $param1['status']=0;
//         $param2['status']=1;
//         if($_GET["member_id"])
//        {
//            $param1['member_id'] =$_GET["member_id"];
//            $param2['member_id'] =$_GET["member_id"];
//        }
//        
//        $sub_menu = array(
//            0=>array('label'=>app::get('b2c')->_('未分成订单'),'optional'=>false,'filter'=>$param1),
//            1=>array('label'=>app::get('b2c')->_('已分成订单'),'optional'=>false,'filter'=>$param2),
//        );
//        return $sub_menu;
//     }
    
    //如果是分成的话，就把当前状态改成1，修改用户佣金总额
    function audit($sale_id){
        $this->begin();
        //查询提现或者分佣金的金额
       $memberlog  = app::get('b2c')->model('member_sale')->getRow("member_id,money",array("sale_id"=>$sale_id));
       //查询对应用户的金额然后更新
       $member_drp=app::get('b2c')->model('member_drp');
       $user_amount  = $member_drp->getRow("user_money",array("member_id"=>$memberlog["member_id"]));
       $dif_amount=$user_amount["user_money"]+$memberlog["money"];
         
        $sdf= array(
               'user_money'   =>$dif_amount,
            );
        $logsdf=array(
               'status'   =>1,
            );
        //修改佣金状态
        $status=app::get('b2c')->model('member_sale')->update($logsdf,array("sale_id"=>$sale_id));
        //修改用户佣金总额
        if($member_drp->update($sdf,array('member_id' => $memberlog["member_id"]))&$status)
        {
              $this->end(true,app::get('b2c')->_('审核通过'));
        }else{
             $this->end(false,app::get('b2c')->_('审核失败'));
        }
    }
    

 }
