<div class="fixed-sidebar-left">
    <ul class="nav navbar-nav side-nav nicescroll-bar flex-nowrap">
        <?
        foreach($this->RoleMenuArr as $val){
            if($this->LoginUserRs['GroupAdminId'] != 1 && !in_array($val['Key'], $this->PermissionArr)) continue;
            $Key = $val['Key'];
            $Key2Id = str_replace('/', '_', $Key);
            $CateRs = $this->MenuArr[$Key];
            if(!in_array($this->LoginUserRs['GroupAdminId'], $CateRs['Permission'])) continue;
        ?>
        <li class="nav-item">
            <a class="nav-link px-3 <? if(in_array(Router::$s_Controller, $val['subCont'])) echo 'active';?>"
                <?=!empty($val['Sub']) ? 'href="javascript:void(0);" data-toggle="collapse"' : 'href="'.$CateRs['Url'].'"'?>

                data-target="#<?=$Key2Id?>">
                <i class="<?=$val['Icon']?> mr-10" style="font-size: 1rem;"></i><?=$CateRs['Name']?><span class="pull-right <?=empty($val['Sub']) ? 'd-none' : 'd-block'?>"><i class="fa fa-fw fa-angle-down"></i></span></a>
                <? if(!empty($val['Sub'])){ ?>
            <ul id="<?=$Key2Id?>" class="collapse <? if(in_array(Router::$s_Controller, $val['subCont'])) echo 'show';?> collapse-level-1">
                <?
                foreach($val['Sub'] as $sval){

                    if($this->LoginUserRs['GroupAdminId'] != 1 && !in_array($sval['Key'], $this->PermissionArr)) continue;
                    $sKey = $sval['Key'];
                    $sCateRs = $this->MenuArr[$sKey];
                    if(!in_array($this->LoginUserRs['GroupAdminId'], $sCateRs['Permission'])) continue;
                    $PathIsOk = ($this->CommonObj->url(array($this->Module, Router::$s_Controller, Router::$s_Method)) == $sCateRs['Url']) ? 1 : 0;
                    $Active = '';
                    $ParaStr = '';
                    //var_dump($sCateRs);
                    if(isset($sCateRs['Para'])){
                        foreach($sCateRs['Para'] as $pkey => $pval){
                            if(isset($_GET[$pkey]) && $_GET[$pkey] == $pval && $PathIsOk){
                                $Active = 'active';
                            }else{
                                $Active = '11';
                            }
                        }

                        $ParaStr = '?'.http_build_query($sCateRs['Para']);
                    }elseif($PathIsOk){
                        $Active = 'active';
                    }
                ?>
                <li>
                    <a class="<?=$Active?>" href="<?=$sCateRs['Url'].$ParaStr?>"><?=$sCateRs['Name']?></a>
                </li>
            <? } ?>

            </ul>
            <? } ?>
        </li>
    <? } ?>
    </ul>
</div>