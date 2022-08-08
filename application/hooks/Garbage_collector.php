<?php
if (!defined( 'BASEPATH')) exit('No direct script access allowed'); 

class cleaners {
	private $extensions;
	public function __construct() {
		$this->extensions = ['xlsx','xls','csv', 'doc', 'docx', 'rtf', 'pdf', 'rar', 'zip', 'txt', 'json', 'xml', 'htm', 'html', 'sql'];
	}	

	public function clean_all() {
		$config 	=& get_config();
		$filepath 	= $config['path_tmp'];
		file_exists($filepath) OR mkdir($filepath, 0755, TRUE);
		
		$this->delete_tmp($this->extensions, LOCALPATH."/$filepath");
	}

	public function delete_tmp($extensions=array(), $del_path=false) {
		if($del_path && $extensions){   
		    $dir=$del_path;
		    $segundos=300;
		    $t=time();
		    $h=opendir($dir);
		    while($file=readdir($h)){
		    	$f = explode('.',$file);
				$ext = $f[count($f)-1];	
		    	if(in_array($ext, $extensions)){
		            $path="$dir/$file";
		            if($t-filemtime($path)>$segundos)
		                @unlink($path);
		        }
		    }
		    closedir($h);
		}
	}

}