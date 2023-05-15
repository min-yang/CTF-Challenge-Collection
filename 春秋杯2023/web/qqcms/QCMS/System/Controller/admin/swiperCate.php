<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class SwiperCate extends ControllersAdmin {
    
    public function index_Action(){
        $Arr = $this->Swiper_cateObj->SetSort(array('Sort' => 'ASC', 'SwiperCateId' => 'ASC'))->ExecSelect();
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['SwiperCateId'] = $v['SwiperCateId'];
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="swiperCate" data-index="'.$v['SwiperCateId'].'" value="'.$v['Sort'].'"/>';
            $Arr[$k]['BtnArr'] = array(
                array('Desc' => '图片管理', 'Color' => 'success', 'Link' => $this->CommonObj->Url(array('admin', 'swiper', 'index')), 'Para' => $GET),
                //array('Desc' => '代码管理', 'Color' => 'success'),
            );
        }
        $KeyArr = array(
            'SwiperCateId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '分类名', 'Td' => 'th'),
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),
            
        );
        $this->BuildObj->PrimaryKey = 'SwiperCateId';
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            $Ret = $this->Swiper_cateObj->SetInsert(array(
                'Name' => $_POST['Name'],
                'Sort' => 99,
                'Code' => '',
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'swiperCate', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '分类名',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('SwiperCateId'))) $this->Err(1001);
        $Rs = $this->Swiper_cateObj->getOne($_GET['SwiperCateId']);
        if(empty($Rs)) $this->Err(1003);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            $Ret = $this->Swiper_cateObj->SetCond(array('SwiperCateId' => $Rs['SwiperCateId']))->SetUpdate(array(
                'Name' => $_POST['Name'],
            ))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->Swiper_cateObj->clean($Rs['SwiperCateId']);
            $this->Jump(array('admin', 'swiperCate', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '分类名',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 12),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('SwiperCateId'))) $this->Err(1001);
        $Rs = $this->Swiper_cateObj->getOne($_GET['SwiperCateId']);
        if(empty($Rs)) $this->Err(1003);
        $Count = $this->SwiperObj->SetCond(array('SwiperCateId' => $Rs['SwiperCateId']))->SetField('COUNT(*) AS c')->ExecSelectOne();
        if($Count['c'] != 0) $this->Err(1045);
        $Ret = $this->Swiper_cateObj->SetCond(array('SwiperCateId' => $Rs['SwiperCateId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->Swiper_cateObj->clean($Rs['SwiperCateId']);
        $this->Jump(array('admin', 'swiperCate', 'index'), 1888);
    }
    
}