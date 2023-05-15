<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Admin extends ControllersAdmin {

    public function index_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array('IsAdmin' => 1);
        if(!empty($_GET['GroupAdminId'])) $CondArr['GroupAdminId'] = $_GET['GroupAdminId'];
        if(!empty($_GET['Phone'])) $CondArr['Phone LIKE '] = $_GET['Phone'];
        $Arr = $this->UserObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('GroupAdminId' => 'ASC', 'UserId' => 'ASC'))->ExecSelectAll($Count);

        $GroupAdminArr = $this->Group_adminObj->getList();        
        $GroupAdminKV = array_column($GroupAdminArr, 'Name', 'GroupAdminId');
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['GroupAdminId'] = $v['GroupAdminId'];
            $Arr[$k]['AdminGroupView'] = '<a class="btn btn-primary btn-sm btn-outline" href="'.$this->CommonObj->Url(array('admin', 'admin', 'index')).'?'.http_build_query($GET).'">'.$GroupAdminKV[$v['GroupAdminId']].'</a>';
            $Arr[$k]['IsDel'] = ($v['GroupAdminId'] == 1 || $v['UserId'] == $this->LoginUserRs['UserId']) ? 2 : 1;
            $Arr[$k]['TsLastView'] = empty($v['TsLast']) ? '未登录' : date('Y-m-d H:i', $v['TsLast']);
            $Arr[$k]['IpLastView'] = empty($v['IpLast']) ? '未登录' : $v['IpLast'];
            $Arr[$k]['BtnArr'] = array(
                //array('Desc' => '文档', 'Link' => '#', 'Color' => 'success'),
            );
        }
        $KeyArr = array(
            'UserId' => array('Name' => 'ID', 'Td' => 'th'),
            'Phone' => array('Name' => '账号', 'Td' => 'th'),
            //'Sn_Out' => array('Name' => '第三方订单号', 'Td' => 'th'),
            'NickName' => array('Name' => '昵称', 'Td' => 'th'),
            'AdminGroupView' => array('Name' => '管理组', 'Td' => 'th'),
            'TsLastView' => array('Name' => '登录时间', 'Td' => 'th'),
            'IpLastView' => array('Name' => '登录IP', 'Td' => 'th'),

        );
        $this->BuildObj->PrimaryKey = 'UserId';
        $this->BuildObj->NameDel = '降级';
        //$this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->BuildObj->Arr = array(
            array('Name' =>'Phone', 'Desc' => '账号',  'Type' => 'input', 'Value' => $_GET['Phone'], 'Required' => 0, 'Col' => 12),
            array('Name' =>'GroupAdminId', 'Desc' => '管理组',  'Type' => 'select', 'Data' => $GroupAdminKV, 'Value' => $_GET['GroupAdminId'], 'Required' => 0, 'Col' => 12),
        );
        $this->BuildObj->Form('get', 'form-inline');
        $this->HeadHtml = $this->BuildObj->Html;
        $this->LoadView('admin/common/list', $tmp);
    }

    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Phone', 'NickName', 'Password', 'GroupAdminId'))) $this->Err(1001);
            if(!$this->VeriObj->VeriMobile($_POST['Phone'])) $this->Err(1001);
            $IsHave = $this->UserObj->SetCond(array('Phone' => $_POST['Phone']))->SetField('COUNT(*) AS c')->ExecSelectOne();
            if($IsHave['c'] > 0) $this->Err(1041);
            $Ret = $this->UserObj->SetInsert(array(
                'Phone' => $_POST['Phone'],
                'NickName' => $_POST['NickName'],
                'Head' => $_POST['Head'],
                'Password' => md5(trim($_POST['Password'])),
                'GroupAdminId' => $_POST['GroupAdminId'],
                'IsAdmin' => 1,
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'admin', 'index'), 1888);
        }
        $GroupAdminArr = $this->Group_adminObj->getList();
        $GroupAdminKV = array_column($GroupAdminArr, 'Name', 'GroupAdminId');
        $this->BuildObj->Arr = array(
            array('Name' =>'Phone', 'Desc' => '手机号',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'NickName', 'Desc' => '昵称',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'Head', 'Desc' => '头像',  'Type' => 'upload', 'Value' => '', 'Required' => 0, 'Col' => 12),
            array('Name' =>'Password', 'Desc' => '密码',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12, 'Placeholder' => '不修改密码，请留空！'),
        );
        $this->BuildObj->Arr[] = array('Name' =>'GroupAdminId', 'Desc' => '管理组',  'Type' => 'select', 'Data' => $GroupAdminKV, 'Value' => '', 'Required' => 1, 'Col' => 12);
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('UserId'))) $this->Err(1001);
        $UserRs = $this->UserObj->getOne($_GET['UserId']);
        if(empty($UserRs)) $this->Err(1003);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('NickName', 'GroupAdminId'))) $this->Err(1001);
            //if(!$this->VeriObj->VeriMobile($_POST['Phone'])) $this->Err(1001);
            $UpdateArr = array(
                //'Phone' => $_POST['Phone'],
                'NickName' => $_POST['NickName'],
                'Head' => $_POST['Head'],
                //'Password' => md5(trim($_POST['Password'])),
                'GroupAdminId' => $_POST['GroupAdminId'],
                'IsAdmin' => 1,
            );
            if(!empty($_POST['Password'])) $UpdateArr['Password'] = md5(trim($_POST['Password']));
            $Ret = $this->UserObj->SetCond(array('UserId' => $UserRs['UserId']))->SetUpdate($UpdateArr)->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->UserObj->clean($UserRs['UserId']);
            $this->Jump(array('admin', 'admin', 'index'), 1888);
        }
        $GroupAdminArr = $this->Group_adminObj->getList();
        $GroupAdminKV = array_column($GroupAdminArr, 'Name', 'GroupAdminId');
        $this->BuildObj->Arr = array(
            array('Name' =>'Phone', 'Desc' => '手机号',  'Type' => 'input', 'Value' => $UserRs['Phone'], 'Disabled' => 1, 'Col' => 12),
            array('Name' =>'NickName', 'Desc' => '昵称',  'Type' => 'input', 'Value' => $UserRs['NickName'], 'Required' => 1, 'Col' => 12),
            array('Name' =>'Head', 'Desc' => '头像',  'Type' => 'upload', 'Value' => $UserRs['Head'], 'Required' => 0, 'Col' => 12),
            array('Name' =>'Password', 'Desc' => '密码 ',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 12, 'Placeholder' => '不修改密码，请留空！'),
        );
        $this->BuildObj->Arr[] = array('Name' =>'GroupAdminId', 'Desc' => '管理组',  'Type' => 'select', 'Data' => $GroupAdminKV, 'Value' => $UserRs['GroupAdminId'], 'Required' => 1, 'Col' => 12);
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('UserId'))) $this->Err(1001);
        $UserRs = $this->UserObj->getOne($_GET['UserId']);
        if(empty($UserRs)) $this->Err(1003);
        if($UserRs['GroupAdminId'] == 1 || $UserRs['UserId'] == $this->LoginUserRs['UserId']) $this->Err(1042);
        $Ret = $this->UserObj->SetCond(array('UserId' => $UserRs['UserId']))->SetUpdate(array('IsAdmin' => 2, 'GroupAdminId' => 0))->ExecUpdate();
        if($Ret === false) $this->Err(1002);
        $this->UserObj->clean($UserRs['UserId']);
        $this->Jump(array('admin', 'admin', 'index'), 1888);
    }

}