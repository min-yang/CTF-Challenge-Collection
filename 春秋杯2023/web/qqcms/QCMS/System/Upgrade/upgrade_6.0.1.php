<?php 
use Model\QC_Sys;
use Model\QC_Category;

class Upgrade{
    
    public function Exec(){
        $SysObj = QC_Sys::get_instance();
        $CategoryObj = QC_Category::get_instance();
        $DbConfig = Config::DbConfig();
        $Rs = $CategoryObj->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = '".$DbConfig['Prefix']."category' and table_schema = '".$DbConfig['Name']."';", array());
        $FieldArr = array_column($Rs, 'COLUMN_NAME');
        $PageRs = $CategoryObj->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = '".$DbConfig['Prefix']."page' and table_schema = '".$DbConfig['Name']."';", array());
        $PageFieldArr = array_column($PageRs, 'COLUMN_NAME');
        $ArticleRs = $CategoryObj->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = '".$DbConfig['Prefix']."table_article' and table_schema = '".$DbConfig['Name']."';", array());
        $ArticleFieldArr = array_column($ArticleRs, 'COLUMN_NAME');
        $ModelArr = $CategoryObj->SetTbName('sys_model')->ExecSelect();
        $SwiperRs = $CategoryObj->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = '".$DbConfig['Prefix']."swiper' and table_schema = '".$DbConfig['Name']."';", array());
        $SwiperFieldArr = array_column($SwiperRs, 'COLUMN_NAME');
        try{
            $SysObj->exec('alter table '.$DbConfig['Prefix'].'swiper_cate modify COLUMN Name varchar(100) NOT NULL DEFAULT "";', array());
            if(!in_array('TCateId', $FieldArr)){
                $SysObj->exec('alter table '.$DbConfig['Prefix'].'category add COLUMN TCateId int(11) NOT NULL DEFAULT "0";', array()); 
            }
            if(!in_array('NameEn', $FieldArr)){
                $SysObj->exec('alter table '.$DbConfig['Prefix'].'category add COLUMN NameEn varchar(100) NOT NULL DEFAULT "";', array()); 
            }    
            if(!in_array('NameEn', $PageFieldArr)){
                $SysObj->exec('alter table '.$DbConfig['Prefix'].'page add COLUMN NameEn varchar(100) NOT NULL DEFAULT "";', array());
            }  
            if(!in_array('DownNum', $ArticleFieldArr)){
                foreach($ModelArr as $v){
                    $SysObj->exec('alter table '.$DbConfig['Prefix'].'table_'.$v['KeyName'].' add COLUMN DownNum int(11) NOT NULL DEFAULT "0";', array());
                }
                
            }
            if(!in_array('Summary', $SwiperFieldArr)){
                $SysObj->exec('alter table '.$DbConfig['Prefix'].'swiper add COLUMN Summary varchar(255) NOT NULL DEFAULT "";', array()); 
            }
        }catch (PDOException $e){
        
        }
        
        $TopCateArr = $SysObj->SetTbName('category')->SetCond(array('PCateId' => 0))->SetField('CateId, PCateId')->ExecSelect();
        $SecCateArr = $SysObj->SetTbName('category')->SetCond(array('PCateId' => array_column($TopCateArr, 'CateId')))->SetField('CateId, PCateId')->ExecSelect();
        foreach($SecCateArr as $v){
            $CategoryObj->getAllCateId($v['CateId'], -1);
            $CategoryObj->SetCond(array('CateId' => $CategoryObj->AllSubCateIdArr))->SetUpdate(array('TCateId' => $v['PCateId']))->ExecUpdate();
        }
    }
    
}
