<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Label extends ControllersAdmin {
    
    public function index_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        if(!empty($_GET['LabelCateId'])) $CondArr['LabelCateId'] = $_GET['LabelCateId'];
        $Arr = $this->LabelObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Sort' => 'ASC', 'LabelId' => 'ASC'))->ExecSelectAll($Count);
        
        $labelCateArr = $this->Label_cateObj->getList();
        $labelCateKV = array_column($labelCateArr, 'Name', 'LabelCateId');

        foreach($Arr as $k => $v){  
            $GET = $_GET;
            $GET['LabelCateId'] = $v['LabelCateId'];
            $Arr[$k]['LabelCateName'] = '<a class="btn btn-primary btn-outline btn-sm" href="'.$this->CommonObj->Url(array('admin', 'label', 'index')).'?'.http_build_query($GET).'">'.$labelCateKV[$v['LabelCateId']].'</a>';
            $Arr[$k]['KeyNameView'] = '<input class="form-control" disabled="disabled" type="text" value="{{label:'.$v['KeyName'].'}}"/>';
            $Arr[$k]['JsView'] = '<input class="form-control" disabled="disabled" type="text" value="<script language=JavaScript src=\''.$_SERVER['REQUEST_SCHEME'].'://'.URL_DOMAIN.'/index/js?KeyName='.$v['KeyName'].'\'></script>"/>';
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="label" data-index="'.$v['LabelId'].'" value="'.$v['Sort'].'"/>';
        }
        $KeyArr = array(
            'LabelId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '标签名', 'Td' => 'th'),
            'KeyNameView' => array('Name' => '调用标签名', 'Td' => 'th', 'Style' => 'width:200px;'), 
            'JsView' => array('Name' => '外部JS调用', 'Td' => 'th', 'Style' => 'width:400px;'),         
            'LabelCateName' => array('Name' => '分类', 'Td' => 'th'),
            'State' => array('Name' => '状态', 'Td' => 'th', 'Type' => 'Switch'),
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),            
        );
        $this->BuildObj->PrimaryKey = 'LabelId';
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '分类管理', 'Class' => 'primary', 'Link' => $this->CommonObj->Url(array('admin', 'labelCate', 'index'))),
        );
        $this->BuildObj->NameAdd = '添加标签';
        //$this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->BuildObj->Arr = array(
            array('Name' =>'LabelCateId', 'Desc' => '选择分类',  'Type' => 'select', 'Data' => $labelCateKV, 'Value' => $_GET['LabelCateId'], 'Required' => 0, 'Col' => 12),
        );
        $this->BuildObj->Form('get', 'form-inline');
        $this->HeadHtml = $this->BuildObj->Html;
        $this->BuildObj->Js = 'var ChangeStateUrl="'.$this->CommonObj->Url(array('admin', 'api', 'labelState')).'";';
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'KeyName', 'LabelCateId'))) $this->Err(1001);
            if(!$this->VeriObj->IsPassword($_POST['KeyName'], 1, 30)) $this->Err(1048);
            $Ret = $this->LabelObj->SetInsert(array(
                'Name' => $_POST['Name'],
                'LabelCateId' => $_POST['LabelCateId'],
                'KeyName' => $_POST['KeyName'],
                'Content' => $_POST['Content'],
                'IsEditor' => $_POST['IsEditor'],
                'State' => 1,
                'Sort' => 99,
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'label', 'index'), 1888);
        }
        $CateArr = $this->Label_cateObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'LabelCateId');
        
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '标签名字',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'KeyName', 'Desc' => '调用名字 (只能英文和数字)',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 3),
            array('Name' =>'LabelCateId', 'Desc' => '分类',  'Type' => 'select', 'Data' => $CateKV, 'Value' => $CateArr[0]['LabelCateId'], 'Required' => 1, 'Col' => 3),
            array('Name' =>'Content', 'Desc' => '内容',  'Type' => 'textarea', 'Value' => '', 'Required' => 0, 'Col' => 12, 'Row' => 22, 'Class' => 'Content'),
            array('Name' =>'IsEditor', 'Desc' => '加载编辑器',  'Type' => 'hidden', 'Value' => '2', 'Required' => 0, 'Col' => 12),
            
        );
        $this->BuildObj->FormFooterBtnArr[] = array('Name' => 'Editor', 'Desc' => '加载编辑器', 'Type' => 'button', 'Class' => 'success');
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('LabelId'))) $this->Err(1001);
        $Rs = $this->LabelObj->SetCond(array('LabelId' => $_GET['LabelId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'KeyName', 'LabelCateId', 'IsEditor'))) $this->Err(1001);
            if(!$this->VeriObj->IsPassword($_POST['KeyName'], 1, 30)) $this->Err(1048);
            $Ret = $this->LabelObj->SetCond(array('LabelId' => $Rs['LabelId']))->SetUpdate(array(
                'Name' => $_POST['Name'],
                'LabelCateId' => $_POST['LabelCateId'],
                'KeyName' => $_POST['KeyName'],
                'Content' => $_POST['Content'],
                'IsEditor' => $_POST['IsEditor'],
            ))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->LabelObj->clean($Rs['KeyName']);
            $this->Jump(array('admin', 'label', 'index'), 1888);
        }
        $CateArr = $this->Label_cateObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'LabelCateId');
        
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '标签名字',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 6),
            array('Name' =>'KeyName', 'Desc' => '调用名字 (只能英文和数字)',  'Type' => 'input', 'Value' => $Rs['KeyName'], 'Required' => 1, 'Col' => 3),
            array('Name' =>'LabelCateId', 'Desc' => '分类',  'Type' => 'select', 'Data' => $CateKV, 'Value' => $Rs['LabelCateId'], 'Required' => 1, 'Col' => 3),
            array('Name' =>'Content', 'Desc' => '内容',  'Type' => 'textarea', 'Value' => $Rs['Content'], 'Required' => 0, 'Col' => 12, 'Row' => 22, 'Class' => 'Content'),
            array('Name' =>'IsEditor', 'Desc' => '加载编辑器',  'Type' => 'hidden', 'Value' => $Rs['IsEditor'], 'Required' => 0, 'Col' => 12),
            
        );
        $this->BuildObj->FormFooterBtnArr[] = array('Name' => 'Editor', 'Desc' => '加载编辑器', 'Type' => 'button', 'Class' => 'success');
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('LabelId'))) $this->Err(1001);
        $Rs = $this->LabelObj->SetCond(array('LabelId' => $_GET['LabelId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        $Ret = $this->LabelObj->SetCond(array('LabelId' => $Rs['LabelId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->LabelObj->clean($Rs['KeyName']);
        $this->Jump(array('admin', 'label', 'index'), 1888);
    }
    
}