<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class GroupUser extends ControllersAdmin {
    
    public function index_Action(){
        $Arr = $this->Group_userObj->SetSort(array('GroupUserId' => 'ASC'))->ExecSelect();
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['GroupUserId'] = $v['GroupUserId'];
            $Arr[$k]['IsEdit'] = $Arr[$k]['IsDel'] = ($v['IsSys'] == 1) ? 2 : 1;
            $Arr[$k]['BtnArr'] = array(
                array('Desc' => '组用户', 'Link' => $this->CommonObj->Url(array('admin', 'user', 'index')), 'Color' => 'success', 'Para' => $GET),
            );
        }
        $KeyArr = array(
            'GroupUserId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '管理组', 'Td' => 'th'),
        );
        $this->BuildObj->PrimaryKey = 'GroupUserId';        
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            //$Permission = empty($_POST['Permission']) ? '' : implode('|', array_keys($_POST['Permission']));
            $Ret = $this->Group_userObj->SetInsert(array('Name' => $_POST['Name'], 'IsSys' => 2))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Group_userObj->cleanList();
            $this->Jump(array('admin', 'groupUser', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '会员组',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
        );
        /* $DataArr = self::_MenuPermission();
        foreach($DataArr as $k => $v){
            $Data = array_column($v['SubArr'], 'Name', 'Url');
            $this->BuildObj->Arr[] = array('Name' =>'Permission', 'Desc' => $v['Name'],  'Type' => 'checkbox', 'Data' => $Data, 'Value' => '', 'Required' => 1, 'Col' => 12);
        } */
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('GroupUserId'))) $this->Err(1001);
        $Rs = $this->Group_userObj->getOne($_GET['GroupUserId']);
        if(empty($Rs)) $this->Err(1003);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            //$Permission = empty($_POST['Permission']) ? '' : implode('|', array_keys($_POST['Permission']));
            $Ret = $this->Group_userObj->SetCond(array('GroupUserId' => $Rs['GroupUserId']))->SetUpdate(array('Name' => $_POST['Name']))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->Group_userObj->clean($Rs['GroupUserId']);
            $this->Jump(array('admin', 'groupUser', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '管理组',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 12),
        );
        /* $DataArr = self::_MenuPermission();
        foreach($DataArr as $k => $v){
            $Data = array_column($v['SubArr'], 'Name', 'Url');
            $this->BuildObj->Arr[] = array('Name' =>'Permission', 'Desc' => $v['Name'],  'Type' => 'checkbox', 'Data' => $Data, 'Value' => $Rs['Permission'], 'Required' => 1, 'Col' => 12);
        } */
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('GroupUserId'))) $this->Err(1001);
        $Rs = $this->Group_userObj->getOne($_GET['GroupUserId']);
        if(empty($Rs)) $this->Err(1003);
        if($Rs['IsSys'] == 1) $this->Err(1042);
        $UserCount = $this->UserObj->SetCond(array('GroupUserId' => $Rs['GroupUserId']))->SetField('COUNT(*) AS c')->ExecSelectOne();
        if($UserCount['c'] > 0) $this->Err(1043);
        $Ret = $Ret = $this->Group_userObj->SetCond(array('GroupUserId' => $Rs['GroupUserId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->Group_userObj->clean($Rs['GroupUserId']);
        $this->Group_userObj->cleanList();
        $this->Jump(array('admin', 'groupUser', 'index'), 1888);
    }
    
    private function _MenuPermission(){
        $DataArr = array();
        foreach($this->MenuArr as $k => $v){
            $KArr = explode('/', $k);
            if($KArr[0] != 'admin') continue;
            $Name = '';
            switch($KArr[1]){
                case 'admin':
                    $Name = '管理员管理';
                    break;
                case 'groupAdmin':
                    $Name = '管理组管理';
                    break;
                case 'user':
                    $Name = '用户管理';
                    break;
                case 'groupUser':
                    $Name = '用户组管理';
                    break;
                default:
                    $Name = '其他管理';
                    break;
            }
            if(!isset($DataArr[$KArr[1]])) $DataArr[$KArr[1]] = array('Name' => $Name, 'SubArr' => array());
            $DataArr[$KArr[1]]['SubArr'][] = $v;
        }
        return $DataArr;
    }
    
    
}