<?php
/**
 * 后台订单提现和销售订单管理
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_member_back extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member_back';

    //会员提现列表显示
    function index(){
        $actions_base['title'] = app::get('b2c')->_('佣金提现管理');
        $actions_base['use_buildin_export'] = true;//是否到处导出
        $actions_base['use_buildin_filter'] = true;//是否使用高级筛选
        $actions_base['use_view_tab'] = true;
        $actions_base['base_filter'] = array("change_type"=>0);//只查询申请的
        $this->finder('b2c_mdl_member_back',$actions_base);
    }
    
    /*
     * 佣金提现审核
     */
    function addnew($back_id=null){
        $mem_back = $this->app->model('member_back');
        $aback = $mem_back->getRow("*",array("back_id"=>$back_id));
        $aback['pay_money']=$aback["money"];
        $this->pagedata['back'] = $aback;
        $this->display('admin/member/back.html');
    }
    
      //如果是分成的话，就把当前状态改成1，修改用户佣金总额
    function audit(){
        $back_id=$_POST["back_id"];
        $this->begin();
        //查询提现或者分佣金的金额
       $memberlog  = app::get('b2c')->model('member_back')->getRow("member_id,money",array("back_id"=>$back_id));
       //查询对应用户的金额然后更新
       $member_drp=app::get('b2c')->model('member_drp');
       $user_amount  = $member_drp->getRow("user_money",array("member_id"=>$memberlog["member_id"]));
       $dif_amount=$user_amount["user_money"]-$memberlog["money"];
         
        $sdf= array(
               'user_money'   =>$dif_amount,
            );
        $logsdf=array(
               'status'   =>1,
               'pay_money'=>$_POST["pay_money"]
            );
        //修改佣金状态
        $status=app::get('b2c')->model('member_back')->update($logsdf,array("back_id"=>$back_id));
        //修改用户佣金总额
        if($member_drp->update($sdf,array('member_id' => $memberlog["member_id"]))&$status)
        {
              $this->end(true,app::get('b2c')->_('审核通过'));
        }else{
             $this->end(false,app::get('b2c')->_('审核失败'));
        }
    }

 }
