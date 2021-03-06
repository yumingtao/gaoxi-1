<?php 
namespace Console\Controller;
use Think\Controller;
class AssetController extends CommonController{
	public function index(){
		if(I('name') != ''){
			$map['name'] = array('like','%'.I('name').'%');
		}
		
		$list=D('Asset')->where($map)->select();
		$this->assign('list',$list);
		$this->display();
	}
  
        
 //添加页面       
    public function add(){
        $jumpUrl = U('Console/Asset/index');
        $asset = D('Asset');            
        if(!IS_POST){
             $this->display(); 
             exit();
        }        
        $data = $asset->create();    
        if($data){    
            $data['status']=1;
            $data['createtime']=time();
            $data['gettime']=time();
            $result = $asset->add($data);
            if($result){
                $this->success('添加成功!',$jumpUrl);
            }else{
                $this->error('添加失败!');
            }
        } else {
          $this->error($asset->getError());
        }            
    } 
    
    /*
     * 获取办公用品
     */
    public function  staffAsset(){   
        $controller = D('Asset');
        // 读取数据
        $id=I('id');
       
    	$this->assign('asset',D('Asset')->getAsset());         
    	$this->display();        
    }
   
    /*
     * 获取库存数量
     */
    public function  getStock(){   
        $controller = D('Asset');
        $asset_id=I('id'); 
        $info = D('Asset')->where('id='.$asset_id)->getField('stock');
        $this->ajaxReturn($info);   
    }
    
    public function shopping(){        
        $controller = D('Asset');
        $jumpUrl =U('Console/Asset/index');
        // 读取数据       
        $id=I('request.id');
        if(!$id){
            $this->error('数据错误');
        }   
 
        if(IS_POST){           
            $stock=I('amount');            
            $data['stock']=array('exp','stock+'.$stock);
            $data['amount']=array('exp','amount+'.$stock);
            $data['gettime']=time();
            $sul =  $controller->where('id=%d',array($id))->save($data);
            if($sul){
                $this->success('增加成功',$jumpUrl);
            }else{
                $this->error('数据错误');
            }        
            exit();
        }
        
        $data =  $controller->find($id);
        if($data) {
            $this->assign('user',$data);// 模板变量赋值
        }else{
            $this->error('数据错误');
        }        
        $this->display();        
    } 
    
    
    
    /**
     *领取用品
    **/ 
    public function StaffTake(){
        $staff_id=I('staff_id');

        $data=D('StaffTake')->StaffTake($staff_id);
        if($data['status']>0){
            $this->success('领取成功');    
        }else{
            $this->error($data['msg']);
        }        
    }

    /**
     *领取用品详情
    **/
    public function assetlist(){
        $asset_id=I('id');
        $data=D('StaffTake')->assetfind($asset_id);
        
    //查看领取用品信息       
      foreach($data as $k=>$v){
          $aslist = D('Asset')->where('id='.$v['asset_id'])->getField('name');
//          echo M()->getLastSql();exit;
          $data[$k]['asset_name'] = $aslist;
      }  
        
        $this->assign('list',$data);
        $this->display();
    }
   
    /**
    *删除设备
    **/
    public function assetdel(){
        $this->ajaxreturn();
    }

    /**
    *设备回收
    **/
    public function assetrecycle(){
        $id=I('id');
        if(!$id){
            $this->error('此信息不存在');            
        } 
        $result=D('Asset')->resycle($id);
        if($result){
            $this->success('成功回收');
        }else{
            $this->error('回收失败');            
        }
    }

    /**
    *设备扣款
    **/
    public function assetpayment($id){
        $data=D('Asset')->assetpayment($id);
        if($data){
            $this->success('已列为捐款设备');
        }else{
            $this->success('操作失败');
        }
    }
            
    public function edit(){
        $jumpUrl =U('Console/Asset/index'); 
        $model = D('Asset'); 
        $id =I('id');
        if (IS_POST) {
            if ($id > 0) {
                $data=$model->create();                
                $result=$model->where($map)->save($data);
                if ($result){
                    $this->success('修改成功', $jumpUrl);
                } else {                    
                    $this->error('修改失败');
                }          
            } 
        } else {                                    
            $data=D('asset')->where('id='.$id)->find();
            $this->assign('info',$data);
            $this->display();
        }
    }         
        
    /**
     * 查找下级分类
    **/
    public function deparnext(){
        $data=D('Asset')->deparnext();
        $this->ajaxReturn($data);
    }
	    
     //删除数据
    public function delete(){
        $id = I('id');
        $role = M('staff_take');
        $set = $role->where("id = ".$id)->find();
        $data['status']=0;
        $data = $role->where('id=%d',array($id))->save($data);
        if($data) {
            $this->success('数据删除成功！');
        }else{
            $this->error('数据删除错误！');
        }
    }
}
?>