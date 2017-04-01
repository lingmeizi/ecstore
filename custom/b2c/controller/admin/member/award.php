<?php
/**
 * 会员类型（集团写手，店铺（厨师，服务员，写手））
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_member_award extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member_award';

    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index(){

        $this->finder('b2c_mdl_member_award',array(
            'title'=>app::get('b2c')->_('会员佣金奖励设置'),
            'actions'=>array(
                         array('label'=>app::get('b2c')->_('添加佣金奖励规则'),'href'=>'index.php?app=b2c&ctl=admin_member_award&act=addnew','target'=>'dialog::{width:680,height:350,title:\''.app::get('b2c')->_('添加佣金奖励规则').'\'}'),
                        )
            ));
    }

    function addnew($member_award_id=null){
        $mem_award = $this->app->model('member_award');
        $aAward = $mem_award->getRow("*",array("member_award_id"=>$member_award_id));
        $this->pagedata['award'] = $aAward;
       $this->display('admin/member/award.html');
    }

    
    function save(){
        $this->begin();
        $mem_award = $this->app->model('member_award');
        if($_POST['member_award_id']){
            $olddata = app::get('b2c')->model('member_award')->dump($_POST['member_award_id']);
        }
        if($mem_award->save($_POST)){
            $this->end(true,app::get('b2c')->_('保存成功'));
        }else{
            $this->end(false,app::get('b2c')->_('保存失败'));
       }
    }
}
