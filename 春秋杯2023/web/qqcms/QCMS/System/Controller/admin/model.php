<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Model extends ControllersAdmin {

    public function index_Action(){
        $Arr = $this->Sys_modelObj->getList();
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['ModelId'] = $v['ModelId'];
            $Arr[$k]['IsSysView'] = ($v['IsSys'] == 1) ? '<i class="bi bi-check-lg text-success h5"></i>' : '<i class="bi bi-x-lg text-danger h5"></i>';
            $Arr[$k]['BtnArr'] = array(
                array('Desc' => '字段管理', 'Color' => 'success', 'Link' => $this->CommonObj->Url(array('admin', 'modelField', 'index')), 'Para' => $GET),
            );
        }
        $KeyArr = array(
            'ModelId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '模型名', 'Td' => 'th'),
            'KeyName' => array('Name' => '标识名', 'Td' => 'th'),
            'IsSysView' => array('Name' => '系统内置', 'Td' => 'th'),
        );
        $this->BuildObj->PrimaryKey = 'ModelId';
        $this->BuildObj->TableTopBtnArr = array(
            array('Name' => '分类管理', 'Link' => $this->CommonObj->Url(array('admin', 'linkCate', 'index'))),
        );
        $this->BuildObj->NameAdd = '添加模型';

        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }

    public function add_Action(){
        if(!empty($_POST)){
            $DbConfig = Config::DbConfig();
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'KeyName'))) $this->Err(1001);
            try{
                DB::$s_db_obj->beginTransaction();
                $this->Sys_modelObj->SetInsert(array('Name' => trim($_POST['Name']), 'KeyName' => trim($_POST['KeyName'])))->ExecInsert();

                $FieldStr = "
                  `Id` bigint(20) NOT NULL DEFAULT '0',
                  `CateId` int(11) NOT NULL DEFAULT '0',
                  `Title` varchar(100) NOT NULL DEFAULT '',
                  `STitle` varchar(60) NOT NULL DEFAULT '' COMMENT '短标题',
                  `Tag` varchar(100) NOT NULL DEFAULT '' COMMENT 'Tag',
                  `Pic` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
                  `Source` varchar(50) NOT NULL DEFAULT '' COMMENT '来源',
                  `Author` varchar(50) NOT NULL DEFAULT '' COMMENT '作者',
                  `Sort` tinyint(3) NOT NULL DEFAULT '99',
                  `Keywords` varchar(255) NOT NULL DEFAULT '',
                  `Description` varchar(255) NOT NULL DEFAULT '',
                  `TsAdd` bigint(20) NOT NULL DEFAULT '0',
                  `TsUpdate` bigint(20) NOT NULL DEFAULT '0',
                  `ReadNum` int(11) NOT NULL DEFAULT '0',
                  `DownNum` int(11) NOT NULL DEFAULT '0',
                  `Coins` int(11) NOT NULL DEFAULT '0' COMMENT '需消费金币',
                  `Money` int(11) NOT NULL DEFAULT '0' COMMENT '支付费用',
                  `UserLevel` int(11) NOT NULL DEFAULT '0' COMMENT '浏览权限',
                  `Color` varchar(10) NOT NULL DEFAULT '' COMMENT '颜色',
                  `UserId` bigint(20) NOT NULL DEFAULT '0',
                  `Good` int(11) NOT NULL DEFAULT '0',
                  `Bad` int(11) NOT NULL DEFAULT '0',
                  `State` tinyint(3) NOT NULL DEFAULT '1',
                  `Content` text,
                  `IsLink` tinyint(3) NOT NULL DEFAULT '2',
                  `LinkUrl` varchar(255) NOT NULL DEFAULT '' COMMENT '外链地址',
                  `IsBold` tinyint(3) NOT NULL DEFAULT '2',
                  `IsPic` tinyint(3) NOT NULL DEFAULT '2' COMMENT '是否有缩略图',
                  `IsSpuerRec` tinyint(3) NOT NULL DEFAULT '2' COMMENT '是否特推',
                  `IsHeadlines` tinyint(3) NOT NULL DEFAULT '2' COMMENT '是否头条',
                  `IsRec` tinyint(3) NOT NULL DEFAULT '2' COMMENT '是否推荐',
                  `IsPost` tinyint(3) NOT NULL DEFAULT '1' COMMENT '允许评论',
                  `IsDelete` tinyint(3) NOT NULL DEFAULT '2' COMMENT '是否删除',
                  `PinYin` varchar(255) NOT NULL DEFAULT '',
                  `PY` varchar(255) NOT NULL DEFAULT '',
                  `Summary` varchar(255) DEFAULT '' COMMENT '摘要',
                  ";

                $TableSql = 'CREATE TABLE `'.$DbConfig['Prefix'].'table_'.$_POST['KeyName'].'` ( '.PHP_EOL.$FieldStr.' PRIMARY KEY (`Id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=\'\';';
                $this->SysObj->exec($TableSql, array());
                DB::$s_db_obj->commit();
            }catch (PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->Err(1002);
            }
            $this->Sys_modelObj->cleanList();
            $this->Jump(array('admin', 'model', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '模型名字',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Type' => 'htmlFill', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'KeyName', 'Desc' => '标识名字 (只能英文和数字)',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),

        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('ModelId'))) $this->Err(1001);
        $Rs = $this->Sys_modelObj->SetCond(array('ModelId' => $_GET['ModelId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);

        if(!empty($_POST)){
            $DbConfig = Config::DbConfig();
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            try{
                DB::$s_db_obj->beginTransaction();
                $this->Sys_modelObj->SetCond(array('ModelId' => $Rs['ModelId']))->SetUpdate(array('Name' => trim($_POST['Name'])))->ExecUpdate();
                DB::$s_db_obj->commit();
            }catch (PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->Err(1002);
            }
            $this->Sys_modelObj->cleanList();
            $this->Jump(array('admin', 'model', 'index'), 1888);
        }

        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '模型名字',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 6),
            array('Type' => 'htmlFill', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'KeyName', 'Desc' => '标识名字 (只能英文和数字)',  'Type' => 'input', 'Value' => $Rs['KeyName'], 'Disabled' => 1, 'Col' => 6),

        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }

    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('ModelId'))) $this->Err(1001);
        $Rs = $this->Sys_modelObj->SetCond(array('ModelId' => $_GET['ModelId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        if($Rs['IsSys'] == 1) $this->Err(1051);
        $DbConfig = Config::DbConfig();
        $TableArr = $this->Sys_modelObj->query('show tables', array());
        $TableNameArr = array_column($TableArr, 'Tables_in_'.$DbConfig['Name']);
        if(in_array($DbConfig['Prefix'].'table_'.$Rs['KeyName'], $TableNameArr)){
            $Count = $this->Sys_modelObj->SetTbName('table_'.$Rs['KeyName'])->SetField('COUNT(*) AS c')->ExecSelectOne();
            if($Count['c'] > 0) $this->Err(1050);
            $HaveCate = $this->CategoryObj->SetCond(array('ModelId' => $Rs['ModelId']))->SetField('COUNT(*) AS c')->ExecSelectOne();
            if($HaveCate['c'] > 0) $this->Err(1050);
        }

        try{
            DB::$s_db_obj->beginTransaction();
            if(in_array($DbConfig['Prefix'].'table_'.$Rs['KeyName'], $TableNameArr)){
                $this->SysObj->exec('DROP TABLE IF EXISTS `'.$DbConfig['Prefix'].'table_'.$Rs['KeyName'].'`;', array()); //删除原有表
            }
            $this->Sys_modelObj->SetCond(array('ModelId' => $Rs['ModelId']))->ExecDelete();
            DB::$s_db_obj->commit();
        }catch (PDOException $e){
            DB::$s_db_obj->rollBack();
            $this->Err(1002);
        }
        $this->Sys_modelObj->cleanList();
        $this->Jump(array('admin', 'model', 'index'), 1888);
    }

}