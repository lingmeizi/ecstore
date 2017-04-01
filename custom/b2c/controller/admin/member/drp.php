<?php
/**
 * 会员类型（集团写手，店铺（厨师，服务员，写手））
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_member_drp extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member_profit';

    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index(){
        $custom_actions[] =  array('label'=>app::get('b2c')->_('审核'),'submit'=>'index.php?app=b2c&ctl=admin_member_drp&act=approve_members');
        $custom_actions[] =  array('label'=>app::get('b2c')->_('拒绝'),'submit'=>'index.php?app=b2c&ctl=admin_member_drp&act=reject_members');
        $actions_base['actions'] = $custom_actions;
        $actions_base['title'] = app::get('b2c')->_('经销商管理');
        $this->finder('b2c_mdl_member_drp',$actions_base);
    }
     //经销商审批
    function approve_members()
    {
        $this->begin("index.php?app=b2c&ctl=admin_member_drp&act=index");
        $obj_member = app::get('b2c')->model('member_drp');
        if($_POST['isSelectedAll'] == '_ALL_'){
            $aMember = array();
            $view_filter = $this->get_view_filter('b2c_ctl_admin_member_drp','b2c_mdl_member_drp');
            $_POST = array_merge($_POST,$view_filter);
            unset($_POST['isSelectedAll']);
            $obj_member->filter_use_like = true;
            $aData = $obj_member->getList('member_drp_id',$_POST);
            foreach((array)$aData as $key => $val){
                $aMember[] = $val['member_drp_id'];
            }
        }
        else{
            $aMember = $_POST['member_drp_id'];
        }  
        $membersdata = app::get('b2c')->model('members');
        //根据
        $aData = $obj_member->getList('member_id',array('member_drp_id|in'=>$aMember));
           foreach((array)$aData as $key => $val){
               $members[] = $val['member_id'];
           }
        //修改提交状态为已审核，是否经销商改成是
        foreach ($members as $key => $value) 
        {
            $membersdata->db->exec("update sdb_b2c_members set isjxs='1' ,  up_member_id = null     where member_id in(".$value.")");
            $obj_member->db->exec("update sdb_b2c_member_drp set appstatus = '2' where member_id in(".$value.")");
        }
        $this->end(true, app::get('b2c')->_('已完成'));
    }
    
    //拒绝成为经销
    function reject_members()
    {
        $this->begin("index.php?app=b2c&ctl=admin_member_drp&act=index");
        $obj_member = app::get('b2c')->model('member_drp');
        if($_POST['isSelectedAll'] == '_ALL_'){
            $aMember = array();
            $view_filter = $this->get_view_filter('b2c_ctl_admin_member_drp','b2c_mdl_member_drp');
            $_POST = array_merge($_POST,$view_filter);
            unset($_POST['isSelectedAll']);
            $obj_member->filter_use_like = true;
            $aData = $obj_member->getList('member_drp_id',$_POST);
            foreach((array)$aData as $key => $val){
                $aMember[] = $val['member_drp_id'];
            }
        }
        else{
            $aMember = $_POST['member_drp_id'];
        }  
        $membersdata = app::get('b2c')->model('members');
        //根据
        $aData = $obj_member->getList('member_id',array('member_drp_id|in'=>$aMember));
           foreach((array)$aData as $key => $val){
               $members[] = $val['member_id'];
           }
        //修改提交状态为已提交，是否经销商改成是
        foreach ($members as $key => $value) 
        {
            $membersdata->db->exec("update sdb_b2c_members set isjxs='0' where member_id in(".$value.")");
            $obj_member->db->exec("update sdb_b2c_member_drp set appstatus = '3' where member_id in(".$value.")");
        }
        $this->end(true, app::get('b2c')->_('已完成'));
    }
    
    //下载
    function file_download($file_url) {
	if (@$file_handle = fopen($file_url, 'r')) {        //http 协议时
		header("Content-Type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".filesize($file_url));
		header("Content-Disposition: attachment; filename=".basename($file_url));
		echo fread($file_handle, filesize($file_url));
		fclose($file_handle);
	} else if(file_exists($file_url)) {                  //file 协议时
		$file_handle = fopen($file_url, 'r');
		header("Content-Type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".filesize($file_url));
		header("Content-Disposition: attachment; filename=".basename($file_url));
		echo fread($file_handle, filesize($file_url));
		fclose($file_handle);
		
	} else {
		header("Content-Type: text/html; charset=utf-8");
		echo "<p style='color:red'>文件地址有误！</p><br/>";
		echo "<a href='javascript:window.history.go(-1)'>返回</a>";
	}
}
}
?>

   