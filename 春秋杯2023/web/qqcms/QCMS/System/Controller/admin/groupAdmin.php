<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class GroupAdmin extends ControllersAdmin {
    
    public function index_Action(){
        $Arr = $this->Group_adminObj->SetSort(array('GroupAdminId' => 'ASC'))->ExecSelect();
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['GroupAdminId'] = $v['GroupAdminId'];
            $Arr[$k]['IsEdit'] = $Arr[$k]['IsDel'] = ($v['GroupAdminId'] == 1) ? 2 : 1;
            $Arr[$k]['BtnArr'] = array(
                array('Desc' => '组用户', 'Link' => $this->CommonObj->Url(array('admin', 'admin', 'index')), 'Color' => 'success', 'Para' => $GET),
            );
        }
        $KeyArr = array(
            'GroupAdminId' => array('Name' => 'ID', 'Td' => 'th'),         
            'Name' => array('Name' => '管理组', 'Td' => 'th'),            
        );
        $this->BuildObj->PrimaryKey = 'GroupAdminId';
        //$this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;

        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            $Permission = empty($_POST['Permission']) ? '' : implode('|', array_keys($_POST['Permission']));
            $Ret = $this->Group_adminObj->SetInsert(array('Name' => $_POST['Name'], 'IsSys' => 2, 'Permission' => $Permission))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Group_adminObj->cleanList();
            $this->Jump(array('admin', 'groupAdmin', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '管理组',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),            
        );  
        $DataArr = self::_MenuPermission();
        foreach($DataArr as $k => $v){
            $Data = array_column($v['SubArr'], 'Name', 'Key');
            $this->BuildObj->Arr[] = array('Name' =>'Permission', 'Desc' => $v['Name'],  'Type' => 'checkbox', 'Data' => $Data, 'Value' => '', 'Required' => 1, 'Col' => 12);
        }
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('GroupAdminId'))) $this->Err(1001);
        $Rs = $this->Group_adminObj->getOne($_GET['GroupAdminId']);
        if(empty($Rs)) $this->Err(1003);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            $Permission = empty($_POST['Permission']) ? '' : implode('|', array_keys($_POST['Permission']));
            $Ret = $this->Group_adminObj->SetCond(array('GroupAdminId' => $Rs['GroupAdminId']))->SetUpdate(array('Name' => $_POST['Name'], 'Permission' => $Permission))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->Group_adminObj->clean($Rs['GroupAdminId']);
            $this->Jump(array('admin', 'groupAdmin', 'edit'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '管理组',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 12),
        );
        $DataArr = self::_MenuPermission();
        foreach($DataArr as $k => $v){
            $Data = array_column($v['SubArr'], 'Name', 'Key');
            $this->BuildObj->Arr[] = array('Name' =>'Permission', 'Desc' => $v['Name'],  'Type' => 'checkbox', 'Data' => $Data, 'Value' => $Rs['Permission'], 'Required' => 1, 'Col' => 12);
        }
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('GroupAdminId'))) $this->Err(1001);
        $Rs = $this->Group_adminObj->getOne($_GET['GroupAdminId']);
        if(empty($Rs)) $this->Err(1003);
        if($Rs['IsSys'] == 1) $this->Err(1042);
        $UserCount = $this->UserObj->SetCond(array('IsAdmin' => 1, 'GroupAdminId' => $Rs['GroupAdminId']))->SetField('COUNT(*) AS c')->ExecSelectOne();
        if($UserCount['c'] > 0) $this->Err(1043);
        $Ret = $Ret = $this->Group_adminObj->SetCond(array('GroupAdminId' => $Rs['GroupAdminId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->Group_adminObj->clean($Rs['GroupAdminId']);
        $this->Group_adminObj->cleanList();
        $this->Jump(array('admin', 'groupAdmin', 'index'), 1888);
    }
    
    private function _MenuPermission(){
        $DataArr = array();
        foreach($this->MenuArr as $k => $v){
            $KArr = explode('/', $k);
            //if(count($KArr) < 3) continue;
            if($KArr[0] != 'admin') continue;
            $Key = $Name = '';
            if(in_array($KArr[1], array('category', 'page', 'pageCate', 'labelCate', 'label', 'form', 'formField', 'formData'))){
                $Key = 'admin/category';
                $Name = '分类管理';
            }elseif(in_array($KArr[1], array('content'))){
                $Key = 'admin/content';
                $Name = '内容管理';
            }elseif(in_array($KArr[1], array('user', 'groupUser'))){
                $Key = 'admin/user';
                $Name = '会员中心';
            }elseif(in_array($KArr[1], array('data', 'model', 'modelField', 'database', 'redisManage'))){
                $Key = 'admin/data';
                $Name = '数据维护';
            }elseif(in_array($KArr[1], array('linkCate', 'link', 'inlinkCate', 'inlink', 'file', 'swiper', 'swiperCate', 'tag'))){
                $Key = 'admin/assist';
                $Name = '辅助插件';
            }elseif(in_array($KArr[1], array('templates'))){
                $Key = 'admin/templates';
                $Name = '模板管理';
            }elseif(in_array($KArr[1], array('sys', 'admin', 'groupAdmin', 'log', 'site'))){
                $Key = 'admin/sys';
                $Name = '系统管理';
            }elseif(in_array($KArr[1], array('api'))){
                $Key = 'admin/api';
                $Name = 'API管理';
            }else{
                $Key = 'admin/other';
                $Name = '其他管理'.$KArr[1];
            }
            if(!isset($DataArr[$Key])) $DataArr[$Key] = array('Name' => $Name, 'SubArr' => array());
            $v['Key'] = $k;
            $DataArr[$Key]['SubArr'][] = $v;
        }
        return $DataArr;
    }
    
    
}