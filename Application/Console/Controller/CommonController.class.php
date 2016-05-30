<?php
namespace Console\Controller;
use Think\Controller;
class CommonController extends Controller{
	public function __construct(){
		header("Content-Type: text/html; charset=utf-8");
		parent::__construct();
		if(session('username')){
			$this->assign('leftmenu',CONTROLLER_NAME);
			$this->assign('lml',ACTION_NAME);
		}else{
			$this->error('您还没有登录！',U('Console/Login/login'));
		}

		if(!$this->access()){			
			$this->error('您无权操作该控制器');
		}


	}
	public function tip($result,$title){
		if ($result) {
			$this->success($title.'成功！');
		}else{
			$this->error($title.'失败！');
		}
	}	


 	private function access(){
 		$page=CONTROLLER_NAME."/".ACTION_NAME; 
 
 		$t=explode(',',C('NOT_AUTH_MODULE'));
 		foreach ($t as $key => $value) {
 			if($value==$page){
 				return true;
 			}
 		}

 		$map['role_id']=session('roleidstaff');
 		if(!$map['role_id']){
 			return true;
 		} 		
 		return true;
 		// $result=D('access')->where($map)->getField('module',true);
 		// if($result){
 		// 	if(in_array($page,$result)){
 		// 		return true;
 		// 	}
 		// }
 		// return false;
 	}

}