<?php
namespace Model;
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name 	: QC_Tag
 * Date 	: 2022-03-26
 * Author 	: Qesy
 * QQ 		: 762264
 * Mail 	: 762264@qq.com
 * Company	: Shanghai Rong Yi Technology Co., Ltd.
 * Web		: http://www.sj-web.com.cn/
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class QC_Tag extends \Db_pdo {
	public $TableName = 'tag';
	public $PrimaryKey = 'TagId';
	
	public function RunUpdate($NewTags, $OldTags, $TableId, $ModelId){
	    $NewArr = array_unique(explode(',', $NewTags));
	    $OldArr = array_unique(explode(',', $OldTags));
	    $Add = $Del =  array(); //新增的
	    foreach($NewArr as $v){
	        if(!in_array($v, $OldArr)) $Add[] = $v;
	    }
	    foreach($OldArr as $v){
	        if(!in_array($v, $NewArr)) $Del[] = $v;
	    }
	    foreach($Add as $v){ //新增的	        
	        $Rs = $this->SetCond(array('Name' => $v))->ExecSelectOne();
	        $TagId = 0;
	        if(empty($Rs)){
	            $InsertArr = array('Name' => $v, 'Total' => 1, 'TsAdd' => time());
	            $this->SetInsert($InsertArr)->ExecInsert();
	            $TagId = $this->last_insert_id();
	        }else{
	            $this->SetCond(array('TagId' => $Rs['TagId']))->SetUpdate(array('Total' => ($Rs['Total']+1)))->ExecUpdate();
	            $TagId = $Rs['TagId'];
	        }
	        $this->SetTbName('tag_map')->SetInsert(array('TagId' => $TagId, 'TableId' => $TableId, 'ModelId' => $ModelId))->ExecInsert();
	    }
	    foreach($Del as $v){
	        $Rs = $this->SetCond(array('Name' => $v))->ExecSelectOne();
	        if($Rs['Total'] == 1){
	            $this->SetCond(array('TagId' => $Rs['TagId']))->ExecDelete();
	        }else{
	            $this->SetCond(array('TagId' => $Rs['TagId']))->SetUpdate(array('Total' => ($Rs['Total']-1)))->ExecUpdate();
	        }
	        $this->SetTbName('tag_map')->SetCond(array('TableId' => $TableId, 'TagId' => $Rs['TagId']))->ExecDelete();
	    }
	}
	
	public function DeleteTag($TableId){
	    $MapArr = $this->SetTbName('tag_map')->SetCond(array('TableId' => $TableId))->ExecSelect();
	    $TagArr = $this->SetCond(array('TagId' => array_column($MapArr, 'TagId')))->ExecSelect();
	    foreach($TagArr as $v){
	        $Total = ($v['Total']-1);
	        if($Total == 0){
	            $this->SetCond(array('TagId' => $v['TagId']))->ExecDelete();
	        }else{
	            $this->SetCond(array('TagId' => $v['TagId']))->SetUpdate(array('Total' => $Total))->ExecUpdate();
	        }
	    }
	    $this->SetTbName('tag_map')->SetCond(array('TableId' => $TableId))->ExecDelete();

	}
	
}