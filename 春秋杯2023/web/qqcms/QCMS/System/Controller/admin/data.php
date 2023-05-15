<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Data extends ControllersAdmin {
    
    public function replace_Action(){ //批量替换
        $ModelArr = $this->Sys_modelObj->getList();
        $ModelKV = array(-1 => '全部模型');
        foreach($ModelArr as $v) $ModelKV[$v['ModelId']] = $v['Name'].'模型';
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('ModelId', 'Search', 'Replace', 'Field'))) $this->Err(1001);
            $ModelKeyKv = array_column($ModelArr, 'NameKey', 'ModelId');

            try{
                DB::$s_db_obj->beginTransaction();
                if($_POST['ModelId'] != -1){
                    $this->Table_articleObj->SetTbName('table_'.$ModelKeyKv[$_POST['ModelId']])->SetUpdate('`'.$_POST['Field'].'` = replace (`'.$_POST['Field'].'`, \''.$_POST['Search'].'\', \''.$_POST['Replace'].'\')')->ExecUpdate();
                }else{
                    foreach($ModelKeyKv as $k => $v){
                        $this->Table_articleObj->SetTbName('table_'.$v)->SetUpdate('`'.$_POST['Field'].'` = replace (`'.$_POST['Field'].'`, \''.$_POST['Search'].'\', \''.$_POST['Replace'].'\')')->ExecUpdate();
                    }
                }          
                DB::$s_db_obj->commit();
            }catch (PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->Err(1002);
            }
            $this->Jump(array('admin', 'data', 'replace'), 1888);
        }
        $FieldArr = array('Title' => '标题', 'Content' => '内容', 'Source' => '来源', 'Author' => '作者', 'Keywords' => '关键字', 'Description' => '描述');
        $this->BuildObj->Arr = array(
            array('Name' =>'ModelId', 'Desc' => '内容模型',  'Type' => 'select', 'Data' => $ModelKV, 'Value' => -1, 'Required' => 1, 'Col' => 6),
            array('Type' => 'htmlFill', 'Col' => 6),
            array('Name' =>'Field', 'Desc' => '字段选择', 'Type' => 'radio', 'Data' => $FieldArr, 'Value' => 'Title', 'Col' => 12),
            array('Name' =>'Search', 'Desc' => '将字符',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12, 'Placeholder' => '*请输入替换前的内容'),
            array('Name' =>'Replace', 'Desc' => '替换成',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12, 'Placeholder' => '*请输入替换后的内容'),
            
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function highReplace_Action(){
        $DbConfig = Config::DbConfig();
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('TableName', 'Field', 'Search', 'Replace'))) $this->Err(1001); 
            if(in_array($_POST['TableName'], array('qc_user', 'qc_token'))) $this->Err(1048); 
            $Ret = $this->Table_articleObj->SetTbName(substr(trim($_POST['TableName']), strlen($DbConfig['Prefix'])))->SetUpdate('`'.$_POST['Field'].'` = replace (`'.$_POST['Field'].'`, \''.$_POST['Search'].'\', \''.$_POST['Replace'].'\')')->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'data', 'highReplace'), 1888);
        }
        $TableArr = $this->SysObj->query('show tables', array());
        $TableKv = array();
        
        foreach($TableArr as $v){       
            if(in_array($v['Tables_in_'.$DbConfig['Name']], array('qc_user', 'qc_token'))) continue;
            $TableKv[$v['Tables_in_'.$DbConfig['Name']]] = $v['Tables_in_'.$DbConfig['Name']];
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'TableName', 'Desc' => '选择数据表',  'Type' => 'select', 'Data' => $TableKv, 'Value' => $TableArr[0]['Tables_in_'.$DbConfig['Name']], 'Required' => 1, 'Col' => 6),
            array('Name' =>'Field', 'Desc' => '字段选择', 'Type' => 'select', 'Data' => array(), 'Value' => '0', 'Required' => 1, 'Col' => 6),
            array('Name' =>'Search', 'Desc' => '将字符',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12, 'Placeholder' => '*请输入替换前的内容'),
            array('Name' =>'Replace', 'Desc' => '替换成',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12, 'Placeholder' => '*请输入替换后的内容'),
            
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/data/highReplace');
    }
    
}