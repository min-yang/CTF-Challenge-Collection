<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Page extends ControllersAdmin {
    
    public function index_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        if(!empty($_GET['PageCateId'])) $CondArr['PageCateId'] = $_GET['PageCateId'];
        $Arr = $this->PageObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Sort' => 'ASC', 'PageId' => 'ASC'))->ExecSelectAll($Count);
        
        $pageCateArr = $this->Page_cateObj->getList();
        $pageCateKV = array_column($pageCateArr, 'Name', 'PageCateId');
        foreach($Arr as $k => $v){
            //$Arr[$k]['LogoView'] = empty($v['Logo']) ? '无Logo' : '<image src="'.$v['Logo'].'" style="height:33px;"/>';
            $GET = $_GET;
            $GET['PageCateId'] = $v['PageCateId'];
            $Arr[$k]['TsAddView'] = date('Y-m-d', $v['TsAdd']);
            $Arr[$k]['PageCateName'] = '<a class="btn btn-primary btn-outline btn-sm" href="'.$this->CommonObj->Url(array('admin', 'page', 'index')).'?'.http_build_query($GET).'">'.$pageCateKV[$v['PageCateId']].'</a>';
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="page" data-index="'.$v['PageId'].'" value="'.$v['Sort'].'"/>';
            $Arr[$k]['BtnArr'] = array(
              array('Desc' => '预览', 'Link' => $this->createUrl('page', $v['PageId'], $v['PinYin'], $v['PY']), 'Color' => 'success', 'IsBlank' => 1),  
            );
        }
        $KeyArr = array(
            'PageId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '网站名称', 'Td' => 'th'),
            //'LogoView' => array('Name' => 'LOGO', 'Td' => 'th'),
            //'Link' => array('Name' => '网站地址', 'Td' => 'th'),
            'PageCateName' => array('Name' => '分类', 'Td' => 'th'),
            'State' => array('Name' => '状态', 'Td' => 'th', 'Type' => 'Switch'),
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),
            
        );
        $this->BuildObj->PrimaryKey = 'PageId';
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '分类管理', 'Class' => 'primary', 'Link' => $this->CommonObj->Url(array('admin', 'pageCate', 'index'))),
        );
        $this->BuildObj->NameAdd = '添加单页';
        //$this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        
 
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->BuildObj->Arr = array(            
            array('Name' =>'PageCateId', 'Desc' => '选择分类',  'Type' => 'select', 'Data' => $pageCateKV, 'Value' => $_GET['PageCateId'], 'Required' => 0, 'Col' => 12),            
        );
        $this->BuildObj->Form('get', 'form-inline');
        $this->HeadHtml = $this->BuildObj->Html;
        $this->BuildObj->Js = 'var ChangeStateUrl="'.$this->CommonObj->Url(array('admin', 'api', 'pageState')).'";';
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'PageCateId', 'TempDetail'))) $this->Err(1001);
            $Ret = $this->PageObj->SetInsert(array(
                'PageCateId' => $_POST['PageCateId'],
                'Name' => $_POST['Name'],
                'NameEn' => $_POST['NameEn'],
                'TempDetail' => $_POST['TempDetail'],
                //'UrlDetail' => $_POST['UrlDetail'],
                'SeoTitle' => $_POST['SeoTitle'],
                'Keywords' => $_POST['Keywords'],
                'Description' => $_POST['Description'],
                'Content' => $_POST['Content'],
                'Sort' => 99,
                'State' => 1,
                'Pic' => trim($_POST['Pic']),
                'PinYin' => $this->PinYinObj->str2pys(trim($_POST['Name'])),
                'PY' => $this->PinYinObj->str2py(trim($_POST['Name'])),
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'page', 'index'), 1888);
        }
        $CateArr = $this->Page_cateObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'PageCateId');
        $CateDefaultId = $CateArr[0]['PageCateId'];
        $TempList = $this->getTemplate('page_');
        $Keys = array_keys($TempList);
        $UrlListDesc = '<span class="text-dark">
            <span class="mr-3 font-weight-bold">{PageId}</span>分类ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+分类ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+分类ID<br>
            <span class="mr-3 font-weight-bold">{Page}</span>页码<br>
        </span>
        ';
        $this->BuildObj->Arr = array(
            array(
                'Title' => '核心设置',
                'Form' => array(
                        array('Name' =>'Name', 'Desc' => '单页名字',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
                    array('Name' =>'NameEn', 'Desc' => '英文别名',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 3),
                        array('Name' =>'PageCateId', 'Desc' => '分类',  'Type' => 'select', 'Data' => $CateKV, 'Value' => $CateDefaultId, 'Required' => 1, 'Col' => 3),                        
                    array('Name' =>'Pic', 'Desc' => '单页图片',  'Type' => 'upload', 'Value' => '', 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Content', 'Desc' => '单页内容',  'Type' => 'textarea', 'Value' => '', 'Required' => 0, 'Col' => 12, 'Row' => 22, 'Class' => 'Content'),
                    array('Name' =>'IsEditor', 'Desc' => '加载编辑器',  'Type' => 'hidden', 'Value' => '2', 'Required' => 0, 'Col' => 12),
                )
            ),
            array(
                'Title' => '扩展设置',
                'Form' => array(                   
                    array('Name' =>'TempDetail', 'Desc' => '选择模板',  'Type' => 'select', 'Data' => $TempList, 'Value' => $Keys[0], 'Required' => 1, 'Col' => 12),
                    //array('Name' =>'UrlDetail', 'Desc' => '文件名字',  'Type' => 'input', 'Value' => '{PageId}.html', 'Required' => 1, 'Col' => 6),
                    array('Name' =>'SeoTitle', 'Desc' => 'SEO标题',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Keywords', 'Desc' => '关键字',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Description', 'Desc' => '单页描述',  'Type' => 'textarea', 'Value' => '', 'Required' => 0, 'Col' => 12, 'Row' => 6),                    
                    //array('Desc' => '规则说明',  'Type' => 'html', 'Value' => $UrlListDesc, 'Required' => 1, 'Col' => 12),
                )
            )
            
        );
        $this->BuildObj->FormFooterBtnArr[] = array('Name' => 'Editor', 'Desc' => '加载编辑器', 'Type' => 'button', 'Class' => 'success');
        $this->PageTitle2 = $this->BuildObj->FormMultipleTitle();
        $this->BuildObj->FormMultiple('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('PageId'))) $this->Err(1001);
        $Rs = $this->PageObj->SetCond(array('PageId' => $_GET['PageId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'PageCateId', 'TempDetail'))) $this->Err(1001);
            $Ret = $this->PageObj->SetCond(array('PageId' => $Rs['PageId']))->SetUpdate(array(
                'PageCateId' => $_POST['PageCateId'],
                'Name' => $_POST['Name'],
                'NameEn' => $_POST['NameEn'],
                'TempDetail' => $_POST['TempDetail'],
                //'UrlDetail' => $_POST['UrlDetail'],
                'SeoTitle' => $_POST['SeoTitle'],
                'Keywords' => $_POST['Keywords'],
                'Description' => $_POST['Description'],
                'Content' => $_POST['Content'],
                'Pic' => trim($_POST['Pic']),
                'PinYin' => $this->PinYinObj->str2pys(trim($_POST['Name'])),
                'PY' => $this->PinYinObj->str2py(trim($_POST['Name'])),
            ))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'page', 'index'), 1888);
        }
        
        $CateArr = $this->Page_cateObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'PageCateId');
        $TempList = $this->getTemplate('page_');
        $Keys = array_keys($TempList);
        $UrlListDesc = '<span class="text-dark">
            <span class="mr-3 font-weight-bold">{PageId}</span>分类ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+分类ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+分类ID<br>
            <span class="mr-3 font-weight-bold">{Page}</span>页码<br>
        </span>
        ';
        $this->BuildObj->Arr = array(
            array(
                'Title' => '核心设置',
                'Form' => array(
                    array('Name' =>'Name', 'Desc' => '单页名字',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 6),
                    array('Name' =>'NameEn', 'Desc' => '英文别名',  'Type' => 'input', 'Value' => $Rs['NameEn'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'PageCateId', 'Desc' => '分类',  'Type' => 'select', 'Data' => $CateKV, 'Value' => $Rs['PageCateId'], 'Required' => 1, 'Col' => 3),
                    array('Name' =>'Pic', 'Desc' => '单页图片',  'Type' => 'upload', 'Value' => $Rs['Pic'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Content', 'Desc' => '单页内容',  'Type' => 'textarea', 'Value' => $Rs['Content'], 'Required' => 0, 'Col' => 12, 'Row' => 22, 'Class' => 'Content'),
                    array('Name' =>'IsEditor', 'Desc' => '加载编辑器',  'Type' => 'hidden', 'Value' => '2', 'Required' => 0, 'Col' => 12),
                )
            ),
            array(
                'Title' => '扩展设置',
                'Form' => array(
                    array('Name' =>'TempDetail', 'Desc' => '选择模板',  'Type' => 'select', 'Data' => $TempList, 'Value' => $Rs['TempDetail'], 'Required' => 1, 'Col' => 12),
                    //array('Name' =>'UrlDetail', 'Desc' => '文件名字',  'Type' => 'input', 'Value' => $Rs['UrlDetail'], 'Required' => 1, 'Col' => 6),
                    array('Name' =>'SeoTitle', 'Desc' => 'SEO标题',  'Type' => 'input', 'Value' => $Rs['SeoTitle'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Keywords', 'Desc' => '关键字',  'Type' => 'input', 'Value' => $Rs['Keywords'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Description', 'Desc' => '单页描述',  'Type' => 'textarea', 'Value' => $Rs['Description'], 'Required' => 0, 'Col' => 12),
                    //array('Desc' => '规则说明',  'Type' => 'html', 'Value' => $UrlListDesc, 'Required' => 1, 'Col' => 12),
                )
            )
            
        );
        $this->BuildObj->FormFooterBtnArr[] = array('Name' => 'Editor', 'Desc' => '加载编辑器', 'Type' => 'button', 'Class' => 'success');
        $this->PageTitle2 = $this->BuildObj->FormMultipleTitle();
        $this->BuildObj->FormMultiple('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('PageId'))) $this->Err(1001);
        $Rs = $this->PageObj->SetCond(array('PageId' => $_GET['PageId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        $Ret = $this->PageObj->SetCond(array('PageId' =>$Rs['PageId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->Jump(array('admin', 'page', 'index'), 1888);
    }
    
}