<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Link extends ControllersAdmin {
    
    public function index_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        if(!empty($_GET['LinkCateId'])) $CondArr['LinkCateId'] = $_GET['LinkCateId'];
        $Arr = $this->LinkObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Sort' => 'ASC', 'LinkId' => 'ASC'))->ExecSelectAll($Count);
        
        $linkCateArr = $this->Link_cateObj->getList();
        $linkCateKV = array_column($linkCateArr, 'Name', 'LinkCateId');
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['LinkCateId'] = $v['LinkCateId'];
            $Arr[$k]['LogoView'] = empty($v['Logo']) ? '无Logo' : '<image src="'.$v['Logo'].'" style="height:33px;"/>';
            $Arr[$k]['TsAddView'] = date('Y-m-d', $v['TsAdd']);
            $Arr[$k]['LinkCateName'] = '<a class="btn btn-sm btn-primary btn-outline" href="'.$this->CommonObj->Url(array('admin', 'link', 'index')).'?'.http_build_query($GET).'">'.$linkCateKV[$v['LinkCateId']].'</a>';
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="link" data-index="'.$v['LinkId'].'" value="'.$v['Sort'].'"/>';
        }
        $KeyArr = array(
            'LinkId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '网站名称', 'Td' => 'th'),
            'LogoView' => array('Name' => 'LOGO', 'Td' => 'th'),
            'Link' => array('Name' => '网站地址', 'Td' => 'th'),
            'LinkCateName' => array('Name' => '分类', 'Td' => 'th'),
            'State' => array('Name' => '状态', 'Td' => 'th', 'Type' => 'Switch'),
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),
            'TsAddView' => array('Name' => '时间', 'Td' => 'th'),
            
        );
        $this->BuildObj->PrimaryKey = 'LinkId';
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '分类管理', 'Link' => $this->CommonObj->Url(array('admin', 'linkCate', 'index')), 'Class' => 'primary'),
        );
        $this->BuildObj->NameAdd = '添加友情链接';
        //$this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->BuildObj->Arr = array(
            array('Name' =>'LinkCateId', 'Desc' => '选择分类',  'Type' => 'select', 'Data' => $linkCateKV, 'Value' => $_GET['LinkCateId'], 'Required' => 0, 'Col' => 12),
        );
        $this->BuildObj->Form('get', 'form-inline');
        $this->HeadHtml = $this->BuildObj->Html;
        $this->BuildObj->Js = 'var ChangeStateUrl="'.$this->CommonObj->Url(array('admin', 'api', 'linkState')).'";';
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'LinkCateId', 'Link', 'IsIndex'))) $this->Err(1001);            
            $Ret = $this->LinkObj->SetInsert(array(
                'Name' => $_POST['Name'],
                'LinkCateId' => $_POST['LinkCateId'],
                'Mail' => $_POST['Mail'],
                'Link' => $_POST['Link'],
                'Logo' => $_POST['Logo'],
                'Info' => $_POST['Info'],
                'IsIndex' => $_POST['IsIndex'],
                'State' => 1,
                'TsAdd' =>time(),
                'Sort' => 99,
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'link', 'index'), 1888);
        }
        $CateArr = $this->Link_cateObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'LinkCateId');
        $CateDefaultId = $CateArr[0]['LinkCateId'];
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '网站名字',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'LinkCateId', 'Desc' => '分类',  'Type' => 'select', 'Data' => $CateKV, 'Value' => $CateDefaultId, 'Required' => 1, 'Col' => 3),
            array('Name' =>'Mail', 'Desc' => '站长邮箱',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 3),
            array('Name' =>'Link', 'Desc' => '网站地址',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
            array('Name' =>'Logo', 'Desc' => '网站Logo',  'Type' => 'upload', 'Value' => '', 'Required' => 0, 'Col' => 12),            
            array('Name' =>'Info', 'Desc' => '网站简介',  'Type' => 'textarea', 'Value' => '', 'Required' => 0, 'Col' => 12),            
            array('Name' =>'IsIndex', 'Desc' => '是否首页',  'Type' => 'radio', 'Data' => $this->IsArr, 'Value' => '1', 'Required' => 1, 'Col' => 12),
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('LinkId'))) $this->Err(1001);
        $Rs = $this->LinkObj->getOne($_GET['LinkId']);
        if(empty($Rs)) $this->Err(1003);
        
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'LinkCateId', 'Link', 'IsIndex'))) $this->Err(1001);
            $Ret = $this->LinkObj->SetCond(array('LinkId' => $Rs['LinkId']))->SetUpdate(array(
                'Name' => $_POST['Name'],
                'LinkCateId' => $_POST['LinkCateId'],
                'Mail' => $_POST['Mail'],
                'Link' => $_POST['Link'],
                'Logo' => $_POST['Logo'],
                'Info' => $_POST['Info'],
                'IsIndex' => $_POST['IsIndex'],               
            ))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->LinkObj->clean($Rs['LinkId']);
            $this->Jump(array('admin', 'link', 'index'), 1888);
        }
        $CateArr = $this->Link_cateObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'LinkCateId');
        
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '网站名字',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 6),
            array('Name' =>'LinkCateId', 'Desc' => '分类',  'Type' => 'select', 'Data' => $CateKV, 'Value' => $Rs['LinkCateId'], 'Required' => 1, 'Col' => 3),
            array('Name' =>'Mail', 'Desc' => '站长邮箱',  'Type' => 'input', 'Value' => $Rs['Mail'], 'Required' => 0, 'Col' => 3),
            array('Name' =>'Link', 'Desc' => '网站地址',  'Type' => 'input', 'Value' => $Rs['Link'], 'Required' => 1, 'Col' => 12),
            array('Name' =>'Logo', 'Desc' => '网站Logo',  'Type' => 'upload', 'Value' => $Rs['Logo'], 'Required' => 0, 'Col' => 12),
            array('Name' =>'Info', 'Desc' => '网站简介',  'Type' => 'textarea', 'Value' => $Rs['Info'], 'Required' => 0, 'Col' => 12),
            array('Name' =>'IsIndex', 'Desc' => '是否首页',  'Type' => 'radio', 'Data' => $this->IsArr, 'Value' => $Rs['IsIndex'], 'Required' => 1, 'Col' => 12),
        );        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
        
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('LinkId'))) $this->Err(1001);
        $Rs = $this->LinkObj->getOne($_GET['LinkId']);
        if(empty($Rs)) $this->Err(1003);
        $Ret = $this->LinkObj->SetCond(array('LinkId' => $Rs['LinkId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->LinkObj->clean($Rs['LinkId']);
        $this->Jump(array('admin', 'link', 'index'), 1888);
    }
    
    
}