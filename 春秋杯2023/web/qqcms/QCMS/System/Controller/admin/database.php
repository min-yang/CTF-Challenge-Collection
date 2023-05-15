<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Database extends ControllersAdmin {
    
    public $Path = PATH_STATIC.'backups/';
    
    public function index_Action(){
        $Files = scandir($this->Path);
        $FileArr = array();
        foreach($Files as $v){
            
            if(in_array($v, array('.', '..'))) continue;            
            if(is_dir($this->Path.$v) || $v == 'index.html') continue;
            $StrArr = explode('.', $v);
            $Ext = $StrArr[count($StrArr)-1];
            if($Ext != 'sql') continue;
            $Path = $this->Path.$v;
            $Ts = filectime($Path);
            $FileArr[$Ts] = array('Name' => $v, 'Size' => $this->CommonObj->Size(filesize($Path)), 'Ts' => date('Y-m-d H:i:s', $Ts));
            
        }
        foreach($FileArr as $k => $v){
            $GET = $_GET;
            $GET['Name'] = $v['Name'];
            $FileArr[$k]['BtnArr'] = array(
                array('Desc' => '下载', 'Color' => 'success', 'Link' => URL_STATIC.'backups/'.$v['Name']),
                array('Desc' => '还原', 'Color' => 'primary', 'Confirm' => '还原数据库将覆盖原来数据，是否继续？', 'Link' => $this->CommonObj->Url(array('admin', 'database', 'restore')), 'Para' => $GET),
            );
        }
        $KeyArr = array(
            'Name' => array('Name' => '文件名', 'Td' => 'th'),
            'Size' => array('Name' => '大小', 'Td' => 'th'),
            'Ts' => array('Name' => '创建时间', 'Td' => 'th'),
        );
        
        krsort($FileArr);
        $this->BuildObj->NameAdd = '添加备份';
        $this->BuildObj->LinkAdd = $this->CommonObj->Url(array('admin', 'database', 'backups'));        
        /* $this->BuildObj->NameEdit = '还原';
        $this->BuildObj->LinkEdit = $this->CommonObj->Url(array('admin', 'database', 'restore')); */
        $this->BuildObj->PrimaryKey = 'Name';
        $this->BuildObj->IsEdit = false;
        $tmp['Table'] = $this->BuildObj->Table($FileArr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function backups_Action(){ //备份数据库
        $Sql = $this->Sys_modelObj->ExportSql();
        $FileName = date('YmdHis').'_'.$this->IdCreate().'.sql';
        $Ret = @file_put_contents($this->Path.$FileName, $Sql);
        if($Ret === false) $this->Err(1002);
        $this->Jump(array('admin', 'database', 'index'), 1888);
    }
    
    public function restore_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Name'))) $this->Err(1001);
        $Path = $this->Path.trim($_GET['Name']);
        if(!file_exists($Path)) $this->Err(1035);
        $this->SysObj->ImportSql($this->Path.trim($_GET['Name']));
        $this->Jump(array('admin', 'database', 'index'), 1888);
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Name'))) $this->Err(1001);
        $Path = $this->Path.trim($_GET['Name']);
        if(!file_exists($Path)) $this->Err(1035);
        if(!unlink($Path)) $this->Err(1002);
        $this->Jump(array('admin', 'database', 'index'), 1888);
    }
}