<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Tag extends ControllersAdmin {
    
    public function index_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        $Arr = $this->TagObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Total' => 'DESC', 'TagId' => 'ASC'))->ExecSelectAll($Count);
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['TagId'] = $v['TagId'];
            $Arr[$k]['TotalView'] = '<a class="text-primary" href="'.$this->CommonObj->Url(array('admin', 'tag', 'list')).'?'.http_build_query($GET).'">'.$v['Total'].'</a>';
            $Arr[$k]['TsAddView'] = date('Y-m-d H:i:s', $v['TsAdd']);
        }
        $KeyArr = array(
            'TagId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '名字', 'Td' => 'th'),
            'ReadNum' => array('Name' => '点击数', 'Td' => 'th'),
            'TotalView' => array('Name' => '文档数', 'Td' => 'th'),
            'TsAddView' => array('Name' => '添加时间', 'Td' => 'th'),
        );
        $this->BuildObj->PrimaryKey = 'TagId';
        $this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function list_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('TagId'))) $this->Err(1001);
        
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array('TagId' => $_GET['TagId']);
        $Arr = $this->Tag_mapObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('TagMapId' => 'DESC'))->ExecSelectAll($Count);
        $ModelArr = $this->Sys_modelObj->SetCond(array('ModelId' => array_column($Arr, 'ModelId')))->ExecSelect();
        $ModelKV = array_column($ModelArr, 'Name', 'ModelId');
        $DataArr = array();
        foreach($ModelArr as $v){
            $ContentArr = $this->Sys_modelObj->SetTbName('table_'.$v['KeyName'])->SetCond(array('Id' => array_column($Arr, 'TableId')))->SetIsDebug(0)->ExecSelect();
            foreach($ContentArr as $sv){
                $DataArr[] = array(
                    'Id' => $sv['Id'], 
                    'Title' => $sv['Title'], 
                    'ModelId' => $v['ModelId'], 
                    'ReadNum' => $sv['ReadNum'], 
                    'CateId' => $sv['CateId'],
                    'TsUpdate' => $sv['TsUpdate'],
                    'UserId' => $sv['UserId'],
                    'UserLevel' => $sv['UserLevel'],
                    'IsLink' => $sv['IsLink'],
                    'IsSpuerRec' => $sv['IsSpuerRec'],
                    'IsHeadlines' => $v['IsHeadlines'],
                    'IsRec' => $sv['IsRec'],
                    'IsPic' => $sv['IsPic'],
                    'State' => $sv['State'],
                );
            }
        }
        $CateArr = $this->CategoryObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'CateId');
        $UserArr = $this->UserObj->SetCond(array('UserId' => array_column($DataArr, 'UserId')))->ExecSelect();
        $UserKv = array_column($UserArr, 'NickName', 'UserId');
        $GroupUserArr = $this->Group_userObj->getList();
        $GroupUserKv = array(0 => '开放浏览');
        foreach(array_column($GroupUserArr, 'Name', 'GroupUserId') as $k => $v) $GroupUserKv[$k] = $v;
        //var_dump($CateKV);exit;
        foreach($DataArr as $k => $v){
            $Attr = array();
            if($v['IsLink'] == 1) $Attr[] = '外链';
            if($v['IsSpuerRec'] == 1) $Attr[] = '特推';
            if($v['IsHeadlines'] == 1) $Attr[] = '头条';
            if($v['IsRec'] == 1) $Attr[] = '推荐';
            if($v['IsPic'] == 1) $Attr[] = '图片';
            $AttrStr = empty($Attr) ? '' : '<span class="px-2 text-danger" style="font-size:.6rem;">[ '.implode(' ', $Attr).' ]</span>';
            $DataArr[$k]['TitleView'] = '<span class="'.($v['IsBold'] == 2 ? '' : 'font-weight-bold').'">'.$v['Title'].'</span>'.$AttrStr;
            $DataArr[$k]['ModelView'] = $ModelKV[$v['ModelId']];
            $DataArr[$k]['CateName'] = $CateKV[$v['CateId']];
            $DataArr[$k]['TsUpdateView'] = date('Y-m-d H:i', $v['TsUpdate']);
            $DataArr[$k]['NickName'] = $UserKv[$v['UserId']];
            $DataArr[$k]['UserLevelView'] = $GroupUserKv[$v['UserLevel']];
            $DataArr[$k]['StateView'] = $this->StateArr[$v['State']];
        }
        $KeyArr = array(
            'Id' => array('Name' => 'ID', 'Td' => 'th'),
            'TitleView' => array('Name' => '标题', 'Td' => 'th'),
            'CateName' => array('Name' => '分类名', 'Td' => 'th'),
            
            
            'ReadNum' => array('Name' => '浏览数', 'Td' => 'th'),
            'UserLevelView' => array('Name' => '权限', 'Td' => 'th'),
            'StateView' => array('Name' => '状态', 'Td' => 'th'),
            'TsUpdateView' => array('Name' => '更新时间', 'Td' => 'th'),
            'NickName' => array('Name' => '发布人', 'Td' => 'th'),
            'ModelView' => array('Name' => '模型', 'Td' => 'th'),
        );
        $this->BuildObj->PrimaryKey = 'TagId';
        $this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '返回', 'Link' => $this->CommonObj->Url(array('admin', 'tag', 'index')).'?'.http_build_query($_GET), 'Class' => 'default'),
        );
        $tmp['Table'] = $this->BuildObj->Table($DataArr, $KeyArr, $PageBar, 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
}