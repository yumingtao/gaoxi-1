<?php
namespace Console\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");

class ShipmentsController extends CommonController {
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
		$data=D('staff');
		$name = $data->where('quarters='.$roleid)->order('id')->select();
	   
		$this->assign($rolename,$name);

	}

	//设备编号获取
	public function getEquipment(){

	   $id=I('id');
		if(!$id){
			return 0;
		}    
		$data=D('equipment');
		$result = $data->field('id,xinghao,bianhao')->where('staffid=%d',array($id))->order('id desc')->select();
		if(!$result)
		{
			return 0;
		}
		$this->ajaxReturn($result);
	}
	//设备编号获取
	private function getequipment_name(){
		$data=D('equipment');
		$name = $data->where('bianhao<>""')->order('id desc')->select();

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
		$username = I('username');

		if($username){
			$map['order_no']  = array('like','%'.trim($username).'%'); 
		}

		$user_id = session("userid");
		if($user_id > 0 ){
			$where['id']=session("userid");
			$where['accounts']=session("username");
			$count=M('controller')->where($where)->count();
			if($count<=0){
				$user=D('Staff')->getthislevel();
				$map['agent']  = array('in',implode(',',$user));
			}
	    }
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


		$map['status']=1;
		$map['payment_status']=1;

	    
		
		$count = $User->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出

	  

		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($map)->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();	
	
		$staff=D('staff')->getfield('id,name',true);
		$catdata=D('Category')->categoryone();      
		
		$this->assign('staff',$staff);
		$this->assign('cat',$catdata);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}

	//忆发货订单
	public function ylist($name=''){
		$username = I('username');

		if($username){
			$map['order_no']  = array('like','%'.trim($username).'%'); 
		}

		$user_id = session("userid");
		if($user_id > 0 ){
			$where['id']=session("userid");
			$where['accounts']=session("username");
			$count=M('controller')->where($where)->count();
			if($count<=0){
				$user=D('Staff')->getthislevel();
				$map['agent']  = array('in',implode(',',$user));
			}
	    }
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
		$map['status']=2;

		$count = $User->where($map)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出

	  

		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($map)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();	
	
		$staff=D('staff')->getfield('id,name',true);
		$catdata=D('Category')->categoryone();      
		
		$this->assign('staff',$staff);
		$this->assign('cat',$catdata);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板

	}

	//提交订单
	public function add(){
            $map['id']=session('userid');
            $map['accounts']=session('username');            
            $count=M('controller')->where($map)->count();            
            if($count<=0){                 
                $userdata=D('Staff')->getthislevel();
                $where['id']=array('in',implode(',',$userdata));
            }      
            $where['quarters']=array('in',"38,39,67");
            $where['iswork']=array('neq',4);            
            $staff=D('Staff')->where($where)->getField('id,name',true);
   
            
//            
//            $this->getrolename('agent',68);
//            $this->getrolename('assistant', 67);
           // $this->getequipment_name();
            $this->getpayment();
		$data = M('product'); // 实例化User对象
		$list = $data->where('1=1')->order('id')->select();  
	   $this->assign('staff', $staff);
		$this->assign('prolist',$list);// 赋值数据集 
		$this->display();
	}



	//查看页面
    public function look(){
        $id=I('get.id');        
        $model = D('order_info');      
        if(empty($id)){            
            $this->error('请选择查看订单');
        } 
		$info = $model->where("id=%d",array($id))->find(); 
		$Equipment = D('Equipment')->where('staffid='.$info['agent'])->select();
		$payment=D('payment')->where('')->getfield('id,payment',true);
		$staff=D('staff')->getfield('id,name',true);
		$products=$this->GetProdect($info['order_no']);
		$this->assign('products',$products);             
		
		$this->assign('department',D('Category')->department());
		$this->assign('quarters',$quarters);
		$this->assign('subordinates',$subordinates);
		$this->assign('Equipment',$Equipment);
		$this->assign('order_nums',$order_nums);
		$this->assign('payment',$payment);
		$this->assign('staff',$staff);
		$this->assign('info',$info);
		$this->assign('id',$id);
		$this->display();

    }
    public function printlook(){
         $id=I('get.id');        
        $model = D('order_info');      
        if(empty($id)){            
            $this->error('请选择查看订单');
        } 
		$info = $model->where("id=%d",array($id))->find(); 
		$Equipment = D('Equipment')->where('staffid='.$info['agent'])->select();
		$payment=D('payment')->where('')->getfield('id,payment',true);
		$staff=D('staff')->getfield('id,name',true);
		
		$products=$this->GetProdect($info['order_no']);
		$this->assign('products',$products);
		
		$this->assign('department',D('Category')->department());
		$this->assign('quarters',$quarters);
		$this->assign('subordinates',$subordinates);
		$this->assign('Equipment',$Equipment);
		$this->assign('order_nums',$order_nums);
		$this->assign('payment',$payment);
		$this->assign('staff',$staff);
		$this->assign('info',$info);
		$this->assign('id',$id);
		$this->display();
    }
    //根据订单号查找产品
    private function GetProdect($order_no){
		$datagoods = D('order_goods');
		$products = $datagoods-> field('product,price2,buynum')->where('order_no="'.$order_no.'"')->order('id desc')->select();  
		return $products;            
    }

	




	//查找下级经济人 ajax请求
	public function getagent(){
		$id=I('id');
		if(empty($id)){        
			$this->ajaxreturn(0);
		}     
		$data=D('staff')->where('nibs=%d',array($id))->getfield('id,name',true);
		if($data){
			$this->ajaxreturn($data);    
		}else{
			$this->ajaxreturn(0);
		}
		
	}


	//print打印页面
	public function weixin_orders_print() {
		$id = I ( 'id' );
		$this->assign ( 'id', $id );
		
		$model = new WeixinApipayOrdersModel ();
		$orderArray = $model->get_info_byid ( $id );
		
		// 设置报表标题
		$orderArray ['reportTitle'] = '这是用户基本表';
		
		// 报表中要得到的数据格式
		$xmlReportData = get_reports_xml_byarray ( $orderArray );
		$this->assign ( 'xmlReportData', $xmlReportData );
		// 要打印的报表文件
		$reportName = 'kdd_shentong.grf';
		$this->assign ( 'reportName', $reportName );
		// put_log($xmlReportData);
		
		
		$this->display ( 'Weixin:weixin_orders_print' );// 显示模板
	}
	

}
