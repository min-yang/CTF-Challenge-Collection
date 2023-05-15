<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Site extends ControllersAdmin {
    
    public function index_Action(){        
        $Arr = $this->SiteObj->getList();        
        foreach($Arr as $k => $v){            
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="site" data-index="'.$v['SiteId'].'" value="'.$v['Sort'].'"/>';
            $Arr[$k]['TsView'] = date('Y-m-d', $v['Ts']);
            $Arr[$k]['BtnArr'] = array(
                array('Desc' => '登录管理', 'Color' => 'success', 'Link' => $v['WebSite'].'index/muLogin.html', 'Para' => array('Secret' => md5($v['Secret'])))
            );
        }
        $KeyArr = array(
            'SiteId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '网站名字', 'Td' => 'th'),
            'WebSite' => array('Name' => '网站地址', 'Td' => 'th'),
            
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),
            'TsView' => array('Name' => '添加时间', 'Td' => 'th'),
        );
        $this->BuildObj->PrimaryKey = 'SiteId';
        
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'WebSite', 'Secret'))) $this->Err(1001);
            if(strpos($_POST['WebSite'], 'http') === false || substr($_POST['WebSite'], -1) != '/')  $this->Err(1048);
            $Ret = $this->SiteObj->SetInsert(array(
                'Name' => $_POST['Name'],
                'WebSite' => trim($_POST['WebSite']),
                'Secret' => trim($_POST['Secret']),
                'Sort' => 99,
                'Ts' => time(),
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->SiteObj->cleanList();
            $this->Jump(array('admin', 'site', 'index'), 1888);
        }
        
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '网站名称',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'WebSite', 'Desc' => '网站地址',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12, 'Placeholder' => 'http://www.xxx.com/'),
            array('Name' =>'Secret', 'Desc' => '秘钥',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),            
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('SiteId'))) $this->Err(1001);
        $Rs = $this->SiteObj->SetCond(array('SiteId' => $_GET['SiteId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'WebSite'))) $this->Err(1001);
            if(strpos($_POST['WebSite'], 'http') === false || substr($_POST['WebSite'], -1) != '/')  $this->Err(1048);
            $UpdateArr = array('Name' => $_POST['Name'], 'WebSite' => trim($_POST['WebSite']));
            if(!empty($_POST['Secret'])) $UpdateArr['Secret'] = trim($_POST['Secret']);
            $Ret = $this->SiteObj->SetCond(array('SiteId' => $Rs['SiteId']))->SetUpdate($UpdateArr)->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->SiteObj->cleanList();
            $this->Jump(array('admin', 'site', 'index'), 1888);
        }
        
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '网站名称',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 12),
            array('Name' =>'WebSite', 'Desc' => '网站地址',  'Type' => 'input', 'Value' => $Rs['WebSite'], 'Required' => 1, 'Col' => 12, 'Placeholder' => 'http://www.xxx.com/'),
            array('Name' =>'Secret', 'Desc' => '秘钥',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 12, 'Placeholder' => '不改变请留空'),
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('SiteId'))) $this->Err(1001);
        $Ret = $this->SiteObj->SetCond(array('SiteId' => $_GET['SiteId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->SiteObj->cleanList();
        $this->Jump(array('admin', 'site', 'index'), 1888);
    }
    
}