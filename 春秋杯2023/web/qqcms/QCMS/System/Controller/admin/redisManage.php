<?php
use Helper\RedisKey;
use Helper\Redis;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class RedisManage extends ControllersAdmin {
    
    public $RedisType = array(
        1 => 'string',
        2 => 'set',
        3 => 'list',
        4 => 'zset',
        5 => 'hash',
        6 => 'other',
    );
    
    public function index_Action(){        
        
        $Keys = (Redis::$s_IsOpen != 1) ? array() : Redis::keys(RedisKey::$s_projectKey.'_*');
        sort($Keys);
        $Arr = array();
        foreach($Keys as $k => $v){
            $Arr[$k] = array('Name' => $v, 'Type' => $this->RedisType[Redis::type($v)]);
        }
        $KeyArr = array(
            'Name' => array('Name' => '缓存名', 'Td' => 'td'),
            'Type' => array('Name' => '数据类型', 'Td' => 'th'),
        );
        $this->BuildObj->IsAdd = false;
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '清空缓存', 'Class' => 'primary', 'Link' => $this->CommonObj->Url(array('admin', 'redisManage', 'empty'))),
        );
        $this->BuildObj->PrimaryKey = 'Name';
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Name'))) $this->Err(1001);
        $Type = redis::type($_GET['Name']);
        $TypeStr = $this->RedisType[$Type];
        if(!empty($_POST)){
            if($TypeStr == 'string'){
                redis::set($_GET['Name'], $_POST['Content']);
            }elseif($TypeStr == 'hash'){
                redis::hMset($_GET['Name'], json_decode($_POST['Content'], true));
            }
            $this->Jump(array('admin', 'redisManage', 'index'), 1888);
        }
        switch($TypeStr){
            case 'string':
                $Ret = redis::get($_GET['Name']);
                break;
            case 'hash':
                $Rs = redis::hGetAll($_GET['Name']);
                $Ret = json_encode($Rs);
                break;
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '缓存名字',  'Type' => 'input', 'Value' => $_GET['Name'], 'Disabled' => 1, 'Col' => 6),            
            array('Name' =>'Type', 'Desc' => '缓存类型',  'Type' => 'input', 'Value' => $TypeStr, 'Disabled' => 1, 'Col' => 6),
            array('Name' =>'Content', 'Desc' => '缓存内容',  'Type' => 'textarea', 'Value' => $Ret, 'Disabled' => 0, 'Col' => 12),
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Name'))) $this->Err(1001);
        $Name = $_GET['Name'];
        Redis::del($Name);
        $this->Jump(array('admin', 'redisManage', 'index'), 1888);
    }
    
    public function empty_Action(){
        if(Redis::$s_IsOpen != 1) $this->Err(1002);
        $Keys = Redis::keys(RedisKey::$s_projectKey.'_*');
        foreach($Keys as $v)Redis::del($v);
        $this->Jump(array('admin', 'redisManage', 'index'), 1888);
    }
    
}