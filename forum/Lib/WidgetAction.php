<?php
class WidgetAction extends YouYaX
{
    public function setActive()
    {
	  	if($_SESSION['token']!=getparam("token")){
	  		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
	  	}else{
	  		$_SESSION['token']='';	
	  	}
  	  if (!empty($_SESSION['youyax_admin'])) {
        $class_name = getparam("name");
        $data       = $this->find(C('db_prefix') . "plugin", "string", "name='" . $class_name . "'");
        if (empty($data)) {
            $dat['name']   = $class_name;
            $dat['status'] = 1;
            $this->add($dat, C('db_prefix') . "plugin");
        } else {
            $dat['status'] = 1;
            $this->save($dat, C('db_prefix') . "plugin", "name='" . $class_name . "'");
        }
        if (method_exists(w($class_name), 'install')) {
            w($class_name)->install();
        }
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "plugin_view" . C('static_url'));
      }
    }
    public function setUnActive()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
    	if (!empty($_SESSION['youyax_admin'])) {
        $class_name    = getparam("name");
        $dat['status'] = 0;
        $this->save($dat, C('db_prefix') . "plugin", "name='" . $class_name . "'");
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "plugin_view" . C('static_url'));
      }
    }
    public function postAction()
    {
        w($_POST['name'])->$_POST['method']($_POST);
    }
    public function getAction()
    {
        $class_name  = getparam("name");
        $method_name = getparam("method");
        w($class_name)->$method_name($this->array_url);
    }
}
?>