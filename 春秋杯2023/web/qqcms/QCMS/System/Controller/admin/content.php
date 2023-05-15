<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Content extends ControllersAdmin {

    public function index_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('ModelId'))) $this->Err(1001);
        $ModelRs = $this->Sys_modelObj->getOne($_GET['ModelId']);
        if(empty($ModelRs)) $this->Err(1052);
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array('IsDelete' => 2);
        
        if(!empty($_GET['CateId'])){
            $this->CategoryObj->getAllCateId($_GET['CateId'], $ModelRs['ModelId']);
            $CondArr['CateId'] = $this->CategoryObj->AllSubCateIdArr;
        }
        if(!empty($_GET['State'])) $CondArr['State'] = $_GET['State'];
        if(!empty($_GET['Title'])) $CondArr['Title LIKE'] = $_GET['Title'];
        $Arr = $this->Sys_modelObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Sort' => 'ASC', 'Id' => 'Desc'))->ExecSelectAll($Count);
        $CateArr = $this->CategoryObj->SetCond(array('CateId' => array_column($Arr, 'CateId')))->SetField('CateId, Name')->ExecSelect();
        $CateKV = array_column($CateArr, 'Name', 'CateId');

        $UserArr = $this->UserObj->SetCond(array('UserId' => array_column($Arr, 'UserId')))->ExecSelect();
        $UserKv = array_column($UserArr, 'NickName', 'UserId');

        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKv = array(0 => '开放浏览');
        foreach(array_column($GroupUserArr, 'Name', 'GroupUserId') as $k => $v) $GroupUserKv[$k] = $v;

        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['ModelId'] = $ModelRs['ModelId'];
            $GET['CateId'] = $v['CateId'];
            $GET['Id'] = $v['Id'];
            $Attr = array();
            if($v['IsLink'] == 1) $Attr[] = '外链';
            if($v['IsSpuerRec'] == 1) $Attr[] = '特推';
            if($v['IsHeadlines'] == 1) $Attr[] = '头条';
            if($v['IsRec'] == 1) $Attr[] = '推荐';
            if($v['IsPic'] == 1) $Attr[] = '图片';
            $AttrStr = empty($Attr) ? '' : '<span class="px-2 text-danger" style="font-size:.6rem;">[ '.implode(' ', $Attr).' ]</span>';
            $Arr[$k]['TsUpdateView'] = date('Y-m-d H:i', $v['TsUpdate']);
            $Arr[$k]['CateName'] = '<a class="btn btn-primary btn-outline btn-sm" href="'.$this->CommonObj->Url(array('admin', 'content', 'index')).'?'.http_build_query($GET).'">'.$CateKV[$v['CateId']].'</a>';
            $Arr[$k]['UserLevelView'] = '<span class="text-muted ">'.$GroupUserKv[$v['UserLevel']].'</span>';
            $Arr[$k]['NickName'] = $UserKv[$v['UserId']];
            $Arr[$k]['StateView'] = $this->StateArr[$v['State']];
            $Arr[$k]['TitleView'] = '<span class="'.($v['IsBold'] == 2 ? '' : 'font-weight-bold').'">'.$v['Title'].'</span>'.$AttrStr;
            $Arr[$k]['BtnArr'] = array(
                array('Desc' => '预览', 'Color' => 'success', 'Link' => $this->createUrl('detail', $v['Id'], $v['PinYin'], $v['PY'], $v['TsUpdate']), 'IsBlank' => 1),
            );
            if($ModelRs['KeyName'] == 'album'){
                $Arr[$k]['BtnArr'][] = array('Desc' => '照片管理', 'Color' => 'success', 'Link' => $this->CommonObj->Url(array('admin', 'content', 'photos')), 'Para' => $GET);
            }
        }
        
        $KeyArr = array(
            'Id' => array('Name' => '全选', 'Td' => 'th', 'Type' => 'CheckBox'),
            //'Id' => array('Name' => 'ID', 'Td' => 'th'),
            'TitleView' => array('Name' => '标题', 'Td' => 'th'),
            'CateName' => array('Name' => '分类名', 'Td' => 'th'),
            'ReadNum' => array('Name' => '浏览数', 'Td' => 'th'),
            'UserLevelView' => array('Name' => '权限', 'Td' => 'th'),
            'State' => array('Name' => '状态', 'Type' => 'Switch', 'Td' => 'th'),
            'TsUpdateView' => array('Name' => '更新时间', 'Td' => 'th'),
            'NickName' => array('Name' => '发布人', 'Td' => 'th'),
        );
        $this->BuildObj->TableTopBtnArr = array(
            array('Name' => 'Recycle', 'Desc' => '回收站', 'Class' => 'default', 'Link' => $this->CommonObj->Url(array('admin', 'content', 'recovery')).'?'.http_build_query($_GET))
        );
        $this->BuildObj->TableFooterBtnArr = array(
            array('Name' => 'ContentState1Btn', 'Desc' => '批量发布', 'Class' => 'primary'),
            array('Name' => 'ContentState2Btn', 'Desc' => '取消发布', 'Class' => 'primary'),
            array('Name' => 'ContentbatchMoveBtn', 'Desc' => '批量移动', 'Class' => 'primary'),
            array('Name' => 'ContentbatchDel1Btn', 'Desc' => '批量删除', 'Class' => 'primary'),
            array('Name' => 'ContentbatchAttrAddBtn', 'Desc' => '增加属性', 'Class' => 'primary'),
            array('Name' => 'ContentbatchAttrDelBtn', 'Desc' => '删除属性', 'Class' => 'primary'),
            /* array('Name' => 'ContentbatchSR1Btn', 'Desc' => '批量特推', 'Class' => 'primary'),
            array('Name' => 'ContentbatchSR2Btn', 'Desc' => '取消特推', 'Class' => 'primary'),
            array('Name' => 'ContentbatchHL1Btn', 'Desc' => '批量头条', 'Class' => 'primary'),
            array('Name' => 'ContentbatchHL2Btn', 'Desc' => '取消头条', 'Class' => 'primary'),
            array('Name' => 'ContentbatchRE1Btn', 'Desc' => '批量推荐', 'Class' => 'primary'),
            array('Name' => 'ContentbatchRE2Btn', 'Desc' => '取消推荐', 'Class' => 'primary'), */
        );

        $this->BuildObj->PrimaryKey = 'Id';
        //$this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        $this->BuildObj->Js = 'var ChangeStateUrl="'.$this->CommonObj->Url(array('admin', 'api', 'contentState')).'";';
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        
        $this->CategoryObj->CateSelectId = $_GET['CateId'];
        $this->CategoryObj->getTreeModelSelectHtml($ModelRs['ModelId']);
        $CateHtml = '<label for="Input_CateId" class="mb-1">分类</label><select class="form-control" name="CateId" id="Input_CateId" >';
        $CateHtml .= '<option value="" >请选择分类</option>';
        $CateHtml .= $this->CategoryObj->CateTreeModelSelectHtml;
        $CateHtml .= '</select>';
        $this->BuildObj->Arr = array(            
            array('Name' =>'CateId', 'Desc' => $CateHtml,  'Type' => 'diy', 'Value' => '', 'Required' => 0, 'Col' => 12),
            array('Name' =>'State', 'Desc' => '状态',  'Type' => 'select', 'Data' => $this->OpenArr, 'Value' => $_GET['State'], 'Required' => 0, 'Col' => 12),
            array('Name' =>'Title', 'Desc' => '标题',  'Type' => 'text', 'Value' => $_GET['Title'], 'Required' => 0, 'Col' => 12),
            array('Name' =>'ModelId', 'Desc' => '模型ID',  'Type' => 'hidden', 'Value' => $ModelRs['ModelId'], 'Required' => 0, 'Col' => 12),
        );
        $this->BuildObj->Form('get', 'form-inline');
        $this->HeadHtml = $this->BuildObj->Html;
        $tmp['CateHtml'] = $this->CategoryObj->CateTreeModelSelectHtml;
        $this->LoadView('admin/content/list', $tmp);
    }

    public function add_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('ModelId'))) $this->Err(1001);
        $ModelRs = $this->Sys_modelObj->getOne($_GET['ModelId']);
        $FieldArr = empty($ModelRs['FieldJson']) ? array() : json_decode($ModelRs['FieldJson'], true);
        
        if(empty($ModelRs)) $this->Err(1001);
        $Ts = time();
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Title', 'CateId'))) $this->Err(1001);
            try{
                DB::$s_db_obj->beginTransaction();
                $State = isset($_POST['Attr']['State']) ? 1 : 2;
                $IsPost = isset($_POST['Attr']['IsPost']) ? 1 : 2;
                $IsLink = isset($_POST['Attr']['IsLink']) ? 1 : 2;
                $IsBold = isset($_POST['Attr']['IsBold']) ? 1 : 2;
                $IsPic = !empty($_POST['Pic']) ? 1 : 2;
                $IsSpuerRec = isset($_POST['Attr2']['IsSpuerRec']) ? 1 : 2;
                $IsHeadlines = isset($_POST['Attr2']['IsHeadlines']) ? 1 : 2;
                $IsRec = isset($_POST['Attr2']['IsRec']) ? 1 : 2;
                $this->TableObj->SetInsert(array('ModelId' => $ModelRs['ModelId']))->ExecInsert();
                $InsertId = $this->TableObj->last_insert_id();
                $InsetArr = array(
                    'Id' => $InsertId,
                    'CateId' => intval($_POST['CateId']),
                    'Title' => trim($_POST['Title']),
                    'STitle' => trim($_POST['STitle']),
                    'Tag' => trim($_POST['Tag']),
                    'Pic' => trim($_POST['Pic']),
                    'Source' => trim($_POST['Source']),
                    'Author' => trim($_POST['Author']),
                    'Sort' => intval($_POST['Sort']),
                    'Keywords' => trim($_POST['Keywords']),
                    'Description' => trim($_POST['Description']),
                    'Summary' =>trim($_POST['Summary']),
                    'TsAdd' => $Ts,
                    'TsUpdate' => empty($_POST['TsUpdate']) ? $Ts : strtotime($_POST['TsUpdate']),
                    'ReadNum' => intval($_POST['ReadNum']),
                    'Coins' => intval($_POST['Coins']),
                    'Money' => intval($_POST['Money']),
                    'UserLevel' => intval($_POST['UserLevel']),
                    'Good' => intval($_POST['Good']),
                    'Bad' => intval($_POST['Bad']),
                    'Color' => trim($_POST['Color']),
                    'UserId' => $this->LoginUserRs['UserId'],
                    'State' => $State,
                    'Content' => trim($_POST['Content']),
                    'IsPost' => $IsPost,
                    'IsLink' => $IsLink,
                    'IsBold' => $IsBold,
                    'IsPic' => $IsPic,
                    'IsSpuerRec' => $IsSpuerRec,
                    'IsHeadlines' => $IsHeadlines,
                    'IsRec' => $IsRec,
                    'PinYin' => $this->PinYinObj->str2pys(trim($_POST['Title'])),
                    'PY' => $this->PinYinObj->str2py(trim($_POST['Title'])),
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
                    $InsetArr[$v['Name']] = $_POST[$v['Name']];                                       
                }                     
                $this->Sys_modelObj->SetTbName('table_'.$ModelRs['KeyName'])->SetInsert($InsetArr)->ExecInsert();
                $this->TagObj->RunUpdate($InsetArr['Tag'], '', $InsertId, $ModelRs['ModelId']);
                if($ModelRs['KeyName'] == 'album'){
                    $this->PhotosObj->SetInsert(array('Id' => $InsertId, 'Photos' => json_encode(array())))->ExecInsert();
                }
                if(!empty($_POST['FilePaths'])){
                    $FilePathArr = explode('|', $_POST['FilePaths']);
                    $this->FileObj->SetCond(array('Img' => $FilePathArr))->SetUpdate(array('FType' => 2, 'IndexId' => $InsertId))->ExecUpdate();
                }
                DB::$s_db_obj->commit();
            }catch (PDOException $e){
                DB::$s_db_obj->rollBack();                
                $this->Err(1002);
            }
            $this->Jump(array('admin', 'content', 'index'));
        }

        $this->CategoryObj->getTreeModelSelectHtml($ModelRs['ModelId']);
        $CateHtml = '<label for="Input_CateId" class="mb-1">分类<span class="text-danger ml-2" style="font-weight: 900;">*</span></label><select class="form-control" name="CateId" id="Input_CateId" required="required">';
        $CateHtml .= '<option value="" >请选择分类</option>';
        $CateHtml .= $this->CategoryObj->CateTreeModelSelectHtml;
        $CateHtml .= '</select>';

        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKv = array(0 => '开放浏览');
        foreach(array_column($GroupUserArr, 'Name', 'GroupUserId') as $k => $v) $GroupUserKv[$k] = $v;

        $AttrValArr = array('State', 'IsPost');
        $AttrArr = array('State' => '发布', 'IsPost' => '评论', 'IsLink' => '外链', 'IsBold' => '加粗');

        $AttrArr2 = array('IsSpuerRec' => '特推', 'IsHeadlines' => '头条', 'IsRec' => '推荐');
        $this->BuildObj->Arr = array(
            array(
                'Title' => '基本信息',
                'Form' => array(
                    array('Name' =>'CateId', 'Desc' => $CateHtml,  'Type' => 'diy', 'Value' => '', 'Required' => 1, 'Col' => 12),
                    array('Name' =>'Title', 'Desc' => '标题',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
                    array('Name' =>'STitle', 'Desc' => '短标题',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Attr', 'Desc' => '属性',  'Type' => 'checkbox', 'Data' => $AttrArr, 'Value' => implode('|', $AttrValArr), 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Pic', 'Desc' => '图片',  'Type' => 'upload', 'Value' => '', 'Required' => 0, 'Col' => 6),
                    array('Name' =>'Tag', 'Desc' => '标签(用,分割)',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 6),
                    array('Name' =>'Content', 'Desc' => '内容详情',  'Type' => 'editor', 'Value' => '', 'Required' => 0, 'Col' => 12),
                    array('Name' =>'LinkUrl', 'Desc' => '外链地址',  'Type' => 'hidden', 'Value' => '', 'Required' => 0, 'Col' => 12),
                )
            ),
            array(
                'Title' => '扩展信息',
                'Form' => array(
                    array('Name' =>'UserLevel', 'Desc' => '浏览权限',  'Type' => 'select', 'Data' => $GroupUserKv, 'Value' => '0', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'TsUpdate', 'Desc' => '发布时间',  'Type' => 'input', 'Value' => date('Y-m-d H:i:s'), 'Required' => 0, 'Col' => 3),
                    array('Name' =>'ReadNum', 'Desc' => '浏览次数',  'Type' => 'input', 'Value' => '0', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Sort', 'Desc' => '排序',  'Type' => 'input', 'Value' => '99', 'Required' => 0, 'Col' => 3),

                    array('Name' =>'Source', 'Desc' => '来源',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Author', 'Desc' => '作者',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Color', 'Desc' => '颜色',  'Type' => 'color', 'Value' => '', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Attr2', 'Desc' => '附加属性',  'Type' => 'checkbox', 'Data' => $AttrArr2, 'Value' => '', 'Required' => 0, 'Col' => 3),

                    array('Name' =>'Coins', 'Desc' => '金币费用',  'Type' => 'input', 'Value' => '0', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Money', 'Desc' => '金钱费用',  'Type' => 'input', 'Value' => '0', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Good', 'Desc' => '好评数',  'Type' => 'input', 'Value' => '0', 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Bad', 'Desc' => '差评数',  'Type' => 'input', 'Value' => '0', 'Required' => 0, 'Col' => 3),


                    array('Name' =>'Keywords', 'Desc' => '关键字',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Description', 'Desc' => '描述',  'Type' => 'textarea', 'Value' => '', 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Summary', 'Desc' => '摘要',  'Type' => 'textarea', 'Value' => '', 'Required' => 0, 'Col' => 12),
                    array('Name' =>'FilePaths', 'Desc' => '记录上传资料',  'Type' => 'hidden', 'Value' => '', 'Required' => 0, 'Col' => 12),

                )
            ),
        );
        foreach($FieldArr as $v){            
            $DataArr = array();
            if(!empty($v['Data'])){
                $Data = explode('|', $v['Data']);
                foreach($Data as $sv) $DataArr[$sv] = $sv;
            }            
            $Row = in_array($v['Type'], array('editor', 'textarea')) ? 12 : 3;
            $this->BuildObj->Arr[0]['Form'][] = array('Name' => $v['Name'], 'Desc' => $v['Comment'],  'Type' => $v['Type'], 'Data' => $DataArr, 'Value' => $v['Content'], 'Required' => $v['NotNull'], 'Col' => $Row);
        }        
        
        $this->PageTitle2 = $this->BuildObj->FormMultipleTitle();
        $this->BuildObj->FormMultiple('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Id'))) $this->Err(1001);
        $TableRs = $this->TableObj->getOne($_GET['Id']);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        $FieldArr = empty($ModelRs['FieldJson']) ? array() : json_decode($ModelRs['FieldJson'], true);
        if(empty($ModelRs)) $this->Err(1001);
        $Rs = $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $_GET['Id']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        $Ts = time();

        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Title', 'CateId'))) $this->Err(1001);
            try{
                DB::$s_db_obj->beginTransaction();
                $State = isset($_POST['Attr']['State']) ? 1 : 2;
                $IsPost = isset($_POST['Attr']['IsPost']) ? 1 : 2;
                $IsLink = isset($_POST['Attr']['IsLink']) ? 1 : 2;
                $IsBold = isset($_POST['Attr']['IsBold']) ? 1 : 2;
                $IsPic = !empty($_POST['Pic']) ? 1 : 2;
                $IsSpuerRec = isset($_POST['Attr2']['IsSpuerRec']) ? 1 : 2;
                $IsHeadlines = isset($_POST['Attr2']['IsHeadlines']) ? 1 : 2;
                $IsRec = isset($_POST['Attr2']['IsRec']) ? 1 : 2;

                $InsetArr = array(
                    'CateId' => intval($_POST['CateId']),
                    'Title' => trim($_POST['Title']),
                    'STitle' => trim($_POST['STitle']),
                    'Tag' => trim($_POST['Tag']),
                    'Pic' => trim($_POST['Pic']),
                    'Source' => trim($_POST['Source']),
                    'Author' => trim($_POST['Author']),
                    'Sort' => intval($_POST['Sort']),
                    'Keywords' => trim($_POST['Keywords']),
                    'Description' => trim($_POST['Description']),
                    'Summary' => trim($_POST['Summary']),
                    //'TsAdd' => $Ts,
                    'TsUpdate' => empty($_POST['TsUpdate']) ? $Ts : strtotime($_POST['TsUpdate']),
                    'ReadNum' => intval($_POST['ReadNum']),
                    'Coins' => intval($_POST['Coins']),
                    'Money' => intval($_POST['Money']),
                    'UserLevel' => intval($_POST['UserLevel']),
                    'Good' => intval($_POST['Good']),
                    'Bad' => intval($_POST['Bad']),
                    'Color' => trim($_POST['Color']),
                    'UserId' => $this->LoginUserRs['UserId'],
                    'State' => $State,
                    'Content' => trim($_POST['Content']),
                    'IsPost' => $IsPost,
                    'IsLink' => $IsLink,
                    'IsBold' => $IsBold,
                    'IsPic' => $IsPic,
                    'IsSpuerRec' => $IsSpuerRec,
                    'IsHeadlines' => $IsHeadlines,
                    'IsRec' => $IsRec,
                    'PinYin' => $this->PinYinObj->str2pys(trim($_POST['Title'])),
                    'PY' => $this->PinYinObj->str2py(trim($_POST['Title'])),
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
                    $InsetArr[$v['Name']] = $_POST[$v['Name']];
                }   
                
                $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $Rs['Id']))->SetUpdate($InsetArr)->ExecUpdate();                
                
                $this->TagObj->RunUpdate($InsetArr['Tag'], $Rs['Tag'], $Rs['Id'], $ModelRs['ModelId']);
                
                if(!empty($_POST['FilePaths'])){
                    $FilePathArr = explode('|', $_POST['FilePaths']);
                    $this->FileObj->SetCond(array('Img' => $FilePathArr))->SetUpdate(array('FType' => 2, 'IndexId' => $Rs['Id']))->ExecUpdate();
                }
                DB::$s_db_obj->commit();
            }catch (PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->Err(1002);
            }
            $this->Jump(array('admin', 'content', 'index'));
        }

        $this->CategoryObj->CateSelectId = $Rs['CateId'];
        $this->CategoryObj->getTreeModelSelectHtml($ModelRs['ModelId']);
        $CateHtml = '<label for="Input_CateId" class="mb-1">分类<span class="text-danger ml-2" style="font-weight: 900;">*</span></label><select class="form-control" name="CateId" id="Input_CateId" required="required">';
        $CateHtml .= '<option value="" >请选择分类</option>';
        $CateHtml .= $this->CategoryObj->CateTreeModelSelectHtml;
        $CateHtml .= '</select>';

        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKv = array(0 => '开放浏览');
        foreach(array_column($GroupUserArr, 'Name', 'GroupUserId') as $k => $v) $GroupUserKv[$k] = $v;

        $AttrValArr = array();
        if($Rs['State'] == 1) $AttrValArr[] = 'State';
        if($Rs['IsPost'] == 1) $AttrValArr[] = 'IsPost';
        if($Rs['IsLink'] == 1) $AttrValArr[] = 'IsLink';
        if($Rs['IsBold'] == 1) $AttrValArr[] = 'IsBold';
        $AttrArr = array('State' => '发布', 'IsPost' => '评论', 'IsLink' => '外链', 'IsBold' => '加粗');
        $AttrValArr2 = array();
        if($Rs['IsSpuerRec'] == 1) $AttrValArr2[] = 'IsSpuerRec';
        if($Rs['IsHeadlines'] == 1) $AttrValArr2[] = 'IsHeadlines';
        if($Rs['IsRec'] == 1) $AttrValArr2[] = 'IsRec';
        $AttrArr2 = array('IsSpuerRec' => '特推', 'IsHeadlines' => '头条', 'IsRec' => '推荐');
        $this->BuildObj->Arr = array(
            array(
                'Title' => '基本信息',
                'Form' => array(
                    array('Name' =>'CateId', 'Desc' => $CateHtml,  'Type' => 'diy', 'Value' => $Rs['CateId'], 'Required' => 1, 'Col' => 12),
                    array('Name' =>'Title', 'Desc' => '标题',  'Type' => 'input', 'Value' => $Rs['Title'], 'Required' => 1, 'Col' => 6),
                    array('Name' =>'STitle', 'Desc' => '短标题',  'Type' => 'input', 'Value' => $Rs['STitle'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Attr', 'Desc' => '属性',  'Type' => 'checkbox', 'Data' => $AttrArr, 'Value' => implode('|', $AttrValArr), 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Pic', 'Desc' => '图片',  'Type' => 'upload', 'Value' => $Rs['Pic'], 'Required' => 0, 'Col' => 6),
                    array('Name' =>'Tag', 'Desc' => '标签(用,分割)',  'Type' => 'input', 'Value' => $Rs['Tag'], 'Required' => 0, 'Col' => 6),
                    array('Name' =>'Content', 'Desc' => '内容详情',  'Type' => 'editor', 'Value' => $Rs['Content'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'LinkUrl', 'Desc' => '外链地址',  'Type' => 'hidden', 'Value' => $Rs['LinkUrl'], 'Required' => 0, 'Col' => 12),
                )
            ),
            array(
                'Title' => '扩展信息',
                'Form' => array(
                    array('Name' =>'UserLevel', 'Desc' => '浏览权限',  'Type' => 'select', 'Data' => $GroupUserKv, 'Value' => $Rs['UserLevel'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'TsUpdate', 'Desc' => '发布时间',  'Type' => 'input', 'Value' => date('Y-m-d H:i:s', $Rs['TsUpdate']), 'Required' => 0, 'Col' => 3),
                    array('Name' =>'ReadNum', 'Desc' => '浏览次数',  'Type' => 'input', 'Value' => $Rs['ReadNum'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Sort', 'Desc' => '排序',  'Type' => 'input', 'Value' => $Rs['Sort'], 'Required' => 0, 'Col' => 3),

                    array('Name' =>'Source', 'Desc' => '来源',  'Type' => 'input', 'Value' => $Rs['Source'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Author', 'Desc' => '作者',  'Type' => 'input', 'Value' => $Rs['Author'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Color', 'Desc' => '颜色',  'Type' => 'color', 'Value' => $Rs['Color'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Attr2', 'Desc' => '附加属性',  'Type' => 'checkbox', 'Data' => $AttrArr2, 'Value' => implode('|', $AttrValArr2), 'Required' => 0, 'Col' => 3),

                    array('Name' =>'Coins', 'Desc' => '金币费用',  'Type' => 'input', 'Value' => $Rs['Coins'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Money', 'Desc' => '金钱费用',  'Type' => 'input', 'Value' => $Rs['Money'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Good', 'Desc' => '好评数',  'Type' => 'input', 'Value' => $Rs['Good'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Bad', 'Desc' => '差评数',  'Type' => 'input', 'Value' => $Rs['Bad'], 'Required' => 0, 'Col' => 3),


                    array('Name' =>'Keywords', 'Desc' => '关键字',  'Type' => 'input', 'Value' => $Rs['Keywords'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Description', 'Desc' => '描述',  'Type' => 'textarea', 'Value' => $Rs['Description'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Summary', 'Desc' => '摘要',  'Type' => 'textarea', 'Value' => $Rs['Summary'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'FilePaths', 'Desc' => '记录上传资料',  'Type' => 'hidden', 'Value' => '', 'Required' => 0, 'Col' => 12),
                )
            ),

        );
        foreach($FieldArr as $v){
            $DataArr = array();
            if(!empty($v['Data'])){
                $Data = explode('|', $v['Data']);
                foreach($Data as $sv) $DataArr[$sv] = $sv;
            }
            if($v['Type'] == 'datetime') $Rs[$v['Name']] = date('Y-m-d\TH:i');
            $Row = in_array($v['Type'], array('editor', 'textarea')) ? 12 : 3;
            $this->BuildObj->Arr[0]['Form'][] = array('Name' => $v['Name'], 'Desc' => $v['Comment'],  'Type' => $v['Type'], 'Data' => $DataArr, 'Value' => $Rs[$v['Name']], 'Required' => $v['NotNull'], 'Col' => $Row);
        }

        $this->PageTitle2 = $this->BuildObj->FormMultipleTitle();
        $this->BuildObj->FormMultiple('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Id'))) $this->Err(1001);
        $TableRs = $this->TableObj->getOne($_GET['Id']);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        if(empty($ModelRs)) $this->Err(1001);
        $Ts = time();
        $Rs = $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $_GET['Id']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        try{
            DB::$s_db_obj->beginTransaction();
            $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $Rs['Id']))->SetUpdate(array('IsDelete' => 1))->ExecUpdate();
            $this->TagObj->DeleteTag($TableRs['Id']);
            DB::$s_db_obj->commit();
        }catch(PDOException $e){
            DB::$s_db_obj->rollBack();
            $this->Err(1002);
        }
        $this->Jump(array('admin', 'content', 'index'));
    }

    public function recovery_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('ModelId'))) $this->Err(1001);
        $ModelRs = $this->Sys_modelObj->getOne($_GET['ModelId']);
        if(empty($ModelRs)) $this->Err(1052);
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array('IsDelete' => 1);

        $Arr = $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Sort' => 'ASC', 'Id' => 'Desc'))->ExecSelectAll($Count);
        $CateArr = $this->CategoryObj->SetCond(array('CateId' => array_column($Arr, 'CateId')))->SetField('CateId, Name')->ExecSelect();
        $CateKV = array_column($CateArr, 'Name', 'CateId');

        $UserArr = $this->UserObj->SetCond(array('UserId' => array_column($Arr, 'UserId')))->ExecSelect();
        $UserKv = array_column($UserArr, 'NickName', 'UserId');

        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKv = array(0 => '开放浏览');
        foreach(array_column($GroupUserArr, 'Name', 'GroupUserId') as $k => $v) $GroupUserKv[$k] = $v;

        foreach($Arr as $k => $v){
            $Attr = array();
            if($v['IsLink'] == 1) $Attr[] = '外链';
            if($v['IsSpuerRec'] == 1) $Attr[] = '特推';
            if($v['IsHeadlines'] == 1) $Attr[] = '头条';
            if($v['IsRec'] == 1) $Attr[] = '推荐';
            if($v['IsPic'] == 1) $Attr[] = '图片';
            $AttrStr = empty($Attr) ? '' : '<span class="px-2 text-danger" style="font-size:.6rem;">[ '.implode(' ', $Attr).' ]</span>';
            $Arr[$k]['TsUpdateView'] = date('Y-m-d H:i', $v['TsUpdate']);
            $Arr[$k]['CateName'] = '<a class="btn btn-primary btn-outline btn-sm" >'.$CateKV[$v['CateId']].'</a>';
            $Arr[$k]['UserLevelView'] = $GroupUserKv[$v['UserLevel']];
            $Arr[$k]['NickName'] = $UserKv[$v['UserId']];
            $Arr[$k]['StateView'] = $this->StateArr[$v['State']];
            $Arr[$k]['TitleView'] = '<span class="'.($v['IsBold'] == 2 ? '' : 'font-weight-bold').'">'.$v['Title'].'</span>'.$AttrStr;
            $Arr[$k]['BtnArr'] = array(
                array('Desc' => '查看', 'Color' => 'success', 'Link' => $this->CommonObj->Url(array('admin', 'content', 'view')) ),
                array('Desc' => '还原', 'Color' => 'primary', 'Link' => $this->CommonObj->Url(array('admin', 'content', 'restore')) ),
                //array('Name' => '彻底删除', 'Color' => 'danger', 'Link' => $this->CommonObj->Url(array('admin', 'content', 'view')) ),
            );
        }
        $KeyArr = array(
            'Id' => array('Name' => '全选', 'Td' => 'th', 'Type' => 'CheckBox'),
            'TitleView' => array('Name' => '标题', 'Td' => 'th'),
            'CateName' => array('Name' => '分类名', 'Td' => 'th'),
            'ReadNum' => array('Name' => '浏览数', 'Td' => 'th'),
            'UserLevelView' => array('Name' => '权限', 'Td' => 'th'),
            'StateView' => array('Name' => '状态', 'Td' => 'th'),
            'TsUpdateView' => array('Name' => '更新时间', 'Td' => 'th'),
            'NickName' => array('Name' => '发布人', 'Td' => 'th'),
        );
        $this->BuildObj->PrimaryKey = 'Id';
        $this->BuildObj->NameDel = '彻底删除';
        $this->BuildObj->LinkDel = $this->CommonObj->Url(array('admin', 'content', 'tDelete'));
        $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '返回', 'Link' => $this->CommonObj->Url(array('admin', 'content', 'index')).'?'.http_build_query($_GET), 'Class' => 'default'),
        );
        $this->BuildObj->TableFooterBtnArr = array(
            array('Name' => 'ContentbatchDel2Btn', 'Desc' => '批量还原', 'Class' => 'primary'), 
            array('Name' => 'ContentbatchDel3Btn', 'Desc' => '彻底删除', 'Class' => 'primary'),
        );
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }

    public function view_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Id'))) $this->Err(1001);
        $TableRs = $this->TableObj->getOne($_GET['Id']);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);        
        if(empty($ModelRs)) $this->Err(1001);
        $FieldArr = empty($ModelRs['FieldJson']) ? array() : json_decode($ModelRs['FieldJson'], true);
        $Rs = $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $_GET['Id']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);        
        $Ts = time();

        $this->CategoryObj->CateSelectId = $Rs['CateId'];
        $this->CategoryObj->getTreeModelSelectHtml($ModelRs['ModelId']);
        $CateHtml = '<label for="Input_CateId" class="mb-1">分类<span class="text-danger ml-2" style="font-weight: 900;">*</span></label><select class="form-control" name="CateId" id="Input_CateId" required="required">';
        $CateHtml .= '<option value="" >请选择分类</option>';
        $CateHtml .= $this->CategoryObj->CateTreeModelSelectHtml;
        $CateHtml .= '</select>';

        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKv = array(0 => '开放浏览');
        foreach(array_column($GroupUserArr, 'Name', 'GroupUserId') as $k => $v) $GroupUserKv[$k] = $v;

        $AttrValArr = array();
        if($Rs['State'] == 1) $AttrValArr[] = 'State';
        if($Rs['IsPost'] == 1) $AttrValArr[] = 'IsPost';
        if($Rs['IsLink'] == 1) $AttrValArr[] = 'IsLink';
        if($Rs['IsBold'] == 1) $AttrValArr[] = 'IsBold';
        $AttrArr = array('State' => '发布', 'IsPost' => '评论', 'IsLink' => '外链', 'IsBold' => '加粗');
        $AttrValArr2 = array();
        if($Rs['IsSpuerRec'] == 1) $AttrValArr2[] = 'IsSpuerRec';
        if($Rs['IsHeadlines'] == 1) $AttrValArr2[] = 'IsHeadlines';
        if($Rs['IsRec'] == 1) $AttrValArr2[] = 'IsRec';
        $AttrArr2 = array('IsSpuerRec' => '特推', 'IsHeadlines' => '头条', 'IsRec' => '推荐');
        $this->BuildObj->Arr = array(
            array(
                'Title' => '基本信息',
                'Form' => array(
                    array('Name' =>'CateId', 'Desc' => $CateHtml,  'Type' => 'diy', 'Value' => $Rs['CateId'], 'Required' => 1, 'Col' => 12),
                    array('Name' =>'Title', 'Desc' => '标题',  'Type' => 'input', 'Value' => $Rs['Title'], 'Required' => 1, 'Col' => 6),
                    array('Name' =>'STitle', 'Desc' => '短标题',  'Type' => 'input', 'Value' => $Rs['STitle'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Attr', 'Desc' => '属性',  'Type' => 'checkbox', 'Data' => $AttrArr, 'Value' => implode('|', $AttrValArr), 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Pic', 'Desc' => '图片',  'Type' => 'upload', 'Value' => $Rs['Pic'], 'Required' => 0, 'Col' => 6),
                    array('Name' =>'Tag', 'Desc' => '标签',  'Type' => 'input', 'Value' => $Rs['Tag'], 'Required' => 0, 'Col' => 6),
                    array('Name' =>'Content', 'Desc' => '内容详情',  'Type' => 'editor', 'Value' => $Rs['Content'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'LinkUrl', 'Desc' => '外链地址',  'Type' => 'hidden', 'Value' => $Rs['LinkUrl'], 'Required' => 0, 'Col' => 12),
                )
            ),
            array(
                'Title' => '扩展信息',
                'Form' => array(
                    array('Name' =>'UserLevel', 'Desc' => '浏览权限',  'Type' => 'select', 'Data' => $GroupUserKv, 'Value' => $Rs['UserLevel'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'TsUpdate', 'Desc' => '发布时间',  'Type' => 'input', 'Value' => date('Y-m-d H:i:s', $Rs['TsUpdate']), 'Required' => 0, 'Col' => 3),
                    array('Name' =>'ReadNum', 'Desc' => '浏览次数',  'Type' => 'input', 'Value' => $Rs['ReadNum'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Sort', 'Desc' => '排序',  'Type' => 'input', 'Value' => $Rs['Sort'], 'Required' => 0, 'Col' => 3),

                    array('Name' =>'Source', 'Desc' => '来源',  'Type' => 'input', 'Value' => $Rs['Source'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Author', 'Desc' => '作者',  'Type' => 'input', 'Value' => $Rs['Author'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Color', 'Desc' => '颜色',  'Type' => 'color', 'Value' => $Rs['Color'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Attr2', 'Desc' => '附加属性',  'Type' => 'checkbox', 'Data' => $AttrArr2, 'Value' => implode('|', $AttrValArr2), 'Required' => 0, 'Col' => 3),

                    array('Name' =>'Coins', 'Desc' => '金币费用',  'Type' => 'input', 'Value' => $Rs['Coins'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Money', 'Desc' => '金钱费用',  'Type' => 'input', 'Value' => $Rs['Money'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Good', 'Desc' => '好评数',  'Type' => 'input', 'Value' => $Rs['Good'], 'Required' => 0, 'Col' => 3),
                    array('Name' =>'Bad', 'Desc' => '差评数',  'Type' => 'input', 'Value' => $Rs['Bad'], 'Required' => 0, 'Col' => 3),


                    array('Name' =>'Keywords', 'Desc' => '关键字',  'Type' => 'input', 'Value' => $Rs['Keywords'], 'Required' => 0, 'Col' => 12),
                    array('Name' =>'Description', 'Desc' => '分类描述',  'Type' => 'textarea', 'Value' => $Rs['Description'], 'Required' => 0, 'Col' => 12),

                )
            ),

        );
        foreach($FieldArr as $v){
            $DataArr = array();
            if(!empty($v['Data'])){
                $Data = explode('|', $v['Data']);
                foreach($Data as $sv) $DataArr[$sv] = $sv;
            }
            if($v['Type'] == 'datetime') $Rs[$v['Name']] = date('Y-m-d\TH:i');
            $Row = in_array($v['Type'], array('editor', 'textarea')) ? 12 : 3;
            $this->BuildObj->Arr[0]['Form'][] = array('Name' => $v['Name'], 'Desc' => $v['Comment'],  'Type' => $v['Type'], 'Data' => $DataArr, 'Value' => $Rs[$v['Name']], 'Required' => $v['NotNull'], 'Col' => $Row);
        }
        $this->PageTitle2 = $this->BuildObj->FormMultipleTitle();
        $this->BuildObj->IsSubmit = false;
        $this->BuildObj->IsBack = true;
        $this->BuildObj->FormMultiple('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function restore_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Id'))) $this->Err(1001);
        $TableRs = $this->TableObj->getOne($_GET['Id']);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        if(empty($ModelRs)) $this->Err(1001);
        $Rs = $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $_GET['Id']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        $_GET['ModelId'] = $ModelRs['ModelId'];
        try{
            DB::$s_db_obj->beginTransaction();
            $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $Rs['Id']))->SetUpdate(array('IsDelete' => 2))->ExecUpdate();
            $this->TagObj->RunUpdate($Rs['Tag'], '', $Rs['Id'], $ModelRs['ModelId']);
            DB::$s_db_obj->commit();
        }catch(PDOException $e){
            DB::$s_db_obj->rollBack();
            $this->Err(1002);
        }

        $this->Jump(array('admin', 'content', 'recovery'));
    }

    public function tDelete_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Id'))) $this->Err(1001);
        $TableRs = $this->TableObj->getOne($_GET['Id']);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        $Rs = $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $TableRs['Id']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        $_GET['ModelId'] = $ModelRs['ModelId'];
        try{
            DB::$s_db_obj->beginTransaction();
            $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $Rs['Id']))->ExecDelete();
            $this->TableObj->SetCond(array('Id' => $Rs['Id']))->ExecDelete();
            if($ModelRs['KeyName'] == 'album'){ //相册表删除
                $this->PhotosObj->SetCond(array('Id' => $Rs['Id']))->ExecDelete();
            }
            $this->FileObj->SetCond(array('FType' => 2, 'IndexId' => $Rs['Id']))->SetUpdate(array('IsDel' => 1))->ExecUpdate();
            
            DB::$s_db_obj->commit();
        }catch (PDOException $e){
            DB::$s_db_obj->rollBack();
            $this->Err(1002);
        }

        $this->Jump(array('admin', 'content', 'recovery'));
    }
    
    public function photos_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Id'))) $this->Err(1001);
        $TableRs = $this->TableObj->getOne($_GET['Id']);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        if($ModelRs['KeyName'] != 'album') $this->Err(1048);
        $Rs = $this->PhotosObj->SetCond(array('Id' => $TableRs['Id']))->ExecSelectOne();
        $Photos = empty($Rs['Photos']) ? array() : json_decode($Rs['Photos'], true);
        
        if(!empty($_FILES)){
            try{
                DB::$s_db_obj->beginTransaction();
                foreach($_FILES as $File){
                    $Ret = self::p_upload($File);
                    if($Ret['Code'] == 0) {
                        $Photos[] = array('Name' => $File['name'], 'Path' => $Ret['Url'], 'Size' => $File['size']);
                        $ext = substr ( strrchr ( $File['name'], '.' ), 1 );
                        $this->FileObj->SetInsert(array(
                            'UserId' => $this->LoginUserRs['UserId'],
                            'Name' => $File['name'],
                            'Img' => $Ret['Url'],
                            'Size' => $File['size'],
                            'Ext' => $ext,
                            'Ts' => time(),
                            'FType' => 2,
                            'IndexId' => $TableRs['Id'],
                        ))->ExecInsert();
                    }
                }
                $this->PhotosObj->SetInsert(array('Id' => $TableRs['Id'], 'Photos' => json_encode($Photos)))->ExecReplace();
                DB::$s_db_obj->commit();
            }catch (PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->ApiErr(1002);
            }
            foreach($Photos as $k => $v) {
                $Photos[$k]['SizeView'] = $this->CommonObj->Size($v['Size']);
            }
            $this->ApiSuccess($Photos);
            
        }
        foreach($Photos as $k => $v) {
            $Photos[$k]['SizeView'] = $this->CommonObj->Size($v['Size']);
        }
        $tmp['Photos'] = $Photos;
        $this->LoadView('admin/content/photos', $tmp);
    }
    
    public function photoSort_Action(){ //排序
        if(!$this->VeriObj->VeriPara($_GET, array('Id'))) $this->ApiErr(1001);
        $PhotoIndex = intval($_POST['PhotoIndex']);
        $TableRs = $this->TableObj->getOne($_GET['Id']);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        if($ModelRs['KeyName'] != 'album') $this->ApiErr(1048);
        $Rs = $this->PhotosObj->SetCond(array('Id' => $TableRs['Id']))->ExecSelectOne();
        $Photos = empty($Rs['Photos']) ? array() : json_decode($Rs['Photos'], true);
        //if(!$this->VeriObj->VeriPara($_POST, array('oldIndex', 'newIndex'))) $this->ApiErr(1001);
        $OldIndex = intval($_POST['oldIndex']);
        $NewIndex = intval($_POST['newIndex']);
        $Old = $Photos[$OldIndex]; 
        array_splice($Photos, $OldIndex, 1);
        $NewPhotoArr = array();
        foreach($Photos as $k => $v){
            if($k == $_POST['newIndex']){
                $NewPhotoArr[] = $Old;
            }
            $NewPhotoArr[] = $v;
        }
        if(count($NewPhotoArr) < $NewIndex+1){
            $NewPhotoArr[] = $Old;
        }
        $Ret = $this->PhotosObj->SetInsert(array('Id' => $TableRs['Id'], 'Photos' => json_encode($NewPhotoArr)))->ExecReplace();
        if($Ret === false) $this->ApiErr(1002);
        foreach($NewPhotoArr as $k => $v) {
            $NewPhotoArr[$k]['SizeView'] = $this->CommonObj->Size($v['Size']);
        }
        $this->ApiSuccess($NewPhotoArr);
    }
    
    public function photoDel_Action(){ //删除
        if(!$this->VeriObj->VeriPara($_GET, array('Id'))) $this->ApiErr(1001);
        //$PhotoIndex = intval($_POST['PhotoIndex']);
        $TableRs = $this->TableObj->getOne($_GET['Id']);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        if($ModelRs['KeyName'] != 'album') $this->ApiErr(1048);
        $Rs = $this->PhotosObj->SetCond(array('Id' => $TableRs['Id']))->ExecSelectOne();
        $Photos = empty($Rs['Photos']) ? array() : json_decode($Rs['Photos'], true);
        $NewPhotos = $DelPhotos = array();
        $PhotoIndexArr = explode('|', $_POST['PhotoIndex']);
        foreach($Photos as $k => $v){
            if(in_array($k, $PhotoIndexArr)){
                $DelPhotos[] = $v;
            }else{
                $NewPhotos[] = $v;                
            }
        }        
        
        try{
            DB::$s_db_obj->beginTransaction();
            $this->PhotosObj->SetInsert(array('Id' => $TableRs['Id'], 'Photos' => json_encode($NewPhotos)))->ExecReplace();
            $this->FileObj->SetCond(array('FType' => 2, 'IndexId' => $TableRs['Id'], 'Img' => array_column($DelPhotos, 'Path')))->SetUpdate(array('IsDel' => 1))->ExecUpdate();
            DB::$s_db_obj->commit();
        }catch (PDOException $e){
            DB::$s_db_obj->rollBack();
            $this->ApiErr(1002);
        }
        foreach($NewPhotos as $k => $v) {
            $NewPhotos[$k]['SizeView'] = $this->CommonObj->Size($v['Size']);
        }
        $this->ApiSuccess($NewPhotos);
    }

}