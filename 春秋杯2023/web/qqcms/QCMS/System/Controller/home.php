<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Home extends Controllers {
	public function err_Action() {	    
	    $Url = substr($_SERVER['REQUEST_URI'], 1) ;
	    if(strpos($Url, 'Static/upload/') === false) self::err404();
	    $FileArr =  explode('/', $Url);	
	    $FileName = $FileArr[count($FileArr)-1];	
	    $Ext = end(explode('.', $FileName));	    
	    if(!in_array($Ext, explode('|', $this->SysRs['ImgViewType']))) self::err404();
	    array_splice($FileArr, -1);
	    
	    $NewPath = implode('/', $FileArr); //新的文件路径	    
	    $OldPath = str_replace('Static/upload/thumb/', 'Static/upload/', $NewPath);
	    $FileNameArr = explode('_', substr($FileName, 0, -strlen('.'.$Ext)));	  
	    if(!in_array($FileNameArr[1].'x'.$FileNameArr[2], explode('|', $this->SysRs['ThumbSize']))) self::err404();	 
	    $OriFileName = $FileNameArr[0].'.'.$Ext;

	    if(!file_exists($OldPath.'/'.$OriFileName)) self::err404(); 
	    // 文件存在，在允许范围的尺寸内，是允许的扩展名
	    if(!is_dir($NewPath)) mkdir($NewPath, 0777);
	    $Ret = $this->CommonObj->img2thumb($OldPath.'/'.$OriFileName, $NewPath.'/'.$FileName, $FileNameArr[1], $FileNameArr[2], 1);    
	    if($Ret === false) self::err404();

	    Header('Location:/'.$Url);
	    exit;
	}
	
	public function err404(){
	    header ( "HTTP/1.1 404 Not Found" );
	    echo '404 error !';
	    exit;
	}
	
	public function phpinfo_Action() {
	    var_dump($_SERVER);
		phpinfo ();
	}
	
    public function build_Action(){
        if(WEB_MODE != 'Dev') return;
        $DbConfig = DbConfig ();
	    $Pre = $DbConfig['Prefix'];
	    $PdoObj = Db_pdo::get_instance();
	    $TableArr = $PdoObj->query('show tables;', array());
	    $OtherInterface = $OtherPublic = $OtherInclude = array();
	    foreach($TableArr as $v){
	        $TableName = substr($v['Tables_in_'.$DbConfig['Name']], strlen($Pre));
	        $OtherPublic[] = 'public $'.ucfirst($TableName).'Obj;';
	        $OtherInclud[] = '$this->'.ucfirst($TableName).'Obj = '.strtoupper($Pre).ucfirst($TableName).'::get_instance();';
	        $OtherInterface[] = 'use Model\\'.strtoupper($Pre).ucfirst($TableName).';';
	        self::_b($DbConfig['Name'], $Pre, $TableName);
	    }
	    $OtherStr = implode("\n", $OtherInterface)."\n\n";
	    $OtherStr .= implode("\n", $OtherPublic)."\n\n";
	    $OtherStr .= implode("\n", $OtherInclud)."\n\n";
	    $FilePath = PATH_LIB.'Model/other.php';
	    file_put_contents($FilePath, $OtherStr);
	    echo '<span style="color:#ff0000;">Success !</span>';
	}
	
    private function _b($DbName, $Pre, $TableName){
	    $PdoObj = Db_pdo::get_instance();
	    $ClassName = strtoupper($Pre).ucfirst($TableName);
	    $Arr = $PdoObj->query('SHOW FULL COLUMNS FROM '.$Pre.$TableName, array());
	    $PrimaryKey = '';
	    $Date = date('Y-m-d');
	    foreach($Arr as $k => $v) if($v['Key'] == 'PRI') $PrimaryKey = $v['Field'];	        
	    $tmp = file_get_contents(PATH_STATIC.'tmp/lib.temp');
	    $Str = str_replace(array('{Date}', '{Table}', '{PrimaryKey}', '{ClassName}'), array($Date, $TableName, $PrimaryKey, $ClassName), $tmp);
	    $FilePath = PATH_LIB.'Model/'.$ClassName.'.php';
	    if(file_exists($FilePath)){
	        echo '<span style="color:green;">'.$FilePath.'</span><br>';
	        return;
	    }
	    echo $FilePath.'<br>';
	    file_put_contents($FilePath, $Str);
	}
}