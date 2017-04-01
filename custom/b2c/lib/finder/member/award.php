<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class b2c_finder_member_award{    
    var $column_edit = '编辑';
    function column_edit($row){
        $return = '<a href="index.php?app=b2c&ctl=admin_member_award&act=addnew&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['member_award_id'].'" target="dialog::{title:\''.app::get('b2c')->_('编辑会员佣金奖励规则').'\', width:680, height:350}">'.app::get('b2c')->_('编辑').'</a>';
        return $return;
    }  
}
