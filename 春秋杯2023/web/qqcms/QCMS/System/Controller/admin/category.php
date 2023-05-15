<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Category extends ControllersAdmin {
    
    public function index_Action(){
        $this->CategoryObj->getTreeDetal();
        $Arr = $this->CategoryObj->CateTreeDetail;
        $Level = 0;
        $TrClass = '';
        $ModelArr = $this->Sys_modelObj->getList();
        $ModelKV = array_column($ModelArr, 'Name', 'ModelId');
        $ModelKV[-1] = '封面';
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['CateId'] = $v['CateId'];
            $GET['ModelId'] = $v['ModelId'];
            $IsPost = ($v['IsPost'] == 1 && $v['IsLink'] != 1) ? '<span class="text-danger mr-2">发布</span>': '<span class="text-secondary mr-2">发布</span>';
            $IsLink = ($v['IsLink'] == 1) ? '<span class="text-danger mr-2">外链</span>': '<span class="text-secondary mr-2">外链</span>';
            $IsShow = ($v['IsShow'] == 1) ? '<span class="text-danger mr-2">显示</span>': '<span class="text-secondary mr-2">显示</span>';
            $IsHasSub = ($v['HasSub']) ? '<i class="ml-2 bi bi-chevron-down ShowBtn" data-cateid="'.$v['CateId'].'"></i>' : '';
            $NameView = ($v['Level'] == 0) ? $v['Name'] : '<span style="padding-left:'.(30*$v['Level']).'px;"><span class="pr-2">├─</span>'.$v['Name'].'</span>';
            $Arr[$k]['NameView'] = $NameView .$IsHasSub;
            $Arr[$k]['AttrView'] = $IsShow.$IsPost.$IsLink;
            $Model = $ModelKV[$v['ModelId']];
            $Arr[$k]['ModelView'] = '<span class="text-secondary">'.$Model.'</span>';
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="category" data-index="'.$v['CateId'].'" value="'.$v['Sort'].'"/>';
            $Arr[$k]['UserLevel'] = '<span class="text-secondary">开放浏览</span>';
            $Arr[$k]['BtnArr'] = array(
                array('Desc' => '预览', 'Color' => 'success', 'Link' => $this->createUrl('cate', $v['CateId'], $v['PinYin'], $v['PY']), 'IsBlank' => 1),
                array('Desc' => '内容', 'Color' => 'success', 'IsDisabled' => ($v['IsLink'] == 1 || $v['IsPost'] != 1 || $v['ModelId'] == -1) ? '1' : '2', 'Link' => $this->CommonObj->Url(array('admin', 'content', 'index')), 'Para' => $GET),
                array('Desc' => '加子类', 'Link' => $this->CommonObj->Url(array('admin', 'category', 'add')), 'Para' => $GET),
                array('Desc' => '移动', 'Link' => $this->CommonObj->Url(array('admin', 'category', 'move')), 'Para' => $GET),                
            );
            if($Level < $v['Level']){
                $Level = $v['Level'];
                $TrClass .= ' ShowDiv_'.$v['PCateId'].' ';
            }else{
                $Level = $v['Level'];
                $TrClass = ' ShowDiv_'.$v['PCateId'].' ';
            }
            //$TrClass .= ' SubShowDiv_'.$v['PCateId'].' ';
            $Arr[$k]['TrClass'] = ($v['Level'] == 0) ? ' SubShowDiv_'.$v['PCateId'].' '.$TrClass : ' SubShowDiv_'.$v['PCateId'].' d-none'.$TrClass;
        }
        $KeyArr = array(
            'CateId' => array('Name' => 'ID', 'Td' => 'th'),
            'NameView' => array('Name' => '分类名', 'Td' => 'th'),
            'ModelView' => array('Name' => '模型', 'Td' => 'th'),
            'AttrView' => array('Name' => '属性', 'Td' => 'th'), 
            'UserLevel' => array('Name' => '浏览权限', 'Td' => 'th'), 
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px'),
        );
        $this->BuildObj->NameAdd = '添加顶级分类';
        $this->BuildObj->PrimaryKey = 'CateId';        
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/category/index', $tmp);
    }
    
    public function add_Action(){
        $PCateId = intval($_GET['CateId']);
        $ModelId = 1;
        $TCateId = 0;
        if(!empty($PCateId)){
            $Rs = $this->CategoryObj->getOne($PCateId);
            $TCateId = ($Rs['TCateId'] == 0) ? $Rs['CateId'] : $Rs['TCateId'];
            if(empty($Rs)) $this->Err(1001);
            $ModelId = $Rs['ModelId'];
        }        
        $FieldArr = empty($this->SysRs['CategoryFieldJson']) ? array() : json_decode($this->SysRs['CategoryFieldJson'], true);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'ModelId'))) $this->Err(1001);
            $IsPost = isset($_POST['Attr']['IsPost']) ? 1 : 2;
            $IsShow = isset($_POST['Attr']['IsShow']) ? 1 : 2;
            $IsLink = isset($_POST['Attr']['IsLink']) ? 1 : 2;
            $InsertArr = array(
                'PCateId' => $PCateId,
                'TCateId' => $TCateId,
                'Name' =>trim($_POST['Name']),
                'NameEn' => trim($_POST['NameEn']),
                'ModelId' => intval($_POST['ModelId']),
                'Pic' => trim($_POST['Pic']),
                'IsPost' => $IsPost,
                'IsShow' => $IsShow,
                'IsLink' => $IsLink,
                'UserLevel' => intval($_POST['UserLevel']),
                'LinkUrl' => trim($_POST['LinkUrl']),
                'TempList' => trim($_POST['TempList']),
                'TempDetail' => trim($_POST['TempDetail']),
                /* 'UrlList' => trim($_POST['UrlList']),
                'UrlDetail' => trim($_POST['UrlDetail']),            */     
                'SeoTitle' => trim($_POST['SeoTitle']),
                'Keywords' => trim($_POST['Keywords']),
                'Description' => trim($_POST['Description']),
                'Content' => trim($_POST['Content']),
                'IsCross' => 2,
                'Sort' => 99,
                'PinYin' => $this->PinYinObj->str2pys(trim($_POST['Name'])),
                'PY' => $this->PinYinObj->str2py(trim($_POST['Name'])),
            );
            
            foreach($FieldArr as $v){
                if(is_array($_POST[$v['Name']])){
                    $_POST[$v['Name']] = implode('|', array_keys($_POST[$v['Name']]));
                }elseif($v['Type'] == 'datetime'){
                    $_POST[$v['Name']] = strtotime($_POST[$v['Name']]);
                }else{
                    $_POST[$v['Name']] = trim($_POST[$v['Name']]);
                }
                if($v['NotNull'] == 1 && empty($_POST[$v['Name']])) $this->Err(1001);
                $InsertArr[$v['Name']] = $_POST[$v['Name']];
            }               
            
            $Ret = $this->CategoryObj->SetInsert($InsertArr)->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->CategoryObj->cleanList();
            $this->Jump(array('admin', 'category', 'index'));
        }
        $ModelArr = $this->Sys_modelObj->getList();        
        $ModelKV = array_column($ModelArr, 'Name', 'ModelId');        
        $ModelKV[-1] = '封面';
        $ModelTempKV = array_column($ModelArr, 'KeyName', 'ModelId');
        $ModelTempKV[-1] = 'page';
        
        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKv = array(0 => '开放浏览'); 
        foreach(array_column($GroupUserArr, 'Name', 'GroupUserId') as $k => $v) $GroupUserKv[$k] = $v;
        $AttrArr = array('IsShow' => '显示', 'IsPost' => '发布', 'IsLink' => '外链');
        $AttrValArr = array('IsShow', 'IsPost');
        $TempList = $this->getTemplate('list_');
        $TempDetail = $this->getTemplate('detail_');
        $UrlList = 'list_{CateId}_{Page}.html';
        $UrlDetail = '{Id}.html';
        $UrlListDesc = '<span class="text-dark">
            <span class="mr-3 font-weight-bold">{CateId}</span>分类ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+分类ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+分类ID<br>
            <span class="mr-3 font-weight-bold">{Page}</span>页码<br>
        </span>
        ';
        $UrlDetailDesc = '<span class="text-dark">
        	<span class="mr-3 font-weight-bold">{Y}、{M}、{D}</span>年月日<br>
            <span class="mr-3 font-weight-bold">{Ts}</span>INT类型的UNIX时间戳<br>
            <span class="mr-3 font-weight-bold">{Id}</span>文章ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+文章ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+文章ID<br>
        </span>
        ';
        $this->BuildObj->Arr = array(
            array(
            'Title' => '核心设置',
            'Form' => array(                
                array('Name' =>'Name', 'Desc' => '分类名称',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 4),
                array('Name' =>'NameEn', 'Desc' => '英文别名',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 2),
                array('Name' =>'ModelId', 'Desc' => '内容模型',  'Type' => 'select', 'Data' => $ModelKV, 'Value' => $ModelId, 'Required' => 1, 'Col' => 3),
                array('Name' =>'Attr', 'Desc' => '属性',  'Type' => 'checkbox', 'Data' => $AttrArr, 'Value' => implode('|', $AttrValArr), 'Required' => 1, 'Col' => 3),
                //array('Name' =>'IsLink', 'Desc' => '是否外链',  'Type' => 'radio', 'Data' => $this->IsArr, 'Value' => 2, 'Required' => 1, 'Col' => 2),
                array('Name' =>'Pic', 'Desc' => '分类图片',  'Type' => 'upload', 'Value' => '', 'Required' => 0, 'Col' => 12),
                array('Name' =>'SeoTitle', 'Desc' => 'SEO标题',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 12),
                array('Name' =>'Keywords', 'Desc' => '关键字',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 12),
                array('Name' =>'Description', 'Desc' => '分类描述',  'Type' => 'textarea', 'Value' => '', 'Required' => 0, 'Col' => 12), 
                array('Name' =>'UserLevel', 'Desc' => '浏览权限',  'Type' => 'select', 'Data' => $GroupUserKv, 'Value' => 0, 'Required' => 1, 'Col' => 12),
                array('Name' =>'LinkUrl', 'Desc' => '外链地址',  'Type' => 'hidden', 'Value' => '', 'Required' => 0, 'Col' => 12),
            )),  
            array(
                'Title' => '高级设置',
                'Form' => array(                    
                    array('Name' =>'TempList', 'Desc' => '列表模板',  'Type' => 'select', 'Data' => $TempList, 'Value' => '', 'Required' => 0, 'Col' => 6),
                    array('Name' =>'TempDetail', 'Desc' => '详情模板',  'Type' => 'select', 'Data' => $TempDetail, 'Value' => '', 'Required' => 0, 'Col' => 6),
                    /* array('Name' =>'UrlList', 'Desc' => '列表命名规则',  'Type' => 'input', 'Value' => $UrlList, 'Required' => 0, 'Col' => 6, 'Help' => '撒旦法安防'),
                    array('Name' =>'UrlDetail', 'Desc' => '详情命名规则',  'Type' => 'input', 'Value' => $UrlDetail, 'Required' => 0, 'Col' => 6),
                    array('Desc' => '规则说明',  'Type' => 'html', 'Value' => $UrlListDesc, 'Required' => 1, 'Col' => 6),
                    array('Desc' => '规则说明',  'Type' => 'html', 'Value' => $UrlDetailDesc, 'Required' => 1, 'Col' => 6), */
                )),
            array(
                'Title' => '分类内容',
                'Form' => array(
                    array('Name' =>'Content', 'Desc' => '分类内容',  'Type' => 'editor', 'Value' => $v['Content'], 'Required' => 0, 'Col' => 12),                    
                )),
        );
        foreach($FieldArr as $v){
            $DataArr = array();
            if(!empty($v['Data'])){
                $Data = explode('|', $v['Data']);
                foreach($Data as $sv) $DataArr[$sv] = $sv;
            }            
            $Row = in_array($v['Type'], array('editor', 'textarea')) ? 12 : 3;
            $this->BuildObj->Arr[0]['Form'][] =  array('Name' => $v['Name'], 'Desc' => $v['Comment'],  'Type' => $v['Type'], 'Data' => $DataArr, 'Value' => $v['Content'], 'Required' => $v['NotNull'], 'Col' => $Row);
        }
        
        $this->PageTitle2 = $this->BuildObj->FormMultipleTitle();
        $this->BuildObj->FormMultiple('post', 'form-row');
        $tmp['ModelTempKV'] = $ModelTempKV;
        $this->LoadView('admin/category/edit', $tmp);
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('CateId'))) $this->Err(1001);
        $CateRs = $this->CategoryObj->getOne($_GET['CateId']);
        if(empty($CateRs)) $this->Err(1003);
        $FieldArr = empty($this->SysRs['CategoryFieldJson']) ? array() : json_decode($this->SysRs['CategoryFieldJson'], true);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            $IsPost = isset($_POST['Attr']['IsPost']) ? 1 : 2;
            $IsShow = isset($_POST['Attr']['IsShow']) ? 1 : 2;
            $IsLink = isset($_POST['Attr']['IsLink']) ? 1 : 2;
            $UpdateArr = array(
                //'PCateId' => $PCateId,
                'Name' =>trim($_POST['Name']),
                'NameEn' => trim($_POST['NameEn']),
                //'ModelId' => intval($_POST['ModelId']),
                'Pic' => trim($_POST['Pic']),
                'IsPost' => $IsPost,
                'IsShow' => $IsShow,
                'IsLink' => $IsLink,
                'UserLevel' => intval($_POST['UserLevel']),
                'LinkUrl' => trim($_POST['LinkUrl']),
                'TempList' => trim($_POST['TempList']),
                'TempDetail' => trim($_POST['TempDetail']),
                /* 'UrlList' => trim($_POST['UrlList']),
                'UrlDetail' => trim($_POST['UrlDetail']), */
                'SeoTitle' => trim($_POST['SeoTitle']),
                'Keywords' => trim($_POST['Keywords']),
                'Description' => trim($_POST['Description']),
                'Content' => trim($_POST['Content']),
                'IsCross' => 2,
                'PinYin' => $this->PinYinObj->str2pys(trim($_POST['Name'])),
                'PY' => $this->PinYinObj->str2py(trim($_POST['Name'])),
            );
            
            foreach($FieldArr as $v){
                if(is_array($_POST[$v['Name']])){
                    $_POST[$v['Name']] = implode('|', array_keys($_POST[$v['Name']]));
                }elseif($v['Type'] == 'datetime'){
                    $_POST[$v['Name']] = strtotime($_POST[$v['Name']]);
                }else{
                    $_POST[$v['Name']] = trim($_POST[$v['Name']]);
                }
                //$_POST[$v['Name']] = is_array($_POST[$v['Name']]) ? implode('|', array_keys($_POST[$v['Name']])) : $_POST[$v['Name']];
                if($v['NotNull'] == 1 && empty($_POST[$v['Name']])) {
                    $this->Err(1001);
                }
                $UpdateArr[$v['Name']] = $_POST[$v['Name']];
            } 
            
            $Ret = $this->CategoryObj->SetCond(array('CateId' => $CateRs['CateId']))->SetUpdate($UpdateArr)->ExecUpdate();
            
            if(isset($_POST['SyncSubColumn']) && $_POST['SyncSubColumn'] == 1){
                $this->CategoryObj->getAllCateId($CateRs['CateId'], $CateRs['ModelId']);
                $SubRet = $this->CategoryObj->SetCond(array('CateId' => $this->CategoryObj->AllSubCateIdArr))->SetUpdate(array('TempList' => trim($_POST['TempList']), 'TempDetail' => trim($_POST['TempDetail'])))->ExecUpdate();
                foreach($this->CategoryObj->AllSubCateIdArr as $v) $this->CategoryObj->clean($v);
            }
            if($Ret === false) $this->Err(1002);
            $this->CategoryObj->cleanList();
            $this->CategoryObj->clean($CateRs['CateId']);
            unset($_GET['CateId']);
            $this->Jump(array('admin', 'category', 'index'));
        }
        
        $ModelArr = $this->Sys_modelObj->getList();
        $ModelKV = array_column($ModelArr, 'Name', 'ModelId');
        $ModelKV[-1] = '封面';
        $ModelTempKV = array_column($ModelArr, 'KeyName', 'ModelId');
        $ModelTempKV[-1] = 'page';
        
        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKv = array(0 => '开放浏览');
        foreach(array_column($GroupUserArr, 'Name', 'GroupUserId') as $k => $v) $GroupUserKv[$k] = $v;
        $AttrArr = array('IsShow' => '显示', 'IsPost' => '发布', 'IsLink' => '外链');
        $AttrValArr = array();
        if($CateRs['IsShow'] == 1) $AttrValArr[] = 'IsShow';
        if($CateRs['IsPost'] == 1) $AttrValArr[] = 'IsPost';
        if($CateRs['IsLink'] == 1) $AttrValArr[] = 'IsLink';
        $TempList = $this->getTemplate('list_');
        $TempDetail = $this->getTemplate('detail_');

        $UrlListDesc = '<span class="text-dark">
            <span class="mr-3 font-weight-bold">{CateId}</span>分类ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+分类ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+分类ID<br>
            <span class="mr-3 font-weight-bold">{Page}</span>页码<br>
        </span>
        ';
        $UrlDetailDesc = '<span class="text-dark">
        	<span class="mr-3 font-weight-bold">{Y}、{M}、{D}</span>年月日<br>
            <span class="mr-3 font-weight-bold">{Ts}</span>INT类型的UNIX时间戳<br>
            <span class="mr-3 font-weight-bold">{Id}</span>文章ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+文章ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+文章ID<br>
        </span>
        ';
        $this->BuildObj->Arr = array(
            array(
                'Title' => '核心设置',
                'Form' => array(
                    array('Name' =>'Name', 'Desc' => '分类名称',  'Type' => 'input', 'Value' => $CateRs['Name'], 'Required' => 1, 'Col' => 4),
                    array('Name' =>'NameEn', 'Desc' => '英文别名',  'Type' => 'input', 'Value' => $CateRs['NameEn'], 'Required' => 0, 'Col' => 2),
                    array('Name' =>'ModelId', 'Desc' => '内容模型',  'Type' => 'select', 'Data' => $ModelKV, 'Value' => $CateRs['ModelId'], 'Disabled' => 1, 'Col' => 3),
                    array('Name' =>'Attr', 'Desc' => '属性',  'Type' => 'checkbox', 'Data' => $AttrArr, 'Value' => implode('|', $AttrValArr), 'Required' => 1, 'Col' => 3),
                    array('Name' =>'Pic', 'Desc' => '分类图片',  'Type' => 'upload', 'Value' => $CateRs['Pic'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'SeoTitle', 'Desc' => 'SEO标题',  'Type' => 'input', 'Value' => $CateRs['SeoTitle'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Keywords', 'Desc' => '关键字',  'Type' => 'input', 'Value' => $CateRs['Keywords'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Description', 'Desc' => '分类描述',  'Type' => 'textarea', 'Value' => $CateRs['Description'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'UserLevel', 'Desc' => '浏览权限',  'Type' => 'select', 'Data' => $GroupUserKv, 'Value' => $CateRs['UserLevel'], 'Required' => 1, 'Col' => 12),
                    array('Name' =>'LinkUrl', 'Desc' => '外链地址',  'Type' => 'hidden', 'Value' => $CateRs['LinkUrl'], 'Required' => 0, 'Col' => 12),
                )),
            array(
                'Title' => '高级设置',
                'Form' => array(
                    array('Name' =>'TempList', 'Desc' => '列表模板',  'Type' => 'select', 'Data' => $TempList, 'Value' => $CateRs['TempList'], 'Required' => 0, 'Col' => 6),
                    array('Name' =>'TempDetail', 'Desc' => '详情模板',  'Type' => 'select', 'Data' => $TempDetail, 'Value' => $CateRs['TempDetail'], 'Required' => 0, 'Col' => 6),
                    array('Name' =>'SyncSubColumn', 'Desc' => '同步子分类模板',  'Type' => 'radio', 'Data' => $this->IsArr, 'Value' => 2, 'Required' => 0, 'Col' => 6),
                    /* array('Name' =>'UrlList', 'Desc' => '列表命名规则',  'Type' => 'input', 'Value' => $CateRs['UrlList'], 'Required' => 0, 'Col' => 6, 'Help' => '撒旦法安防'),
                    array('Name' =>'UrlDetail', 'Desc' => '详情命名规则',  'Type' => 'input', 'Value' => $CateRs['UrlDetail'], 'Required' => 0, 'Col' => 6),
                    array('Desc' => '规则说明',  'Type' => 'html', 'Value' => $UrlListDesc, 'Required' => 1, 'Col' => 6),
                    array('Desc' => '规则说明',  'Type' => 'html', 'Value' => $UrlDetailDesc, 'Required' => 1, 'Col' => 6), */
                )),
            array(
                'Title' => '分类内容',
                'Form' => array(
                    array('Name' =>'Content', 'Desc' => '分类内容',  'Type' => 'editor', 'Value' => $CateRs['Content'], 'Required' => 0, 'Col' => 12),
                )),
        );
        foreach($FieldArr as $v){
            $DataArr = array();
            if(!empty($v['Data'])){
                $Data = explode('|', $v['Data']);
                foreach($Data as $sv) $DataArr[$sv] = $sv;
            }
            $Row = in_array($v['Type'], array('editor', 'textarea')) ? 12 : 3;
            $this->BuildObj->Arr[0]['Form'][] =  array('Name' => $v['Name'], 'Desc' => $v['Comment'],  'Type' => $v['Type'], 'Data' => $DataArr, 'Value' => $CateRs[$v['Name']], 'Required' => $v['NotNull'], 'Col' => $Row);
        }
        $this->PageTitle2 = $this->BuildObj->FormMultipleTitle();
        $this->BuildObj->FormMultiple('post', 'form-row');
        $tmp['ModelTempKV'] = $ModelTempKV;
        $this->LoadView('admin/category/edit', $tmp);
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('CateId'))) $this->Err(1001);
        $CateRs = $this->CategoryObj->getOne($_GET['CateId']);
        if(empty($CateRs)) $this->Err(1003);
        $HaveSub = $this->CategoryObj->SetCond(array('PCateId' => $CateRs['CateId']))->SetField('COUNT(*) AS c')->ExecSelectOne();
        if($HaveSub['c'] > 0) $this->Err(1044);
        $ModelRs = $this->Sys_modelObj->getOne($CateRs['ModelId']);
        if($ModelRs !== false){            
            $HaveDetail = $this->Sys_modelObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('CateId' => $CateRs['CateId']))->SetField('COUNT(*) AS c')->ExecSelectOne();
            if($HaveDetail['c'] > 0) $this->Err(1045);
        }        
        $Ret = $this->CategoryObj->SetCond(array('CateId' => $CateRs['CateId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->CategoryObj->cleanList();
        $this->CategoryObj->clean($CateRs['CateId']);
        unset($_GET['CateId']);
        $this->Jump(array('admin', 'category', 'index'));
    }
    
    public function move_Action(){ //移动
        if(!$this->VeriObj->VeriPara($_GET, array('CateId'))) $this->Err(1001);
        $CateRs = $this->CategoryObj->getOne($_GET['CateId']);
        if(empty($CateRs)) $this->Err(1003);
        $this->CategoryObj->getAllCateId($CateRs['CateId'], -99);
        if(!empty($_POST)){
            $PCateId = intval($_POST['PCateId']);
            $ModelId = 1;
            $TCateId = 0;
            if(!empty($PCateId)){
                $Rs = $this->CategoryObj->getOne($PCateId);
                $TCateId = ($Rs['TCateId'] == 0) ? $Rs['CateId'] : $Rs['TCateId'];
                if(empty($Rs)) $this->Err(1001);
            }
            //if(!$this->VeriObj->VeriPara($_POST, array('CateId'))) $this->Err(1001);
            $this->CategoryObj->getTreeSelectArr($CateRs['CateId']);
            if(in_array($PCateId, $this->CategoryObj->CateTreeSelectArr)) $this->Err(1046);
            try{
                DB::$s_db_obj->beginTransaction();
                $this->CategoryObj->SetCond(array('CateId' => $this->CategoryObj->AllSubCateIdArr))->SetUpdate(array('TCateId' => $CateRs['CateId']))->ExecUpdate();
                $this->CategoryObj->SetCond(array('CateId' => $CateRs['CateId']))->SetUpdate(array('PCateId' => $PCateId, 'TCateId' => $TCateId))->ExecUpdate();
                
                DB::$s_db_obj->commit();
            }catch(PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->Err(1002);
            }

            $this->CategoryObj->cleanList();
            foreach($this->CategoryObj->AllSubCateIdArr as $v){
                $this->CategoryObj->clean($v);
            }            
            unset($_GET['CateId']);
            $this->Jump(array('admin', 'category', 'index'));
        }
        $this->CategoryObj->getTreeSelectHtml($CateRs['CateId']);       
        $tmp['CateRs'] = $CateRs;        
        $this->LoadView('admin/category/move', $tmp);
    }
    
    
}