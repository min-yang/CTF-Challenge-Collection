<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Index extends Controllers {

    const CurrentClient = 'Web';

    public function index_Action(){
        echo $this->tempRun('index');
        self::_statFlow();
    }

    public function cate_Action($CateId = 0){
        if(empty($CateId)) $this->DieErr(1001);
        echo $this->tempRun('cate', $CateId);
        self::_statFlow();
    }

    public function detail_Action($Id = 0){
        if(empty($Id)) $this->DieErr(1001);
        echo $this->tempRun('detail', $Id);        
        $this->TableObj->SetTbName('table_'.$this->Tmp['ModelRs']['KeyName'])->SetCond(array('Id' => $Id))->SetUpdate(array('ReadNum' => ($this->Tmp['TableRs']['ReadNum']+1)))->ExecUpdate();        
        self::_statFlow();
    }

    public function page_Action($PageId = 0){
        if(empty($PageId)) $this->DieErr(1001);
        echo $this->tempRun('page', $PageId);
        self::_statFlow();
    }

    public function search_Action(){
        echo $this->tempRun('search');
        self::_statFlow();
    }

    public function down_Action($Id = 0){
        if(empty($Id)) $this->DieErr(1001);
        $TableRs = $this->TableObj->SetCond(array('Id' => $Id))->ExecSelectOne();
        if(empty($TableRs)) $this->DieErr(1001);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        $TableRs = $this->Sys_modelObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $TableRs['Id']))->ExecSelectOne();

        //if(!empty($TableRs) && $ModelRs['KeyName'] == 'down'){
        //无论什么模型都可以做下载，并统计
        $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $Id))->SetUpdate(array('DownNum' => ($TableRs['DownNum']+1)))->ExecUpdate();
        //}
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: '.$TableRs['Address']);
    }

    public function muLogin_Action(){
        if($this->SysRs['MultistationIsOpen'] != 1) $this->Err(1055);
        if(!$this->VeriObj->VeriPara($_GET, array('Secret'))) $this->Err(1001);
        if(trim($_GET['Secret']) != md5($this->SysRs['Secret'])) $this->Err(1048);
        $Rs = $this->UserObj->SetCond(array('GroupAdminId' => 1))->SetSort(array('UserId' => 'ASC'))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1006);
        $Client = 'muLogin';
        $OldTokenList = $this->TokenObj->SetCond(array('UserId' => $Rs['UserId'], 'Client' => $Client))->ExecSelect();
        $OldTokenArr = array_column($OldTokenList, 'Token');
        $Ts = time();
        $Ip = $this->CommonObj->ip();
        $Token = sha1($this->IdCreate());

        try{
            DB::$s_db_obj->beginTransaction();
            if(!empty($OldTokenArr)){
                $this->TokenObj->SetCond(array('Token' => $OldTokenArr))->ExecDelete();
            }
            $this->Log_loginObj->SetInsert(array('UserId' => $Rs['UserId'], 'Ip' => $Ip, 'Ts' => $Ts, 'Ua'=> $_SERVER['HTTP_USER_AGENT']))->ExecInsert();
            $this->TokenObj->SetInsert(array('Token' => $Token, 'UserId' => $Rs['UserId'], 'Client' => $Client, 'Ts' => $Ts))->ExecInsert();
            $this->UserObj->SetCond(array('UserId' => $Rs['UserId']))->SetUpdate(array('TsLast' => $Ts, 'IpLast' => $Ip))->ExecUpdate();
            DB::$s_db_obj->commit();
        }catch(PDOException $e){
            DB::$s_db_obj->rollBack();
            $this->Err(1040);
        }
        foreach($OldTokenArr as $OldToken) $this->TokenObj->clean($OldToken);
        $this->UserObj->clean($Rs['UserId']);
        $this->CookieObj->set(array('Token' => $Token, 'Key' => $Key), 'User', 24*14);
        /* if(isset($_GET['Refer'])){ //后台用户不需要
         $this->CommonObj->ExecScript('window.location.href="'.urldecode($_GET['Refer']).'"');
         } */
        $this->CommonObj->Success($this->CommonObj->Url(array('admin', 'index')));

    }

	public function auth_Action() {
		echo '<br><br><br><h1><center>QFrame PHP Version 1.0.0 </center></h1><center><h2>Author : Qesy, Email : 762264@qq.com</h2><p>Your IP : ' . $this->CommonObj->ip () . '</p></center>';
	}

	public function js_Action(){
	    if(!$this->VeriObj->VeriPara($_GET, array('KeyName'))) die($this->LanguageArr [1001]);
	    $Rs = $this->LabelObj->getOne($_GET['KeyName']);
	    if(empty($Rs))  die($this->LanguageArr [1003]);
	    if($Rs['State'] != 1) die($this->LanguageArr [1049]);
	    $Str = $this->tempRunTest('index', 0, $Rs['Content']);
	    echo 'document.writeln("'.$this->CommonObj->Html2Js($Str).'");';exit;
	}

	public function form_Action($KeyName = ''){
	    if(empty($KeyName)) $this->DieErr(1001);
	    $Rs = $this->Sys_formObj->getOne(trim($KeyName));
	    if(empty($Rs)) $this->Err(1003);
	    if($Rs['State'] != 1) $this->Err(1003);
	    if(!empty($_POST)){
	        $InsertArr = array('TsAdd' =>time(), 'State' => $Rs['StateDefault'], 'FormId' => $Rs['FormId']);
	        if($Rs['IsLogin'] == 1){
	            if(empty($_POST['Token'])) $this->Err(1007);
	            $TokenRs= $this->TokenObj->getOne(trim($_POST['Token']));
	            if(empty($TokenRs)) $this->Err(1007);
	            $InsertArr['UserId'] = $TokenRs['UserId'];
	        }
	        $FieldArr = empty($Rs['FieldJson']) ? array() : json_decode($Rs['FieldJson'], true);
	        foreach($FieldArr as $v){
	            if($v['NotNull'] == 1 && empty($_POST[$v['Name']])) $this->Err(1001);
	            $InsertArr[$v['Name']] = strip_tags($_POST[$v['Name']]);
	        }
	        $Ret = $this->Sys_formObj->SetTbName('form_'.$Rs['KeyName'])->SetInsert($InsertArr)->ExecInsert();
	        if($Ret === false) $this->Err(1002);
	        if(isset($_SERVER['HTTP_REFERER'])) $this->CommonObj->ExecScript('alert("提交成功");location.href="'.$_SERVER['HTTP_REFERER'].'";');
	        $this->CommonObj->ExecScript('alert("提交成功"); history.back();');
	    }
	    echo $this->tempRun('form', $Rs['KeyName']);
	}

	public function admin_Action(){ //管理员登录
	    if(!empty($_POST)){
	        if(!$this->VeriObj->VeriPara($_POST, array('Phone', 'Password', 'VCode'))) $this->Err(1001);
	        if($_POST['VCode'] != $_SESSION['VeriCode']) $this->Err(1012);
	        if(!$this->VeriObj->VeriMobile($_POST['Phone'])) $this->Err(1039);

	        $Rs = $this->UserObj->SetCond(array('Phone' => trim($_POST['Phone']), 'Password' => md5(trim($_POST['Password']))))->ExecSelectOne();
	        if(empty($Rs)) $this->Err(1006);
	        $OldTokenList = $this->TokenObj->SetCond(array('UserId' => $Rs['UserId'], 'Client' => self::CurrentClient))->ExecSelect();
	        $OldTokenArr = array_column($OldTokenList, 'Token');
	        $Ts = time();
	        $Ip = $this->CommonObj->ip();
	        $Token = sha1($this->IdCreate());

            try{
                DB::$s_db_obj->beginTransaction();
                if(!empty($OldTokenArr)){
                    $this->TokenObj->SetCond(array('Token' => $OldTokenArr))->ExecDelete();
                }
                $this->Log_loginObj->SetInsert(array('UserId' => $Rs['UserId'], 'Ip' => $Ip, 'Ts' => $Ts, 'Ua'=> $_SERVER['HTTP_USER_AGENT']))->ExecInsert();
                $this->TokenObj->SetInsert(array('Token' => $Token, 'UserId' => $Rs['UserId'], 'Client' => self::CurrentClient, 'Ts' => $Ts))->ExecInsert();
                $this->UserObj->SetCond(array('UserId' => $Rs['UserId']))->SetUpdate(array('TsLast' => $Ts, 'IpLast' => $Ip))->ExecUpdate();
                DB::$s_db_obj->commit();
            }catch(PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->Err(1040);
            }
            foreach($OldTokenArr as $OldToken) $this->TokenObj->clean($OldToken);
            $this->UserObj->clean($Rs['UserId']);
            $this->CookieObj->set(array('Token' => $Token, 'Key' => $Key), 'User', 24*14);
	        /* if(isset($_GET['Refer'])){ //后台用户不需要
	            $this->CommonObj->ExecScript('window.location.href="'.urldecode($_GET['Refer']).'"');
	        } */
	        $this->CommonObj->Success($this->CommonObj->Url(array('admin', 'index')));
	    }
	    $this->LoadView('index/admin');
	}

	public function adminLogout_Action(){
	    $this->CookieObj->delBatch('User');
	    $this->CommonObj->Success($this->CommonObj->Url(array('index', 'admin')), '退出成功');
	}

	public function logout_Action(){
	    $this->CookieObj->delBatch('User');
	    $this->CommonObj->Success($this->CommonObj->Url(array('index', 'login')), '退出成功');
	}

	public function code_Action(){
	    $this->CodeObj->get_instance();
	    $this->CodeObj->CreateVerifyImage(102, 34);
	    $_SESSION['VeriCode'] = $this->CodeObj->m_verify_code;
	}

	public function test_Action(){
	    var_dump($this->CommonObj->GetQuery());
	    $this->CommonObj->SetQuery('aa', 'bb');
	    var_dump($this->CommonObj->GetQuery());
		echo 'test';
	}

	private function _statFlow(){
	    $Rs = $this->Stat_flowObj->SetCond(array('Date' => date('Y-m-d')))->ExecSelectOne();
	    if(empty($Rs)){
	        $this->Stat_flowObj->SetInsert(array('Date' => date('Y-m-d'), 'FlowNum' => '1'))->ExecInsert();
	    }else{
	        $this->Stat_flowObj->SetCond(array('Date' => date('Y-m-d')))->SetUpdate(array('FlowNum' => ($Rs['FlowNum']+1)))->ExecUpdate();
	    }
	}
}
