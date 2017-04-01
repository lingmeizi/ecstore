<?php
/**
 * 分销商销售业绩报表
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_member_salereport extends desktop_controller{

    var $workground = 'b2c_ctl_admin_member_salereport';

    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

//    //会员提现列表显示
    function index(){
        $actions_base['title'] = app::get('b2c')->_('销售业绩报表');
        $actions_base['use_buildin_export'] = true;//是否到处导出
        $actions_base['use_buildin_filter'] = true;//是否使用高级筛选
        $actions_base['use_view_tab'] = true;
        $actions_base['use_buildin_recycle'] = false;
        $this->finder('b2c_mdl_member_salereport',$actions_base);
    }
 }
