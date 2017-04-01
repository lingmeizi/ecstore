<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class b2c_mdl_member_profit extends dbeav_model{
    var $defaultOrder = array('member_profit_id', ' ASC');
    function save(&$aData, $mustUpdate = NULL, $mustInsert = false){
        return parent::save($aData);
    }

    /**
     * @获取会员佣金规则列表信息
     * @access public
     * @param $cols 查询字段
     * @param $filter 查询过滤条件
     * @return void
     */
    public function getMProfit($cols='*', $filter=array()){
        $rows = $this->getList($cols,$filter);
        return  $rows ? $rows : array() ;
    }

    //获取所有的佣金规则信息
    public function getListAll(){
        if(!cachemgr::get('member_lv_info_all',$data)){
            cachemgr::co_start();
            $memberLvData = $this->getList('*');
            foreach($memberLvData as $row){
                $data[$row['member_profit_id']] = $row;
            }
            cachemgr::set('member_lv_info_all', $data, cachemgr::co_end());
        }
        return $data;
    }


    //判断删除
   //   function pre_recycle($data){
   //     $members = $this->app->model('members');
   //     foreach($data as $val){
   //        $aData = $members->getList('member_id',array('member_profit_id' => $val['member_profit_id']));
   //        if($aData){
   //            $this->recycle_msg = app::get('b2c')->_('该佣金规则已使用,不能删除');
   //             return false;
   //         }
   //     }
   //     return true;
   // }
    //判断佣金规则不能同时使用(添加和修改佣金规则的时候)
      function check_add($data){
        $member_profit = $this->app->model('member_profit');
        if($data["member_profit_id"])
        {
            $filter=array("member_profit_id|noequal"=>$data["member_profit_id"]);
        }
        $aData = $member_profit->getList('start_time,end_time,tag_id',$filter);
        foreach ($aData as $key => $value) {
            if($data["tag_id"]==$value["tag_id"])
            {
                if(($data['start_time']<$value['end_time']&&$data['start_time']>$value['start_time'])||($data['end_time']<$value['end_time']&&$data['starend_timet_time']>$value['start_time']))
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * 根据佣金规则名称查询佣金规则id
     * @param type $name
     * @return boolean
     */
    function is_exists($name){
        $row = $this->getList('member_profit_id',array('name' => $name));
        if(!$row) return false;
        else return true;
    }
}
