<?php
/**
 * 提现model
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class b2c_mdl_member_back extends dbeav_model{
    var $defaultOrder = array('back_id', ' desc');
    //关联dbschema
        function __construct($app){
         parent::__construct($app);
        $this->app = $app;
    }
    
     /**
     * 提现明细数据
     * @params string member id
     * @params string page number
     */
    public function fetchByback($member_id, $nPage=1, $filter_pre=array(),$limit=10)
    {
        if (!$limit)
          $limit = 10;
          $limitStart = ($nPage-1) * $limit;
         if (isset($member_id))
         {
           $filter = array('member_id' => $member_id,"change_type"=>0);
           $res =$this->getList('*',$filter, $limitStart, $limit, 'change_time DESC');
            foreach ($res as $k => $v) {
                $res[$k]['status'] = $v['status'] == 0 ?  "<font style='font-weight:bold;color:red'>等待处理</font>": "<font style='font-weight:bold'>成功</font>";
                $res[$k]['change_time'] =date('Y-m-d H:i:s', $v['change_time']);
                }
        }
        // 生成分页组建
        $countRd = $this->count($filter);
        $total = ceil($countRd/$limit);
        $current = $nPage;
        $token = '';
        $arrPager = array(
            'current' => $current,
            'total' => $total,
            'token' => $token,
        );
        $arrdata['data'] = $res;
        $arrdata['pager'] = $arrPager;
        return $arrdata;
    }
    
    //查询金额（是否包括）
    function getmoney($member,$status,$change_type)
    {
        $sql="select sum(money) as money from sdb_b2c_member_back where member_id=$member and status=$status and change_type=$change_type";
        $row=$this->db->selectrow($sql);
        return $row["money"];
    }
}
