<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class User extends ControllersAdmin {
    
    public function index_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        if(!empty($_GET['GroupUserId'])) $CondArr['GroupUserId'] = $_GET['GroupUserId'];
        if(!empty($_GET['Phone'])) $CondArr['Phone LIKE'] = $_GET['Phone'];
        $Arr = $this->UserObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('UserId' => 'DESC'))->ExecSelectAll($Count);
        
        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKV = array_column($GroupUserArr, 'Name', 'GroupUserId');
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['UserId'] = $v['UserId'];
            $GET['GroupUserId'] = $v['GroupUserId'];
            $Arr[$k]['PhoneView'] = $v['Phone'].(($v['GroupAdminId'] != 0) ? '<small class="px-2 text-danger">管理员</small>' : '');
            $Arr[$k]['GroupUserView'] = '<a class="btn btn-primary btn-outline btn-sm" href="'.$this->CommonObj->Url(array('admin', 'user', 'index')).'?'.http_build_query($GET).'">'.$GroupUserKV[$v['GroupUserId']].'</a>';
            //$Arr[$k]['IsEdit'] = $Arr[$k]['IsDel'] = ($v['GroupAdminId'] == 1 || $v['UserId'] == $this->LoginUserRs['UserId']) ? 2 : 1;
            $Arr[$k]['TsLastView'] = empty($v['TsLast']) ? '未登录' : date('Y-m-d H:i', $v['TsLast']);
            $Arr[$k]['IpLastView'] = empty($v['IpLast']) ? '未登录' : $v['IpLast'];
            $Arr[$k]['BtnArr'] = array(
                //array('Desc' => '文档', 'Link' => '#', 'Color' => 'success'),
                array('Desc' => '提升', 'Link' => $this->CommonObj->Url(array('admin', 'user', 'upgrade')), 'Color' => 'danger', 'IsDisabled' => $v['IsAdmin'], 'Para' => $GET),
            );
        }
        $KeyArr = array(
            'UserId' => array('Name' => 'ID', 'Td' => 'th'),
            'PhoneView' => array('Name' => '账号', 'Td' => 'th'),
            //'Sn_Out' => array('Name' => '第三方订单号', 'Td' => 'th'),
            'NickName' => array('Name' => '昵称', 'Td' => 'th'),
            'GroupUserView' => array('Name' => '用户组', 'Td' => 'th'),
            'State' => array('Name' => '状态', 'Td' => 'th', 'Type' => 'Switch'),
            'TsLastView' => array('Name' => '登录时间', 'Td' => 'th'),
            'IpLastView' => array('Name' => '登录IP', 'Td' => 'th'),            
        );
        $this->BuildObj->PrimaryKey = 'UserId';
        $this->BuildObj->IsDel = false;
        //$this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->BuildObj->Arr = array(            
            array('Name' =>'Phone', 'Desc' => '账号',  'Type' => 'input', 'Value' => $_GET['Phone'], 'Required' => 0, 'Col' => 12),
            array('Name' =>'GroupUserId', 'Desc' => '用户组',  'Type' => 'select', 'Data' => $GroupUserKV, 'Value' => $_GET['GroupUserId'], 'Required' => 0, 'Col' => 12),            
        );
        $this->BuildObj->Form('get', 'form-inline');
        $this->HeadHtml = $this->BuildObj->Html;
        $this->BuildObj->Js = 'var ChangeStateUrl="'.$this->CommonObj->Url(array('admin', 'api', 'userState')).'";';
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Phone', 'NickName', 'Sex', 'Password', 'GroupUserId'))) $this->Err(1001);
            if(!$this->VeriObj->VeriMobile($_POST['Phone'])) $this->Err(1001);
            $IsHave = $this->UserObj->SetCond(array('Phone' => $_POST['Phone']))->SetField('COUNT(*) AS c')->ExecSelectOne();
            if($IsHave['c'] > 0) $this->Err(1041);
            $Ret = $this->UserObj->SetInsert(array(
                'Phone' => $_POST['Phone'],
                'NickName' => $_POST['NickName'],
                'Sex' => $_POST['Sex'],
                'Password' => md5(trim($_POST['Password'])),
                'GroupUserId' => $_POST['GroupUserId'],
                'Name' => $_POST['Name'],
                'Mail' => $_POST['Mail'],
                'Head' => $_POST['Head'],
                'Address' => $_POST['Address'],
                'TsAdd' => time(),
                'IpAdd' => $this->CommonObj->ip(),
                'IsAdmin' => 2,
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'user', 'index'), 1888);
        }
        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKV = array_column($GroupUserArr, 'Name', 'GroupUserId');
        $GroupUserId = 1;
        $this->BuildObj->Arr = array(
            array('Name' =>'Phone', 'Desc' => '手机号',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'NickName', 'Desc' => '昵称',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 3),
            array('Name' =>'Sex', 'Desc' => '性别',  'Type' => 'radio', 'Data' => $this->SexArr, 'Value' => 1, 'Required' => 1, 'Col' => 3),
            
            array('Name' =>'Password', 'Desc' => '密码',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 3, 'Placeholder' => '不修改密码，请留空！'),
            array('Name' =>'GroupUserId', 'Desc' => '用户组',  'Type' => 'select', 'Data' => $GroupUserKV, 'Value' => $GroupUserId, 'Required' => 1, 'Col' => 3),
            array('Name' =>'Name', 'Desc' => '真名',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 3), 
            array('Name' =>'Mail', 'Desc' => '邮箱',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 3),
            array('Name' =>'Head', 'Desc' => '头像',  'Type' => 'upload', 'Value' => '', 'Required' => 0, 'Col' => 12),
            array('Name' =>'Address', 'Desc' => '地址',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 12),
            
            array('Name' =>'', 'Desc' => '金币',  'Type' => 'input', 'Value' => '0', 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '积分',  'Type' => 'input', 'Value' => '0', 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '最后登录时间',  'Type' => 'input', 'Value' => '', 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '最后登录IP',  'Type' => 'input', 'Value' => '', 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '注册时间',  'Type' => 'input', 'Value' => '', 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '注册IP',  'Type' => 'input', 'Value' => '', 'Disabled' => 1, 'Col' => 3),
            
        );

        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('UserId'))) $this->Err(1001);
        $UserRs = $this->UserObj->getOne($_GET['UserId']);
        if(empty($UserRs)) $this->Err(1003);
        
        if(!empty($_POST)){
            $UpdateArr = array(
                'NickName' => $_POST['NickName'],
                'Sex' => $_POST['Sex'],
                'GroupUserId' => $_POST['GroupUserId'],
                'Name' => $_POST['Name'],
                'Mail' => $_POST['Mail'],
                'Head' => $_POST['Head'],
                'Address' => $_POST['Address'],
            );
            if(!empty($_POST['Password'])) $UpdateArr['Password'] = md5(trim($_POST['Password']));
            $Ret = $this->UserObj->SetCond(array('UserId' => $UserRs['UserId']))->SetUpdate($UpdateArr)->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->UserObj->clean($UserRs['UserId']);
            $this->Jump(array('admin', 'user', 'index'), 1888);
        }
        
        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKV = array_column($GroupUserArr, 'Name', 'GroupUserId');
        $GroupUserId = 1;
        $this->BuildObj->Arr = array(
            array('Name' =>'Phone', 'Desc' => '手机号',  'Type' => 'input', 'Value' => $UserRs['Phone'], 'Disabled' => 1, 'Col' => 6),
            array('Name' =>'NickName', 'Desc' => '昵称',  'Type' => 'input', 'Value' => $UserRs['NickName'], 'Required' => 1, 'Col' => 3),
            array('Name' =>'Sex', 'Desc' => '性别',  'Type' => 'radio', 'Data' => $this->SexArr, 'Value' => $UserRs['Sex'], 'Required' => 1, 'Col' => 3),
            
            array('Name' =>'Password', 'Desc' => '密码',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 3, 'Placeholder' => '不修改密码，请留空！'),
            array('Name' =>'GroupUserId', 'Desc' => '用户组',  'Type' => 'select', 'Data' => $GroupUserKV, 'Value' => $UserRs['GroupUserId'], 'Required' => 1, 'Col' => 3),
            array('Name' =>'Name', 'Desc' => '真名',  'Type' => 'input', 'Value' => $UserRs['Name'], 'Required' => 0, 'Col' => 3),
            array('Name' =>'Mail', 'Desc' => '邮箱',  'Type' => 'input', 'Value' => $UserRs['Mail'], 'Required' => 0, 'Col' => 3),
            array('Name' =>'Head', 'Desc' => '头像',  'Type' => 'upload', 'Value' => $UserRs['Head'], 'Required' => 0, 'Col' => 12),
            array('Name' =>'Address', 'Desc' => '地址',  'Type' => 'input', 'Value' => $UserRs['Address'], 'Required' => 0, 'Col' => 12),
            
            array('Name' =>'', 'Desc' => '金币',  'Type' => 'input', 'Value' => $UserRs['Money'], 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '积分',  'Type' => 'input', 'Value' => $UserRs['Coins'], 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '最后登录时间',  'Type' => 'input', 'Value' => date('Y-m-d H:i:s', $UserRs['TsLast']), 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '最后登录IP',  'Type' => 'input', 'Value' => $UserRs['IpLast'], 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '注册时间',  'Type' => 'input', 'Value' => date('Y-m-d H:i:s', $UserRs['TsAdd']), 'Disabled' => 1, 'Col' => 3),
            array('Name' =>'', 'Desc' => '注册IP',  'Type' => 'input', 'Value' => $UserRs['IpAdd'], 'Disabled' => 1, 'Col' => 3),
            
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
        
    }
    
    public function upgrade_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('UserId'))) $this->Err(1001);
        $UserRs = $this->UserObj->getOne($_GET['UserId']);
        if(empty($UserRs)) $this->Err(1003);
        if($UserRs['IsAdmin'] == 1) $this->Err(1001);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('GroupAdminId'))) $this->Err(1001);
            $Ret = $this->UserObj->SetCond(array('UserId' => $UserRs['UserId']))->SetUpdate(array('IsAdmin' => 1, 'GroupAdminId' => $_POST['GroupAdminId']))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->UserObj->clean($UserRs['UserId']);
            $this->Jump(array('admin', 'user', 'index'), 1888);
        }
        $GroupAdminArr = $this->Group_adminObj->getList();
        $GroupAdminKV = array_column($GroupAdminArr, 'Name', 'GroupAdminId');
        $GroupAdminId = 1;
        $this->BuildObj->Arr = array(
            array('Name' =>'Phone', 'Desc' => '手机号',  'Type' => 'input', 'Value' => $UserRs['Phone'], 'Disabled' => 1, 'Col' => 6),
            array('Type' => 'htmlFill', 'Col' => 6),
            array('Name' =>'GroupAdminId', 'Desc' => '管理组',  'Type' => 'select', 'Data' => $GroupAdminKV, 'Value' => $GroupAdminId, 'Required' => 1, 'Col' => 6),
            array('Type' => 'htmlFill', 'Col' => 6),
        );
        
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
        
    }
    
    
}