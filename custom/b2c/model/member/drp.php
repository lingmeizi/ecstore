<?php
/**
 * 经销商信息
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class b2c_mdl_member_drp extends dbeav_model{
    var $defaultOrder = array('member_drp_id', ' ASC');
    //关联dbschema
        function __construct($app){
         parent::__construct($app);
        $this->app = $app;
    }   
    
   
    //获取经销商审i批状态
    function getStatus($appstatus)
    {
        $result="未提交";
        switch ($appstatus) {
            case 1:
                $result='已提交';
                break;
           case 2:
                $result='已审核';
                break;
            case 3:
                $result='已拒绝';
                break;
            default:
                break;
        }
        return $result;
    }
}
