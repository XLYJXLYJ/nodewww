<?php
class CacheDelWidget extends YouYaX
{
	//用于后台查看插件信息，必填函数,函数名不能随意
	public function desc(){
		$this->version="1.0";
		$this->author="youyax";
		$this->description="此插件用来清空缓存文件夹中的文件，激活后，在缓存容量信息栏会出现‘清空操作’";
		return $this;
	}
	public function show(){
		return '<a style="color:#222;font-size:12px;font-family:Tahoma;font-weight:bold" href="'.$this->youyax_url.'/Widget'.C('default_url').'getAction'.C('default_url').'name'.C('default_url').'CacheDelWidget'.C('default_url').'method'.C('default_url').'clear_cache'.C('static_url').'" onclick="return confirm(\'您确定要清空?\');">( 清空缓存 )</a>';
	}
	public function clear_cache(){
		if (empty($_SESSION['youyax_admin'])) {
            exit;
        }
		$directory="./cache";
			if(is_dir($directory)){
				$dir=opendir($directory);
					while($filename=readdir($dir)){
					   if($filename!="." && $filename!=".."){
					    if(is_file($directory."/".$filename)){
					     	@unlink($directory."/".$filename);
					     }
					   }
					}
				@closedir($dir);
			}
		echo "<script>window.parent.location.href='" . $this->youyax_url . "/admin" . C('default_url') . "index" . C('static_url')  . "';</script>";
	}
}
?>