<?php
/**
 * 库存数量以跨境通为准, 
 * ecstore执行同步.ecstore配置每天同步两次跨境通库存,
 *  读取跨境通库存数据,更新ecstore的商品库存, 同时支持手动执行同步.
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_tasks_inventory_channelQ4SBatchGet extends base_task_abstract implements base_interface_task{
  
    public function exec($params=null){
                $url="http://preapi.kjt.com/open.api";
		$post_data=$this->getOrder();
                $date=array(
                     "endpoint"=>"http://preapi.kjt.com/open.api",
                     "method"=>"Inventory.ChannelQ4SBatchGet",
                     "format"=>"json",
                     "appid"=>"seller3053",
                     "timestamp"=>date("YmdHis",time()),
                     "nonce"=>rand(100000,999999),
                     "version"=>1.0,
                     "data"=>$post_data    
                );
                $token="c0d5c21bb4a06c5bb1bd35755f08ec281699b93a362400f10d90290988707a3c";
                //签名
                //step2: 计算参数字符串&appSecret 的 hash 摘要
                //digest=md5(参数字符串&appSecret)
                //step3: 将二进制的摘要转换为 16 进制表示
                //sign=toHex(digest)，注：签名比较无需区分大小写
                $date['sign'] = bin2hex(md5($this->assemble($date)));
                //$params['sign'] = strtoupper(md5(strtoupper(md5($this->assemble($params)))));
                logger::info('signin-data:'.var_export($date,true));
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $date);
		$output = curl_exec($ch);
		curl_close($ch);
		$response_datas=json_decode($output,true);
		if(is_array($response_datas) && !empty($response_datas['Code']))
		{
                    if($response_datas['Code']==0)
                    {
                        //同步成功，修改同步状态
                        $orders = app::get('b2c')->model('orders');
			$sql='update sdb_b2c_orders set syn_status=1 where order_id='.$post_data["MerchantOrderID"];
			$orders->db->exec($sql);
                    }else{
                         //失败信息记录日志
                    $model_api = app::get('apiactionlog')->model('apilog');
                    $model_api->save($datalog);
                    logger::info('测试返回的失败信息'.$response_datas['Desc']."=======");
		}
            }
    }
    
   /*
    * 1)  参数值应为 urlencode 过后的字符串。
    * 2)  仅对接口定义中声明且请求参数列表中包含的参数（包括空值）进行签名。
    * 3)  参数值不作去除空格。
    */
      function assemble($params){
        if(!is_array($params))  return null;
        ksort($params,SORT_STRING);
        $sign = '';
        foreach($params AS $key=>$val){
            $sign .= $key . (is_array($val) ? $this->assemble($val) : urlencode($val));
        }
        return $sign;
    }
    
    //StringTo16
    function stringToHex ($s) {
        $r = "";
        $hexes = array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
        for ($i=0; $i<strlen($s); $i++) {$r .= ($hexes [(ord($s{$i}) >> 4)] . $hexes [(ord($s{$i}) & 0xf)]);}
        return $r;
    }
    //获取订单数据作为数据传输 
    public function getProducts(){
       //搜索订单数据
       $orderModel=app::get('b2c')->model('orders');
       $orderItemModel=app::get('b2c')->model('order_items');
       //"pay_status"=>'2',
       $value=$orderModel->getRow("order_id,member_id,cost_item,cost_freight,payment,ship_name,ship_tel,ship_addr,shipping_id,ship_area",array("pay_status"=>'1',"syn_status"=>'0'));
       logger::info('order:'.var_export($orders,true));
       //foreach ($orders as $key => $value) {
       $sdf_date["SaleChannelSysNo"]="2233";
       $sdf_date["MerchantOrderID"]=$value["order_id"];
       $sdf_date["WarehouseID"]=51;
       $sdf_date["IsMerchantSelfFEP"]=0;
       $sdf_date["PayInfo"]["ProductAmount"]=$value["cost_item"];
       $sdf_date["PayInfo"]["ShippingAmount"]=$value["cost_freight"];
       $sdf_date["PayInfo"]["TaxAmount"]="无";
       $sdf_date["PayInfo"]["CommissionAmount"]=0;
       $sdf_date["PayInfo"]["PayTypeSysNo"]=$this->getPayment($value["payment"]);
       $sdf_date["PayInfo"]["PaySerialNumber"]=$this->getPayno($value["order_id"]);
       //收货相关
        $area=explode(':',$value["ship_area"]);
        $country=explode('/',$area[1]);
        //根据中文名称改成编码
        $region=$this->getDlAreaById($country[2],$country[1]);
        $sdf_date["ShippingInfo"]["ReceiveName"]=$value["ship_name"];
        $sdf_date["ShippingInfo"]["ReceivePhone"]=$value["ship_tel"];
        $sdf_date["ShippingInfo"]["ReceiveAddress"]=$value["ship_addr"];
        $sdf_date["ShippingInfo"]["ReceiveAreaCode"]=$region["p_1"];
        //$sdf_date["ShippingInfo"]["ShipTypeID"]=$this->getCorp($value["order_id"]);//订单无此数据
        $sdf_date["ShippingInfo"]["ReceiveAreaName"]=str_replace("/", ",", $area[1]);
        //购买人信息
        $sdf_date["AuthenticationInfo"]=$this->menber_info($value["member_id"]);
//        $sdf_date["AuthenticationInfo"]["Name"]=$value["name"];
//        $sdf_date["AuthenticationInfo"]["IDCardType"]=$value["payment"];
//        $sdf_date["AuthenticationInfo"]["IDCardNumber"]=$value["payment"];
//        $sdf_date["AuthenticationInfo"]["PhoneNumber"]=$value["tel"];
//        $sdf_date["AuthenticationInfo"]["Email"]=$value["tel"];
//        $sdf_date["AuthenticationInfo"]["Address"]=$value["addr"];
        //订单明细
        $items=$orderItemModel->getList("product_id,nums,amount",array("order_id"=>$value["order_id"]));
        foreach ($items as $k=> $v) {
            $itemarry["ProductID"]=$v["product_id"];
            $itemarry["Quantity"]=$v["nums"];
            $itemarry["SalePrice"]=$v["amount"];
            $itemarry["TaxPrice"]=0;
        }
        $sdf_date["ItemList"]=$itemarry;
   // }
    return $sdf_date;
  }
//根据用户id获取用户信息
    private  function menber_info($member_id)
    {
        $attr = kernel::single('b2c_user_passport')->get_signup_attr($member_id);
        foreach ($attr as $key => $value) {
           if($value["attr_column"]=="contact[addr]"){ 
               $sdf_date["Address"]=$value["attr_value"];
           }
           if($value["attr_column"]=="contact[name]"){ 
               $sdf_date["Name"]=$value["attr_value"];
           }
           if($value["attr_column"]=="contact[phone][telephone]"){ 
               $sdf_date["PhoneNumber"]=$value["attr_value"];
           }
           if($value["attr_column"]=="EMAIL"){ 
               $sdf_date["Email"]=$value["attr_value"];
           }
//           if($value["attr_column"]=="cardType"){ 
//                $type=array_keys($value["attr_value"]);
//                $sdf_date["IDCardType"]=$type[0];
//           }
           if($value["attr_column"]=="sfzhm"){ 
               $sdf_date["IDCardNumber"]=$value["attr_value"];
           }
        }
        $sdf_date["IDCardType"]=0;
        return $sdf_date;
    }
    //根据订单号获取流水号
  public function  getPayno($order_id)
  {
      $sql="select  payment_id from sdb_ectools_payments p inner join sdb_ectools_order_bills bi on  bi.bill_id=p.payment_id  and  bi.rel_id='$order_id'";
      $data=kernel::database()->selectrow($sql);
      return $data["payment_id"];
  }
  
      /**
     * 得到指定region id的信息及父级的local_nameo
     * @params int region id
     * @return array
     */
    public function getDlAreaById($localname,$parentname)
    {
        $sql = "select c.region_id,c.p_1, c.local_name,c.p_region_id,c.ordernum,p.local_name as parent_name from sdb_ectools_regions as c LEFT JOIN sdb_ectools_regions as p ON p.region_id=c.p_region_id where c.local_name='$localname'  and p.local_name='$parentname'";
        return kernel::database()->selectrow($sql);
    }

 //根据订单号获取发货物流公司编号
 public function  getCorp($order_id)
  {
      $sql="select  corp_code from sdb_b2c_dlycorp dl inner join sdb_b2c_delivery di on  di.logi_id=dl.corp_id and  di.order_id='$order_id'";
      $data=kernel::database()->selectrow($sql);
      return $data["corp_code"];
  }
  

  public function getPayment($type){
      $result="";
        switch ($type)
        {
            case "wxpay":
                $result=118;
                break;
            case "alipay":
                $result=112;
                break;
            case "unionpay":
                $result=117;
                break;
            default :
                $result=0;
                break;  
        }
     return $result;
    }
    
    public function getCode($code){
      $result="";
        switch ($code)
        {
            case 0:
                $result="正确";
                break;
            case 1:
                 $result="请求参数错误";
                break;
            case 2:
                $result="签名校验错误";
                break;
            case 3:
                 $result="无 API 访问权限";
                break;
            case 4:
                 $result="IP 校验错误";
                break;
            case 5:
                 $result="访问超过限制";
                break;
            default :
                 $result="其他错误";
                break;  
        }
     return $result;
    }
    
}