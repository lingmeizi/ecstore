<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class b2c_finder_member_drp{    
    
      //查看详情
        function detail_basic($drp_id){
         $render =  app::get('b2c')->render();
         //根据id信息获取用户信息
         $member =app::get('b2c')->model('member_drp')->getRow('*',array('member_drp_id'=>$drp_id));
         $oImage_attach = app::get('image')->model('image_attach');
         $arr_image_attach = $oImage_attach->getRow('*',array('target_id'=>$member["member_id"],'target_type'=>'drp'));
         $render->pagedata['image_id'] = $arr_image_attach["image_id"];
        return $render->fetch('admin/member/drpdetail.html');
    }
    
    var $column_custom= '查看会员';
    public function column_custom($row){
         $member =app::get('b2c')->model('member_drp')->getRow('*',array('member_drp_id'=>$row['member_drp_id']));
        if(empty($member))
        {
            $return="无客户";
        }else{
            $return = '<a  href="index.php?app=b2c&ctl=admin_member&act=index&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&up_member_id='.$member['member_id'] .'">'.app::get('b2c')->_('我的会员').'</a>';
        }
        return $return;
    } 
    
}
