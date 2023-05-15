<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Log extends ControllersAdmin {

    public function operate_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        $Arr = $this->Log_operateObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('LogOperateId' => 'Desc'))->ExecSelectAll($Count);
        $AdminArr = $this->UserObj->SetCond(array('IsAdmin' => 1))->SetField('UserId, NickName')->ExecSelect();
        $AdminKV = array_column($AdminArr, 'NickName', 'UserId');
        foreach($Arr as $k => $v){            
            $Arr[$k]['TsView'] = date('Y-m-d H:i', $v['Ts']);
            $Arr[$k]['QueryView'] = empty($v['Query']) ? '无' : $v['Query'];
            $Arr[$k]['NickName'] = $AdminKV[$v['UserId']];
        }
        $KeyArr = array(
            'LogOperateId' => array('Name' => 'ID', 'Td' => 'th'),
            'NickName' => array('Name' => '管理员', 'Td' => 'th'),
            'Url' => array('Name' => '访问路径', 'Td' => 'th'),
            'Method' => array('Name' => '访问方式', 'Td' => 'th'),
            'Ip' => array('Name' => '访问IP', 'Td' => 'th'),
            'QueryView' => array('Name' => '参数', 'Td' => 'th'),
            'TsView' => array('Name' => '时间', 'Td' => 'th'),

        );
        $this->BuildObj->PrimaryKey = 'LogOperateId';
        $this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function login_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        $Arr = $this->Log_loginObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('LogLoginId' => 'Desc'))->ExecSelectAll($Count);
        $AdminArr = $this->UserObj->SetCond(array('IsAdmin' => 1))->SetField('UserId, NickName')->ExecSelect();
        $AdminKV = array_column($AdminArr, 'NickName', 'UserId');
        foreach($Arr as $k => $v){
            $Arr[$k]['TsView'] = date('Y-m-d H:i', $v['Ts']);
            $Arr[$k]['NickName'] = $AdminKV[$v['UserId']];
        }
        $KeyArr = array(
            'LogLoginId' => array('Name' => 'ID', 'Td' => 'th'),
            'NickName' => array('Name' => '管理员', 'Td' => 'th'),
            'Ua' => array('Name' => '用户代理', 'Td' => 'th'),
            'Ip' => array('Name' => '访问IP', 'Td' => 'th'),
            'TsView' => array('Name' => '时间', 'Td' => 'th'),
            
        );
        $this->BuildObj->PrimaryKey = 'LogOperateId';
        $this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
}