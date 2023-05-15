<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Install extends ControllersInstall {
    
    public function index_Action(){
        $ConfIniPath = PATH_LIB.'Config/Config.ini';
        if(file_exists($ConfIniPath)){
            echo '系统已经安装, 请直接 <a href="'.$this->CommonObj->Url(array('index', 'admin')).'">登录</a> ！<br> 如需要重新安装，请删除文件 "Lib/Config/Config.ini" ';exit;
        }
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Host', 'Name', 'Accounts', 'Password', 'Port', 'Phone', 'RegPassword'))) $this->Err(1001);
            if(!$this->VeriObj->VeriMobile(trim($_POST['Phone']))) $this->Err(1048);
            if(trim($_POST['RegPassword']) != trim($_POST['RegPassword2'])) $this->Err(1048);
            try{
                $ConnRet   = new PDO ( 'mysql:dbname=' . $_POST['Name'] . ';host=' . $_POST['Host'] . ';port='.$_POST['Port'], $_POST['Accounts'], $_POST['Password'],
                    array (PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            }catch (PDOException $e){
                $Msg = iconv('gbk', 'utf-8', $e->getMessage());
                $this->Err(1000, $Msg);
            }
            $Data = array(
                'DbConfig' => array(
                    'Host' => $_POST['Host'],
                    'Name' => $_POST['Name'],
                    'Accounts' => $_POST['Accounts'],
                    'Password' => $_POST['Password'],
                    'Port' => $_POST['Port'],
                    'Prefix' => 'qc_',
                ),
                'RedisConfig' => array(
                    'IsOpen' => '2',
                    'Host' => '',
                    'Password' => '',
                    'Port' => '',
                )
            );
            $Ret = $this->CommonObj->writeIni($ConfIniPath, $Data, true);
            if($Ret === false) $this->Err(1002);
            $DbConf = Config::DbConfig();
            $this->SysObj = Model\QC_Sys::get_instance();
            $this->UserObj = Model\QC_User::get_instance();
            $Ts = time();
            
            try{
                DB::$s_db_obj->beginTransaction();                
                $Path = PATH_DIRNAME.'/Database/qcms.sql';
                if(!file_exists($Path)) $this->Err(1035);
                $this->SysObj->ImportSql($Path);        
                $this->UserObj->ExecDelete();
                $this->UserObj->SetInsert(array(                    
                    'Phone' => $_POST['Phone'],
                    'NickName' => '管理员',
                    'Head' => '',
                    'Password' => md5(trim($_POST['RegPassword'])),
                    'GroupAdminId' => 1,
                    'GroupUserId' => 1,
                    'IsAdmin' => 1,
                    'State' => 1,
                    'TsAdd' => $Ts,
                    'IpAdd' => $this->CommonObj->ip(),
                ))->ExecInsert();  
                $this->SysObj->SetCond(array('Name' => 'Version'))->SetUpdate(array('AttrValue' => VERSION))->ExecUpdate();
                DB::$s_db_obj->commit();
            }catch (PDOException $e){
                DB::$s_db_obj->rollBack();
                unlink($ConfIniPath);
                $this->Err(1000, $e->getMessage());
            }
            $this->Jump(array('index', 'admin'), 1888);
        }
        $tmp['Step1'] = array(
            array('Name' =>'Host', 'Desc' => '数据库IP',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'Name', 'Desc' => '数据库名',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'Accounts', 'Desc' => '账号',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'Password', 'Desc' => '密码',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'Port', 'Desc' => '端口',  'Type' => 'input', 'Value' => '3306', 'Required' => 1, 'Col' => 12),
        );
        $tmp['Step2'] = array(
            array('Name' =>'Phone', 'Desc' => '手机号',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'RegPassword', 'Desc' => '密码',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'RegPassword2', 'Desc' => '确认密码',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
        );
        $this->LoadView('install/index', $tmp);
    }
    
    public function checkDb_Action(){
        if(!$this->VeriObj->VeriPara($_POST, array('Host', 'Name', 'Accounts', 'Password', 'Port'))) $this->ApiErr(1001);
        try{
            $ConnRet   = new PDO ( 'mysql:dbname=' . $_POST['Name'] . ';host=' . $_POST['Host'] . ';port='.$_POST['Port'], $_POST['Accounts'], $_POST['Password'],
                array (PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }catch (PDOException $e){
            $Msg = iconv('gbk', 'utf-8', $e->getMessage());
            $this->ApiErr(1000, $Msg);
        }
        $this->ApiSuccess();
    }
    
    public function checkPermission_Action(){ //检测环境        
        $TempArr = $this->CommonObj->readAll(substr(PATH_TEMPLATE, 0, -1) );
        $TempArr[] = PATH_STATIC.'upload';
        $TempArr[] = PATH_STATIC.'backups';
        $TempArr[] = PATH_LIB.'Config';
        /* var_dump(PATH_STATIC.'upload');
        var_dump($TempArr);exit; */
        //$TempArr[] = 
        $RetArr = array();        
        $IsOK = 1;
        $len = strlen(PATH_DIRNAME)+1;
        foreach($TempArr as $k => $v){
            if(is_writeable($v)){
                $RetArr[] = array('Path' => substr($v, $len) , 'IsWriteAble' => 1);
            }else{
                $IsOK = 2;
                $RetArr[] = array('Path' => substr($v, $len), 'IsWriteAble' => 2);
            }
        }
        $Result = array('IsOk' => $IsOK, 'FileArr' => $RetArr);
        $this->ApiSuccess($Result);
    }
    
    public function checkExtend_Action(){ //检查扩展
        $Arr = array(
            'curl', 'gd', 'mbstring', 'pdo_mysql', 'iconv', 'date', 'hash', 'json', 'session', 'zip', 'PDO', 'SimpleXML' //'redis',
        );
        $Extensions = get_loaded_extensions();
        $IsOK = 1;
        $RetArr = array();
        foreach($Arr as $v){
            if(in_array($v, $Extensions)){
                $RetArr[] = array('Ext' => $v , 'IsInstall' => 1);
            }else{
                $IsOK = 2;
                $RetArr[] = array('Ext' => $v, 'IsInstall' => 2);
            }
        }
        $Result = array('IsOk' => $IsOK, 'ExtArr' => $RetArr);
        $this->ApiSuccess($Result);
    }
    
    public function auth_Action() {
        echo '<br><br><br><h1><center>QCMS PHP Version '.parent::SYS_VERSION.' </center></h1><center><h2>Author : Qesy, Email : 762264@qq.com</h2><p>Your IP : ' . $this->CommonObj->ip () . '</p></center>';
    }
    
}