<?php
/**
 * 会员类型（集团写手，店铺（厨师，服务员，写手））
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_member_profit extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member_profit';

    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index(){

        $this->finder('b2c_mdl_member_profit',array(
            'title'=>app::get('b2c')->_('会员佣金设置'),
            'actions'=>array(
                         array('label'=>app::get('b2c')->_('添加佣金规则'),'href'=>'index.php?app=b2c&ctl=admin_member_profit&act=addnew','target'=>'dialog::{width:680,height:350,title:\''.app::get('b2c')->_('添加佣金规则').'\'}'),
                        )
            ));
    }

    /*
     * 添加佣金规则
     */
    function addnew($member_profit_id=null){
            // if($member_profit_id!=null)
            // {
                $model_tag=app::get('desktop')->model('tag');
                $tag=$model_tag->getlist('tag_id,tag_name',array('tag_type'=>'goods'),0,-1);
                // echo var_dump($tag);
                $mem_profit = $this->app->model('member_profit');
                $aProfit = $mem_profit->getRow("*",array("member_profit_id"=>$member_profit_id));
                $this->pagedata['profit'] = $aProfit;
                $this->pagedata['tag']=$tag;
            // }

            $this->display('admin/member/profit.html');
    }

    function save(){
        $this->begin();
        $objMemProfit = $this->app->model('member_profit');
        if($_POST['member_profit_id']){
            $olddata = app::get('b2c')->model('member_profit')->dump($_POST['member_profit_id']);
        }
        $dtime = $_POST['_DTIME_'];
        $_POST['start_time'] = $_POST['start_time'] . ' ' . $dtime['H']['start_time'] . ':' . $dtime['M']['start_time'];
        $_POST['start_time'] = strtotime($_POST['start_time']);
        $_POST['end_time'] = $_POST['end_time'] . ' ' . $dtime['H']['end_time'] . ':' . $dtime['M']['end_time'];
        $_POST['end_time'] = strtotime($_POST['end_time']);
        if($_POST['end_time']<$_POST['start_time'])
        {
             $this->end(false,app::get('b2c')->_('失效时间必须大于生效时间'));
        }
        if($objMemProfit->check_add($_POST))
        {
             $this->end(false,app::get('b2c')->_('该商品标签的佣金规则有冲突，请重新设置'));
        }
        #↑↑↑↑↑start_time↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        if($objMemProfit->save($_POST)){
            #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
            if($obj_operatorlogs = kernel::service('operatorlog.members')){
                if(method_exists($obj_operatorlogs,'member_profit_log')){
                    $newdata = app::get('b2c')->model('member_profit')->dump($_POST['member_profit_id']);
                    $obj_operatorlogs->member_type_log($newdata,$olddata);
                }
            }
            #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
            $this->end(true,app::get('b2c')->_('保存成功'));
        }else{
            $this->end(false,app::get('b2c')->_('保存失败'));
       }
    }
        function setdefault($profit_id){
        $this->begin();
        $objMemProfit = $this->app->model('member_profit');
        $difault_profit = $objMemProfit->getRow("*",array('default_profit'=>'true'));//获取默认佣金规则
        if(isset($difault_profit)){
            $result1 = $objMemProfit->update(array('default_profit'=>0),array('member_profit_id'=>$difault_profit['member_profit_id']));
            if($result1){
                $result = $objMemProfit->update(array('default_profit'=>1),array('member_profit_id'=>$profit_id));
                $msg = app::get('b2c')->_('默认佣金规则设置成功');
            }else{
                $msg = app::get('b2c')->_('默认佣金规则设置失败');
            }
        }
        else{
            $result = $objMemProfit->update(array('default_profit'=>1),array('member_profit_id'=>$profit_id));
            $msg = app::get('b2c')->_('默认佣金规则设置成功');
        }
        $this->end($result,$msg);

    }
    //配置经销商须知
    public function license(){
        if($_POST['license']){
            $this->begin();
            app::get('b2c')->setConf('b2c.register.setting_memberlicense',$_POST['license']);
            $this->end(true, app::get('wap')->_('当前配置修改成功！'));
        }
        $this->pagedata['license'] = app::get('b2c')->getConf('b2c.register.setting_memberlicense');
        $this->page('admin/member/license.html');
    }
}
