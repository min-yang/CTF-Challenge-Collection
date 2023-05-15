<?php
namespace Model;
use Helper\RedisKey;
use Helper\Redis;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name 	: QC_Category
 * Date 	: 2022-03-17
 * Author 	: Qesy
 * QQ 		: 762264
 * Mail 	: 762264@qq.com
 * Company	: Shanghai Rong Yi Technology Co., Ltd.
 * Web		: http://www.sj-web.com.cn/
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class QC_Category extends \Db_pdo {
	public $TableName = 'category';
	public $PrimaryKey = 'CateId';
	public $CateArr = array();
	public $CateTreeDetail = array();
	public $CateTreeSelectHtml = '';
	public $CateTreeSelectArr = array();
	public $CateTreeModelSelectHtml = '';
	public $CateTreeModelSelectArr = array();
	public $AllSubCateIdArr = array();
	public $CateTreeIndex = 0;
	public $CateSelectId = 0; // 选中的select
	public $CateCrumbsArr = array(); //面包屑
	
	public function getList(){
	    $key = RedisKey::Category_String();
	    if(Redis::$s_IsOpen == 1 && Redis::exists($key)){
	        $Json = Redis::get($key);
	        return json_decode($Json, true);
	    }
	    // 'CateId, PCateId, TCateId, Name, Pic, ModelId, SeoTitle, Keywords, Description, IsShow, IsLink, LinkUrl, TempList, TempDetail, Sort'
	    $Arr = $this->SetSort(array('Sort' => 'ASC', 'CateId' => 'ASC'))->SetField('*')->ExecSelect();
	    if(!empty($Arr) && Redis::$s_IsOpen == 1) Redis::set($key, json_encode($Arr));
	    return $Arr;
	}
	
	public function cleanList(){
	    if(Redis::$s_IsOpen != 1) return;
	    $key = RedisKey::Category_String();
	    Redis::del($key);
	}
	
	public function getTree(){
	    $this->CateArr = $this->getList();
	    return self::_Tree(array(), 0);
	}
	
	public function getTreeDetal(){
	    if(!empty($this->CateTreeDetail)) return;
	    $this->CateArr = $this->getList();
	    self::_TreeDetail(0, 0);
	    return ;
	}
	
	public function getTreeSelectHtml($SelectCateId){ //用于转移分类	
	    $this->CateArr = $this->getList();
	    return self::_TreeSelectHtml(0, 0, $SelectCateId);
	}
	
	public function getTreeSelectArr($SelectCateId){ //用于判断转移分类	
	    $this->CateArr = $this->getList();
	    return self::_TreeSelectArr(0, $SelectCateId);
	}
	
	public function getTreeModelSelectHtml($ModelId){ //发布某模型的select
	    $this->CateArr = $this->getList();
	    return self::_TreeModelSelectHtml(0, 0, $ModelId);
	}
	
	public function getTreeModelSelectArr($ModelId){ //判断某模型的select
	    $this->CateArr = $this->getList();
	    return self::_TreeModelSelectArr(0, $ModelId);
	}
	
	public function getAllCateId($CateId, $ModelId){ //获取所有子分类ID，方便获取子分类文章
	    $this->AllSubCateIdArr = array();
	    $this->CateArr = $this->getList();
	    $this->AllSubCateIdArr[] = $CateId;
	    return self::_getAllCateId($CateId, $ModelId);
	}
	
	public function getCrumbs($CateId){
	    $CateRs = $this->getOne($CateId);
	    $this->CateCrumbsArr[] = $CateRs;
	    if($CateRs['PCateId'] != 0){
	        self::getCrumbs($CateRs['PCateId']);
	    }else{
	        $this->CateCrumbsArr = array_reverse($this->CateCrumbsArr);
	    }
	}
	
	private function _Tree($Node, $PCateId){ //树结构
	    foreach($this->CateArr as $v){
	        if($v['PCateId'] == $PCateId){
	            $Node[$v['CateId']] = array();
	            unset($this->CateArr[$v['CateId']]);
	            $Node[$v['CateId']] = self::_Tree($Node[$v['CateId']], $v['CateId']);
	        }
	    }
	    return $Node;
	}
	
	private function _TreeDetail($PCateId, $Level, $PIndex = 0){ // 后台分类列表页展示
	    foreach($this->CateArr as $k => $v){
	        if($v['PCateId'] == $PCateId){
	            if($Level != 0){
	                $this->CateTreeDetail[$PIndex]['HasSub'] = 1; //设置父分类有子分类
	            }	            
	            $CateRs = $this->getOne($v['CateId']);
	            $CateRs['Level'] = $Level;
	            $CateRs['HasSub'] = 0;
	            $this->CateTreeDetail[$this->CateTreeIndex] = $CateRs;
	            unset($this->CateArr[$k]);
	            $Index = $this->CateTreeIndex;	
	            $this->CateTreeIndex++;
	            $NewLevel = $Level+1;	 
	            self::_TreeDetail($v['CateId'], $NewLevel, $Index);
	        }
	    }
	}
	
	private function _TreeSelectHtml($PCateId, $Level, $SelectCateId){ 
	    foreach($this->CateArr as $k => $v){
	        if($v['PCateId'] == $PCateId){
	            $CateRs = $this->getOne($v['CateId']);
	            $Step = '';
	            for($i=0; $i<$Level;$i++) $Step .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            if($SelectCateId == $CateRs['CateId'] || $SelectCateId == $CateRs['PCateId']){
	                $Disabled = 'disabled="disabled"';
	                $SCId = $CateRs['CateId'];
	            }else{
	                $Disabled = '';
	                $SCId = -1;
	            }
	            $this->CateTreeSelectHtml .= '<option '.$Disabled.' value="'.$CateRs['CateId'].'">'.$Step.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─'.$CateRs['Name'].'</option>'.PHP_EOL;
	            unset($this->CateArr[$k]);
	            $NewLevel = $Level+1;	 
	            self::_TreeSelectHtml($v['CateId'], $NewLevel, $SCId);
	        }
	    }
	}
	
	private function _TreeSelectArr($PCateId, $SelectCateId){
	    foreach($this->CateArr as $k => $v){
	        if($v['PCateId'] == $PCateId){
	            $CateRs = $this->getOne($v['CateId']);	            
	            $SCId = -1;
	            if($SelectCateId == $CateRs['CateId'] || $SelectCateId == $CateRs['PCateId']){
	                $Disabled = 'disabled="disabled"';
	                $SCId = $CateRs['CateId'];
	                $this->CateTreeSelectArr[] = $CateRs['CateId'];
	            }	            
	            //$this->CateTreeSelect .= '<option '.$Disabled.' value="'.$CateRs['CateId'].'">'.$Step.'├─'.$CateRs['Name'].'</option>'.PHP_EOL;
	            unset($this->CateArr[$k]);
	            self::_TreeSelectArr($v['CateId'], $SCId);
	        }
	    }
	}
	
	private function _TreeModelSelectHtml($PCateId, $Level, $ModelId){
	    foreach($this->CateArr as $k => $v){
	        if($v['PCateId'] == $PCateId){
	            $CateRs = $this->getOne($v['CateId']);
	            $Step = '';
	            for($i=0; $i<$Level;$i++) $Step .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            if($ModelId == $CateRs['ModelId'] && $CateRs['IsPost'] == 1){
	                $Disabled = '';
	            }else{
	                $Disabled = 'disabled="disabled"';
	            }
	            $Selected = ($CateRs['CateId'] == $this->CateSelectId) ? 'selected' : '';
	            $this->CateTreeModelSelectHtml .= '<option '.$Disabled.' '.$Selected.' value="'.$CateRs['CateId'].'">'.$Step.'├─'.$CateRs['Name'].'</option>'.PHP_EOL;
	            unset($this->CateArr[$k]);
	            $NewLevel = $Level+1;
	            self::_TreeModelSelectHtml($v['CateId'], $NewLevel, $ModelId);
	        }
	    }
	}
	
	private function _TreeModelSelectArr($PCateId, $ModelId){
	    foreach($this->CateArr as $k => $v){
	        if($v['PCateId'] == $PCateId){
	            $CateRs = $this->getOne($v['CateId']);
	            if($ModelId == $CateRs['ModelId']){
	                $this->CateTreeModelSelectArr[] = $CateRs['CateId'];
	            }
	            //$this->CateTreeModelSelectHtml .= '<option '.$Disabled.' value="'.$CateRs['CateId'].'">'.$Step.'├─'.$CateRs['Name'].'</option>'.PHP_EOL;
	            unset($this->CateArr[$k]);
	            self::_TreeModelSelectArr($v['CateId'], $ModelId);
	        }
	    }
	}
	
	private function _getAllCateId($PCateId, $ModelId){
	    foreach($this->CateArr as $k => $v){
	        if($v['PCateId'] == $PCateId){
	            $CateRs = $this->getOne($v['CateId']);	            
	            if($ModelId == $CateRs['ModelId'] || $ModelId == -99){
	                $this->AllSubCateIdArr[] = $CateRs['CateId'];
	            }	            
	            unset($this->CateArr[$k]);
	            self::_getAllCateId($v['CateId'], $ModelId);
	        }
	    }
	}
}