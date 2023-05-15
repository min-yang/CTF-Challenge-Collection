<?php
use Helper\RedisKey;
use Helper\Redis;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Index extends ControllersAdmin {
    
    public function index_Action(){  
        if(!empty($this->SysRs['License'])){
            $LicenseJson = $this->getLicense($this->SysRs['License']);
            $LicenseRs = empty($LicenseJson) ? array() : json_decode($LicenseJson, true);
            if(empty($LicenseRs) || strpos(URL_DOMAIN, $LicenseRs['Domain']) === false){
                $LicenseRs = array();
            }
        }        
        $FlowDataKV = $this->Stat_flowObj->GetIndexChart();
        $MaxDay = date('t');
        $Start = strtotime(date('Y-m-01'));
        for($i=0;$i<$MaxDay;$i++){
            $Date = date('Y-m-d', ($Start+86400*$i));
            $DataArr[$Date] = 0;
            if(isset($FlowDataKV[$Date])){
                $DataArr[$Date] = intval($FlowDataKV[$Date]['FlowNum']);               
            }
            $Total += $DataArr[$Date];
            
        }
        $tmp['DataArr'] = $DataArr;
        $tmp['Total'] = $Total;
        $tmp['LicenseRs'] = $LicenseRs;
        $tmp['Stat'] = $this->SysObj->GetStat();
        $this->LoadView('admin/index/index', $tmp);
    }
    
    public function upgrade_Action(){       
        $Ret = $this->getVerUpdate();        
        if(!empty($_POST) && !empty($Ret['Data'])){
            $DownRet = @file_get_contents($Ret['Data']['AddressPatch']);
            if($DownRet === false) $this->Err(1016);
            $Path = './Static/tmp/';
            $FileName = 'QCms_v'.$Ret['Data']['Version'].'_update.zip';
            $WriteRet = @file_put_contents($Path.$FileName, $DownRet);
            if($WriteRet === false) $this->Err(1017);
            $CmsUpdatePath = $Path.'QCms_v'.$Ret['Data']['Version'].'_update';
            $UnZipRet = $this->CommonObj->UnZip($Path.$FileName, $CmsUpdatePath);
            if($UnZipRet === false) $this->Err(1018);
            $CopyRet = $this->CommonObj->DirCopy($CmsUpdatePath, './');
            if($CopyRet === false) $this->Err(1019);
            $upgradeFile = './System/upgrade/upgrade_'.$Ret['Data']['Version'].'.php';
            if(!file_exists($upgradeFile)) $this->Err(1035);
            require_once $upgradeFile;
            $UpgradeObj = new Upgrade();
            try{
                DB::$s_db_obj->beginTransaction();
                $this->SysObj->SetCond(array('Name' => 'Version'))->SetUpdate(array('AttrValue' => $Ret['Data']['Version']))->ExecUpdate();
                $UpgradeObj->Exec();
                DB::$s_db_obj->commit();
            }catch(PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->Err(1002);
            }            
            $this->CookieObj->del(array('UpdateTs' => '', 'IsUpdate' => ''), 'User');
            if(Redis::$s_IsOpen == 1){ //清空缓存      
                $Keys = Redis::keys(RedisKey::$s_projectKey.'_*');
                foreach($Keys as $v)Redis::del($v);
            }             
            $this->Jump(array('admin', 'index', 'upgrade'), 1888);
        }
        if(empty($Ret['Data'])){
            $VersionUpdate = '无更新版本';
        }else{
            $VersionUpdate = $Ret['Data']['Version'];
        }        
        $this->BuildObj->Arr = array(
            array('Name' =>'Version', 'Desc' => '当前版本',  'Type' => 'input', 'Value' => $this->SysRs['Version'], 'Required' => 1, 'Col' => 6),
            array('Type' =>'htmlFill', 'Col' => 6),
            array('Name' =>'VersionUpdate', 'Desc' => '升级版本',  'Type' => 'input', 'Value' => $VersionUpdate, 'Required' => 1, 'Col' => 6),             
            array('Type' =>'htmlFill', 'Col' => 6),         
        );
        $this->BuildObj->FormFooterBtnArr = array(
            array('Name' => 'Update', 'Desc' => '手动升级', 'Class' => 'default', 'Type' => 'Link', 'Url' => $this->CommonObj->Url(array('admin', 'index', 'upgradeManual'))),
        );
        $this->BuildObj->NameSubmit = '立即升级';
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function upgradeManual_Action(){
        $Ret = $this->getVerUpdate();    
        if($Ret['Code'] != 0) $this->Err(1000, $Ret['Msg']);
        $upgradeFile = './System/Upgrade/upgrade_'.$Ret['Data']['Version'].'.php';
        if(!file_exists($upgradeFile)) $this->Err(1035);
        require_once $upgradeFile;
        $UpgradeObj = new Upgrade();
        try{
            DB::$s_db_obj->beginTransaction();
            $this->SysObj->SetCond(array('Name' => 'Version'))->SetUpdate(array('AttrValue' => $Ret['Data']['Version']))->ExecUpdate();
            $UpgradeObj->Exec();
            DB::$s_db_obj->commit();
        }catch(PDOException $e){
            DB::$s_db_obj->rollBack();
            $this->Err(1002);
        }
        $this->CookieObj->del(array('UpdateTs' => '', 'IsUpdate' => ''), 'User');
        if(Redis::$s_IsOpen == 1){ //清空缓存
            $Keys = Redis::keys(RedisKey::$s_projectKey.'_*');
            foreach($Keys as $v)Redis::del($v);
        }
        $this->Jump(array('admin', 'index', 'upgrade'), 1888);
    }
    
}