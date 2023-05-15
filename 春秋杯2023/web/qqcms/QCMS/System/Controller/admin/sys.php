<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Sys extends ControllersAdmin {
    
    public function index_Action(){ //系统设置
        if(!empty($_POST)){
            try {
                DB::$s_db_obj->beginTransaction();
                
                foreach($_POST as $k => $v){
                    $this->SysObj->SetCond(array('Name' => $k))->SetUpdate(array('AttrValue' => $v))->ExecUpdate();
                }
                
                DB::$s_db_obj->commit();
            }catch (PDOException $e){
                DB::$s_db_obj->rollBack();
                $this->Err(1002);
            }
            $this->SysObj->cleanList();
            $this->Jump(array('admin', 'sys', 'index'), 1888);
        }
        /* $Files = scandir(PATH_TEMPLATE);
        $Folder = array();
        foreach($Files as $v){
            if(in_array($v, array('.', '..'))) continue;
            if(!is_dir(PATH_TEMPLATE.$v)) continue;
            $Folder[$v] = $v;
        } */
        $Folder = $this->getTempFolder();
        $SysArr = $this->SysObj->getList();
        $FormArr = array();
        $TempList = $this->getTemplate('index_');
        $TempSearchList = $this->getTemplate('search_');
        $UrlListDesc = '<span class="text-dark">
            <span class="mr-3 font-weight-bold">{CateId}</span>分类ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+分类ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+分类ID<br>
        </span>
        ';
        $UrlDetailDesc = '<span class="text-dark">
        	<span class="mr-3 font-weight-bold">{Y}、{M}、{D}</span>年月日<br>
            <span class="mr-3 font-weight-bold">{Id}</span>文章ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+文章ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+文章ID<br>
        </span>
        ';
        $UrlPageDesc = '<span class="text-dark">
            <span class="mr-3 font-weight-bold">{PageId}</span>文章ID<br>
            <span class="mr-3 font-weight-bold">{PinYin}</span>拼音+文章ID<br>
            <span class="mr-3 font-weight-bold">{PY}</span>拼音部首+文章ID<br>
        </span>
        ';
        foreach($SysArr as $v){
            $DataArr = ($v['AttrType'] == 'radio') ? $this->OpenArr : array();
            if($v['Name'] == 'TmpPath') $DataArr = $Folder;
            if($v['Name'] == 'TmpPathMobile') $DataArr = $Folder;
            if($v['Name'] == 'Editor') $DataArr = $this->EditorArr;
            if($v['Name'] == 'TmpIndex') $DataArr = $TempList;
            if($v['Name'] == 'TmpSearch') $DataArr = $TempSearchList;
            if($v['Name'] == 'WaterMaskType') $DataArr = array(1 => '图片', 2 => '文字');
            if($v['Name'] == 'WaterMaskPostion') $DataArr = array(
                0 => '随机位置',
                1 => '上左',
                2 => '上中',
                3 => '上右',
                4 => '中左',
                5 => '中中',
                6 => '中右',
                7 => '下左',
                8 => '下中',
                9 => '下右',
            );
            $FormArr[$v['GroupId']][] = array('Name' => $v['Name'], 'Desc' => $v['Info'],  'Type' => $v['AttrType'], 'Data' => $DataArr, 'Value' => $v['AttrValue'], 'Col' => 12);;
        }
        $this->BuildObj->Arr = array(            
            array(
                'Title' => '核心设置',
                'Form' => $FormArr[1]                
            ),
            array(
                'Title' => '扩展设置',
                'Form' => $FormArr[2]                        
            ),
            array(
                'Title' => '附件设置',
                'Form' => $FormArr[3]
            ),
            array(
                'Title' => '多站点管理',
                'Form' => $FormArr[4]
            )
        );
        if(isset($FormArr[10]) && count($FormArr[10]) > 0){
            $this->BuildObj->Arr[] = array(
                'Title' => '自定义变量',
                'Form' => $FormArr[10]
            );
        }
        
        
        $this->BuildObj->Arr[1]['Form'][] = array('Desc' => '列表地址规则说明',  'Type' => 'html', 'Value' => $UrlListDesc, 'Required' => 1, 'Col' => 4);
        $this->BuildObj->Arr[1]['Form'][] = array('Desc' => '文章地址规则说明',  'Type' => 'html', 'Value' => $UrlDetailDesc, 'Required' => 1, 'Col' => 4);
        $this->BuildObj->Arr[1]['Form'][] = array('Desc' => '单页地址规则说明',  'Type' => 'html', 'Value' => $UrlPageDesc, 'Required' => 1, 'Col' => 4);
        $this->PageTitle2 = $this->BuildObj->FormMultipleTitle();
        $this->BuildObj->FormMultiple('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function license_Action(){ //授权码
        if(!empty($_POST)){
            //if(!$this->VeriObj->VeriPara($_POST, array('License'))) $this->Err(1001);
            $Ret = $this->SysObj->SetCond(array('Name' => 'License'))->SetUpdate(array('AttrValue' => trim($_POST['License'])))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->SysObj->clean('License');
            $this->Jump(array('admin', 'sys', 'license'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'License', 'Desc' => '授权码',  'Type' => 'textarea', 'Value' => $this->SysRs['License'], 'Required' => 2, 'Col' => 12, 'Row' => 4),           
        );
        if(!empty($this->SysRs['License'])){
            $LicenseJson = $this->getLicense($this->SysRs['License']);            
            $LicenseRs = empty($LicenseJson) ? array() : json_decode($LicenseJson, true);
            if(empty($LicenseRs) || strpos(URL_DOMAIN, $LicenseRs['Domain']) === false){
                $Desc = '授权失败';
                $Content = '系统未经授权，请到官方购买授权。';
            }else{
                $Desc = '授权成功';
                $Content = '域名已经获得正版授权，'.PHP_EOL.'授权域名：'.$LicenseRs['Domain'].', 到期日期：'.$LicenseRs['Date'].'';
            }
            
            $this->BuildObj->Arr[] = array('Name' =>'License', 'Desc' => $Desc,  'Type' => 'textarea', 'Value' => $Content, 'Disabled' => 1, 'Col' => 12, 'Row' => 4);
        }
        $this->BuildObj->FormFooterBtnArr = array(
            array('Name' => 'VeriBtn', 'Desc' => '去官方验证', 'Class' => 'success', 'Type' => 'button'),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function redis_Action(){
        $Rs = Config::DbConfig('RedisConfig');
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Host', 'Port', 'IsOpen'))) $this->Err(1001);
            $Ret = $this->setRedis($_POST['Host'], $_POST['Password'], $_POST['Port'], $_POST['IsOpen']);
            if($Ret['Code'] != 0) $this->Err(1000, $Ret['Msg']);
            $this->Jump(array('admin', 'sys', 'redis'), 1888);
        }
        $Rs['Port'] = empty($Rs['Port']) ? '6379' : $Rs['Port'];
        $this->BuildObj->Arr = array(
            array('Name' =>'Host', 'Desc' => '主机Host',  'Type' => 'input', 'Value' => $Rs['Host'], 'Required' => 1, 'Col' => 12),
            array('Name' =>'Password', 'Desc' => '密码',  'Type' => 'input', 'Value' => $Rs['Password'], 'Required' => 1, 'Col' => 12),
            array('Name' =>'Port', 'Desc' => '端口',  'Type' => 'input', 'Value' => $Rs['Port'], 'Required' => 1, 'Col' => 12),
            array('Name' =>'IsOpen', 'Desc' => '开启',  'Type' => 'select', 'Data' => $this->IsArr, 'Value' => $Rs['IsOpen'], 'Required' => 1, 'Col' => 12),
            //array('Name' =>'Value', 'Desc' => '标签默认值',  'Type' => 'input', 'Value' => '', 'Col' => 12),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function check_Action(){
        $ModuleArr = array(
            'curl', 'gd', 'mbstring', 'pdo_mysql', 'iconv', 'date', 'hash', 'json', 'session', 'zip', 'PDO', 'SimpleXML' //'redis',
        );
        $DescArr = array(
            'curl' => 'curl 是常用的命令行工具，用来请求 Web 服务器。它的名字就是客户端（client）的 URL 工具的意思。',
            'gd' => 'php处理图形的扩展库,GD库提供了一系列用来处理图片的API,使用GD库可以处理图片,或者生成图片。',
            'mbstring' => 'mbstring库 全称是Multi-Byte String ,解决各个编码字节数不一致的问题。',
            'pdo_mysql' => 'PDO扩展为PHP访问数据库定义了一个轻量级的、一致性的接口，它提供了一个数据访问抽象层，这样，无论使用什么数据库，都可以通过一致的函数执行查询和获取数据。',
            'iconv' => 'iconv函数库能够完成各种字符集间的转换，是php编程中不可缺少的基础函数库。',
            'date' => 'PHP日期/时间处理扩展',
            'hash' => '信息摘要（hash）引擎。允许使用各种hash算法直接或增量处理任意长度的信息。',
            'json' => '实现了 » JavaScript对象符号（JSON） 数据转换格式。',
            'session' => '会话支持在 PHP 中是在并发访问时由一个方法来保存某些数据.从而使你能够构建更多的定制程序 从而提高你的 web 网站的吸引力。',
            'zip' => '此扩展可以让你透明地读写ZIP压缩文档以及它们里面的文件。',
            'PDO' => 'PHP 数据对象 （PDO） 扩展为PHP访问数据库定义了一个轻量级的一致接口。实现 PDO 接口的每个数据库驱动可以公开具体数据库的特性作为标准扩展功能。',
            'SimpleXML' => 'SimpleXML 扩展提供了一个非常简单和易于使用的工具集，能将 XML 转换成一个带有一般属性选择器和数组迭代器的对象。',
            'redis' => 'PHP支持redis的扩展，可以让你的程序大幅度提高响应速度。',
            'opcache' => 'OPcache 通过将 PHP 脚本预编译的字节码存储到共享内存中来提升 PHP 的性能， 存储预编译字节码的好处就是 省去了每次加载和解析 PHP 脚本的开销。'
            
        );

        $Extensions = get_loaded_extensions();
        $Arr = array();
        foreach($ModuleArr as $k => $v){
            $State = !in_array($v, $Extensions) ? '<span class="label label-danger font-weight-100"><i class="bi bi-x-lg"></i> 未安装</span>' : '<span class="label label-success font-weight-100"><i class="bi bi-check-lg"></i> 已安装</span>';
            $Arr[$k]['Name'] = $v;
            $Arr[$k]['State'] = $State;
            $Arr[$k]['Desc'] = $DescArr[$v];
        }
        $uploadState = !is_writeable(PATH_STATIC.'upload/') ? '<span class="label label-danger font-weight-100"><i class="bi bi-x-lg"></i> 不可写</span>' : '<span class="label label-success font-weight-100"><i class="bi bi-check-lg"></i> 可写</span>';
        $Arr [] = array('Name' => '/Static/upload/', 'Desc' => '检测上传文件夹是否有可写权限', 'State' => $uploadState);
        $backupsState = !is_writeable(PATH_STATIC.'backups/') ? '<span class="label label-danger font-weight-100"><i class="bi bi-x-lg"></i> 不可写</span>' : '<span class="label label-success font-weight-100"><i class="bi bi-check-lg"></i> 可写</span>';
        $Arr [] = array('Name' => '/Static/backups/', 'Desc' => '检测上传文件夹是否有可写权限', 'State' => $backupsState);
        $configState = !is_writeable(PATH_LIB.'Config/') ? '<span class="label label-danger font-weight-100"><i class="bi bi-x-lg"></i> 不可写</span>' : '<span class="label label-success font-weight-100"><i class="bi bi-check-lg"></i> 可写</span>';
        $Arr [] = array('Name' => '/Lib/Config/', 'Desc' => '检测上传文件夹是否有可写权限', 'State' => $configState);
        $KeyArr = array(
            'Name' => array('Name' => '名称', 'Td' => 'th'),
            'Desc' => array('Name' => '说明', 'Td' => 'th'),
            'State' => array('Name' => '状态', 'Td' => 'th'),            
        );
        $this->BuildObj->PrimaryKey = 'UserId';
        $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = $this->BuildObj->IsDel = false;
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
}