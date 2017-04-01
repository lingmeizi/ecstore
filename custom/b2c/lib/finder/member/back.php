<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class b2c_finder_member_back{   


    //添加提现通过按钮
    var $column_audit= '审核';
    public function column_audit($row){
         $member =app::get('b2c')->model('member_back')->getRow('*',array('back_id'=>$row['back_id']));
        if($member["status"]==1)
        {
            $return="审核通过";
        }else{
        $return = '<a href="index.php?app=b2c&ctl=admin_member_back&act=addnew&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['back_id'].'" target="dialog::{title:\''.app::get('b2c')->_('审核会员提现').'\', width:350, height:150}">'.app::get('b2c')->_('审核').'</a>';
       // $target = '{onComplete:function(){if (finderGroup&&finderGroup[\'' . $_GET['_finder']['finder_id'] . '\']) finderGroup[\'' . $_GET['_finder']['finder_id'] . '\'].refresh();}}';
        //$return = '<a target="'.$target.'" href="index.php?app=b2c&ctl=admin_member_back&act=audit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['back_id'] .'">'.app::get('b2c')->_('审核').'</a>';
        }
        return $return;
    }  
    
}
