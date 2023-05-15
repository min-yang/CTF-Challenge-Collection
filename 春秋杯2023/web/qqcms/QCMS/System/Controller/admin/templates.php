<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Templates extends ControllersAdmin {

    public $TempType = array(
        'index' => '首页',
        'list' => '列表 (分类页)',
        'detail' => '详情 (文章详情)',
        'form' => '表单 (自定义表单)',
        'component' => '组件',
        'page' => '单页',
        'search' => '搜索页',
    );
    public $TempModelType = array(
        'article' => '文章',
        'product' => '产品',
        'album' => '相册',
        'down' => '下载',
        'default' => '默认',
        'main' => '主要',
        'page' => '封面',
    );

    public function index_Action(){
        $TempList = $this->getTemplate();
        $Arr = array();
        $Desc = $Desc2 = '';
        $i = 0;
        foreach($TempList as $v){
            $i++;
            $FileArr = explode('_', substr($v, 0, -5));
            $Desc = $this->TempType[$FileArr[0]];
            $Desc2 = isset($this->TempModelType[$FileArr[1]]) ? $this->TempModelType[$FileArr[1]] : $FileArr[1];
            $Desc3 = isset($FileArr[2]) ? $FileArr[2] : '';
            $FilePath = PATH_TEMPLATE.$this->SysRs['TmpPath'].'/'.$v;
            $Size = $this->CommonObj->Size(filesize($FilePath));
            $Ts = filemtime($FilePath);
            $Arr[] = array('Id' => $i, 'Name' => $v, 'Desc' => $Desc3.$Desc2.$Desc, 'Size' => $Size, 'TsView' => date('Y-m-d H:i:s', $Ts));
        }
        $KeyArr = array(
            'Id' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '文件名', 'Td' => 'th'),
            'Desc' => array('Name' => '描述'),
            'Size' => array('Name' => '大小'),
            'TsView' => array('Name' => '修改时间'),
        );
        $this->BuildObj->PrimaryKey = 'Name';
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }

    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Type', 'KeyName'))) $this->Err(1001);
            $File = $_POST['Type'].'_'.$_POST['KeyName'].'.html';
            $FilePath = PATH_TEMPLATE.$this->SysRs['TmpPath'].'/'.$File;
            $Ret = @file_put_contents($FilePath, $_POST['Html']);
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'templates', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Type', 'Desc' => '模板类型',  'Type' => 'select', 'Data' => $this->TempType, 'Value' => 'index', 'Required' => 1, 'Col' => 6),
            array('Name' =>'KeyName', 'Desc' => '模板名字 (article_diy) 不需要写.Html',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'Html', 'Desc' => '模板HTML',  'Type' => 'textarea', 'Value' => '', 'Required' => 1, 'Col' => 12, 'Row' => 20),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Name'))) $this->Err(1001);
        $FilePath = PATH_TEMPLATE.$this->SysRs['TmpPath'].'/'.trim($_GET['Name']);
        if(!file_exists($FilePath)) $this->Err(1035);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Type', 'KeyName'))) $this->Err(1001);
            $File = $_POST['Type'].'_'.$_POST['KeyName'].'.html';
            $NewFilePath = PATH_TEMPLATE.$this->SysRs['TmpPath'].'/'.$File;
            $Ret = @file_put_contents($NewFilePath, $_POST['Html']);
            if($File != trim($_GET['Name'])) @unlink($FilePath); // 如果改名了，就删除旧的文件
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'templates', 'index'), 1888);
        }
        $FileNameArr = explode('_', trim($_GET['Name']));
        $KeyName = substr(trim($_GET['Name']), strlen($FileNameArr[0])+1, -5);
        $Html = file_get_contents($FilePath);
        $this->BuildObj->Arr = array(
            array('Name' =>'Type', 'Desc' => '模板类型',  'Type' => 'select', 'Data' => $this->TempType, 'Value' => $FileNameArr[0], 'Required' => 1, 'Col' => 6),
            array('Name' =>'KeyName', 'Desc' => '模板名字 (article_diy) 不需要写.Html',  'Type' => 'input', 'Value' => $KeyName, 'Required' => 1, 'Col' => 6),
            array('Name' =>'Html', 'Desc' => '模板HTML',  'Type' => 'textarea', 'Value' => $Html, 'Required' => 1, 'Col' => 12, 'Row' => 20),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Name'))) $this->Err(1001);
        $FilePath = PATH_TEMPLATE.$this->SysRs['TmpPath'].'/'.trim($_GET['Name']);
        if(!file_exists($FilePath)) $this->Err(1035);
        $FilePath = PATH_TEMPLATE.$this->SysRs['TmpPath'].'/'.trim($_GET['Name']);
        @unlink($FilePath);
        $this->Jump(array('admin', 'templates', 'index'), 1888);
    }

    public function market_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $PageNum = 12;
        $CateId = intval($_GET['CateId']);
        $Ret = $this->getTemplaites($Page, $PageNum, $CateId);
        $CateArr = $this->getTemplaitesCate();
        $tmp['CateArr'] = $CateArr['Data'];
        $tmp['Arr'] = $Ret['Data']['List'];
        $tmp['Page'] = $this->CommonObj->PageBar($Ret['Data']['Count'], $PageNum);
        $tmp['TempFolder'] = $this->getTempFolder();
        $this->LoadView('admin/templates/market', $tmp);
    }
    
    public function builder_Action(){
        $LabelArr = array(
            'include' => '引入组件',
            'label' => '自定义标签',
            'global' => '全局标签',
            'cate' => '分类页标签',
            'detail' => '详情页标签',            
            'page' => '单页专属',
            'get' => '获取单条数据',
            'menu' => '一级菜单',
            'smenu' => '二级菜单',
            'ssmenu' => '三级菜单',
            'list' => '列表标签',
            'link' => '友情链接',
            'loop' => '万能标签',
            'slide' => '幻灯片',
            'tag' => 'Tag标签',
            'if' => 'if条件标签',
            'date' => '日期标签',
            'substr' => '截取字符串',
            'math' => '数学标签',
            'replace' => '替换标签',
            'photo' => '相片标签',
            'thumb' => '缩略图',
        );
        $tmp['LabelArr'] = $LabelArr;
        $tmp['DiyLabelArr'] = $this->LabelObj->ExecSelect();
        $tmp['DiyField'] = $this->SysObj->SetCond(array('IsSys' => 2))->SetSort(array('Sort' => 'ASC'))->ExecSelect();
        $tmp['componentList'] = $this->getTemplate('component_');
        $ModelArr = $this->Sys_modelObj->getList();
        
        foreach($ModelArr as $k => $v){
            $ModelArr[$k]['FieldJson'] = empty($v['FieldJson']) ? array() : json_decode($v['FieldJson'], true);
        }
        $tmp['ModelArr'] = $ModelArr;       
        $this->LoadView('admin/templates/builder', $tmp);
    }

    public function test_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Html', 'Type'))) $this->DieErr(1001);
            switch ($_POST['Type']){
                case 'index':
                    echo $this->tempRunTest($_POST['Type'], 0, $_POST['Html']);
                    break;
                case 'cate':
                    echo $this->tempRunTest($_POST['Type'], $_POST['Index'], $_POST['Html']);
                    break;
                case 'detail':
                    echo $this->tempRunTest($_POST['Type'], $_POST['Index'], $_POST['Html']);
                    break;
                case 'page':
                    echo $this->tempRunTest($_POST['Type'], $_POST['Index'], $_POST['Html']);
                    break;
            }
            return;
        }
        $this->LoadView('admin/templates/test');
    }

    public function api_Action(){ // 接口演示
        $ApiArr = array(
            'sys' => array('Name' => '获取系统信息', 'Path' => 'api/common/sys', 'Para' => array()),
            'cateList' => array('Name' => '获取分类列表', 'Path' => 'api/common/cateList', 'Para' => array()),
            'cateOne' => array('Name' => '获取分类详情', 'Path' => 'api/common/cateOne', 'Para' => array(
                array('Key' => 'CateId', 'IsMust' => 1, 'Default' => '1', 'Desc' => '分类ID')
            )),
            'pageOne' => array('Name' => '获取单页信息', 'Path' => 'api/common/pageOne', 'Para' => array(
                array('Key' => 'PageId', 'IsMust' => 1, 'Default' => '1', 'Desc' => '单页ID')
            )),
            'formOne' => array('Name' => '获取表单信息', 'Path' => 'api/common/formOne', 'Para' => array(
                array('Key' => 'KeyName', 'IsMust' => 1, 'Default' => '', 'Desc' => '表单调用名')
            )),
            'formSubmit' => array('Name' => '表单提交', 'Path' => 'api/common/formSubmit', 'Para' => array(
                array('Key' => 'KeyName', 'IsMust' => 1, 'Default' => '', 'Desc' => '表单调用名'),
                array('Key' => 'Token', 'IsMust' => 2, 'Default' => '', 'Desc' => '需要登陆的表单需提交'),
                array('Key' => 'Xxx', 'IsMust' => 2, 'Default' => '', 'Desc' => '其他自定义字段'),
            )),
            'labelOne' => array('Name' => '获取自定义标签', 'Path' => 'api/common/labelOne', 'Para' => array(
                array('Key' => 'KeyName', 'IsMust' => 1, 'Default' => '', 'Desc' => '标签调用名')
            )),
            'contentlist' => array('Name' => '获取文章列表', 'Path' => 'api/common/contentlist', 'Para' => array(
                array('Key' => 'Model', 'IsMust' => 1, 'Default' => 'article', 'Desc' => '模型名(article:文章,product:产品,album:相册,down:下载 ,Xxx:自定义模型)'),
                array('Key' => 'CateId', 'IsMust' => 2, 'Default' => '0', 'Desc' => '分类ID'),
                array('Key' => 'Page', 'IsMust' => 2, 'Default' => '1', 'Desc' => '页码'),
                array('Key' => 'Row', 'IsMust' => 2, 'Default' => '10', 'Desc' => '条数(默认20)'),
                array('Key' => 'State', 'IsMust' => 2, 'Default' => '1', 'Desc' => '状态(1:已发布，2:未发布)'),
                array('Key' => 'Ids', 'IsMust' => 2, 'Default' => '', 'Desc' => '指定获取多条内容(Ids:1|2|3)'),
                array('Key' => 'Keyword', 'IsMust' => 2, 'Default' => '', 'Desc' => '关键字(搜索用)'),
            )),
            'contentOne' => array('Name' => '获取文章详情', 'Path' => 'api/common/contentOne', 'Para' => array(
                array('Key' => 'Id', 'IsMust' => 1, 'Default' => '', 'Desc' => '文章ID')
            )),
            'link' => array('Name' => '获取友情链接', 'Path' => 'api/common/link', 'Para' => array(
                array('Key' => 'LinkCateId', 'IsMust' => 2, 'Default' => '1', 'Desc' => '友情链接分类ID'),
                array('Key' => 'Page', 'IsMust' => 2, 'Default' => '1', 'Desc' => '页码'),
                array('Key' => 'Row', 'IsMust' => 2, 'Default' => '10', 'Desc' => '条数(默认20)'),
            )),
            'swiper' => array('Name' => '获取幻灯片', 'Path' => 'api/common/swiper', 'Para' => array(
                array('Key' => 'SwiperCateId', 'IsMust' => 1, 'Default' => '1', 'Desc' => '幻灯片ID'),
            )),

        );

        $tmp['ApiArr'] = $ApiArr;
        $this->LoadView('admin/templates/api', $tmp);
    }

}