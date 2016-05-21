<?php
namespace Console\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");

class OrderController extends CommonController {
 /**
 * 生成唯一订单号
 */
public function build_order_no()
{
    $no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    //检测是否存在
    $db = M('order_info');
    $info = $db->where(array('order_no'=>$no))->find();
    (!empty($info)) && $no = $this->build_order_no();
    return $no;
}

//管理员组类别
private function getrolename($rolename,$roleid){
    $data=D('controller');
    $name = $data->where('roleid='.$roleid)->order('id')->select();
    // dump($name);
    $this->assign($rolename,$name);

}


//设备编号获取
private function getequipment_name(){
    $data=D('equipment');
    $name = $data->where('bianhao<>""')->order('id desc')->select();
    // dump($name);
    $this->assign('equipment',$name);

}

//通过产品id获取类别id,图片地址
public function get_proid_to_typeid($id='',$typeid=''){
    $data=D('product');
    $name = $data-> field('protype,pic,pic1')->where('id='.$id)->order('id desc')->find();
    
    $pro = $name['protype'];
    $pic = $name['pic'];
    $pic1 = $name['pic1'];
    switch ($typeid) {
        case 1:
            return $pro;
            break;
        case 2:
            return $pic;
            break;
        case 3:
            return $pic1;
            break;
        
        default:
            # code...
            break;
    }    

}



//支付方式获取
private function getpayment(){
    $data=D('payment');
    $name = $data->where('payment<>""')->order('id')->select();
    // dump($name);
    $this->assign('payment',$name);

}
//处理付款状态（确认和取消）
//
public function Payment_status($id,$status){
    $order_info = D('order_info');
    $data['payment_status'] = 1; 
    if($status==0){
        $tip="确认付款成功！";
    }else{
        $tip="取消确认付款！";
    }
    $name = $order_info->where('id='.$id)->save($data);
    if($name){
        $this->success($tip);
    }else{
        $this->error('更新数据出错');
    }
}
//物流单号查询
 public function numberno($no){
    echo "物流信息查询：".$no;
    // $this->$this->display(??)
    // $this->display('plist');
 }

 //插入物流单号到订单表
 //
public function updatenumberno($id,$numberno){
$data["numberno"] = $numberno;
$data["status"] = 2;
    $order_info = D('order_info');
     $name = $order_info->where('id='.$id)->save($data);
     if($name){
        $this->success("物流单号更新成功");
    }else{
        $this->error('更新数据出错');
    }
}



//数据列表
public function Plist($name=''){
    // $this->getprotype();
    // echo "string";
    // $this->show('','utf-8');
    $map = I('');
    
    foreach( $map as $k=>$v){  
        if( !$v )  
            unset( $arr[$k] );  
    }   

    //分页跳转的时候保证查询条件
    foreach($map as $key=>$val) {
        if(!$val){
            unset($map[$key]);
        }else{
        $Page->parameter[$key]   =   urlencode($val);
          }
    }
    $User = M('order_info'); // 实例化User对象
    $count = $User->where($map)->count();// 查询满足要求的总记录数
    $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    $Page->setConfig('header','个会员');
    $show = $Page->show();// 分页显示输出
    // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    $list = $User->where($map)->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();

    $this->assign('list',$list);// 赋值数据集
    $this->assign('page',$show);// 赋值分页输出
    $this->display(); // 输出模板


}
//添加页面
public function add(){
     $this->getrolename('agent',2);
     $this->getrolename('assistant',4);
     $this->getequipment_name();
     $this->getpayment();
    $data = M('product'); // 实例化User对象
    $list = $data->where('1=1')->order('id')->select();
    $this->assign('prolist',$list);// 赋值数据集
    $this->display();
}


//插入数据
public function insert(){

    $data = I('');
    $data['order_no'] = $this->build_order_no();
    $data['addtime'] = time();
    $data['status'] = 1;
    $data['payment_status'] = 0;
    // $dataList[] = array('name'=>'thinkphp','email'=>'thinkphp@g.com');
    $roleList   =   D('order_info');
    // $set = $roleList->create($data);

    if($roleList->create()) {
        $result =   $roleList->add($data);
        if($result) {
            foreach( $data['newslist'] as $k=>$v){  
                $dataList[] = array(
                'proid'=>$data["id".$v],
                'protype'=>$data["protype".$v],
                'pic'=>$data["pic".$v],
                'pic1'=>$data["pics".$v],
                'product'=>$data["product".$v],
                'price2'=>$data["price".$v],
                'buynum'=>$data["text_box".$v],
                'discount'=>$data["zhekou".$v],
                'tollsprice'=>$data["total".$v],
                'order_no'=>$data["order_no"],
                'addtime'=>time(),                
                );
            }
// dump($dataList);
// exit;
            $user=M('order_goods');
            $user->addAll($dataList);
            // $sset = $User->addAll($dataList);

            $this->success('订单表数据添加成功！');
        }else{
            $this->error('订单商表数据添加错误！');
        }

    }else{
        $this->error($roleList->getError());
    }
   
}



//编辑
public function edit($id){
    // $this->getprotype();
     $this->getrolename('agent',2);
     $this->getrolename('assistant',4);
     $this->getequipment_name();
     $this->getpayment();
     $user = M('order_info');
     $orderinfolist = $user->where('id='.$id)->order('id')->find();

     $this->assign('info',$orderinfolist);// 赋值数据集
     $data = M('order_goods'); // 实例化User对象
     $list = $data->where("order_no='".$orderinfolist['order_no']."'")->order('id')->select();

     $this->assign('prolist',$list);// 赋值数据集
    // $this->display();
    $controller   =   M('order_info');
    // 读取数据
    $data =   $controller->find($id);
    if($data) {
        $this->assign('data',$data);// 模板变量赋值
    }else{
        $this->error('数据错误');
    }
    $this->display();

}

//更新数据
public function update(){
$roleList   =   D('order_info');
if($roleList->create()) {
    $result = $roleList->save();
    if($result) {
        $this->success('操作成功！');
    }else{
        $this->error('写入错误！');
    }
}else{
    $this->error($roleList->getError());
}
}

//删除数据
public function delete(){
$id=i('id');
    // dump($id);
    // exit;
    $role = M('order_info');
     $set = $role->where("id = ".$id)->find();
     // $set['oldid'] = $id;
     // unset($set['id']);
     // $set['addtime']=strtotime($set['addtime']);
     $set['price1']= floatval($set['price1']);
     $set['price2']= floatval($set['price2']);
     $set['price3']= floatval($set['price3']);
     // dump($set);
     // exit;
    D('product_copy')->add($set);
    // M('Equipment_copy')->add($set);

    $result = $role->delete($id);

     if($result) {
            $this->success('数据删除成功！');
        }else{
            $this->error('数据删除错误！');
        }
    // $this->display();
}

protected function top(){
    // $this->display();
}

private function hello3(){
    echo '这是private方法!';
}
}
