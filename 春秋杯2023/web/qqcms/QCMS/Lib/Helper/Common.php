<?php
namespace Helper;
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name : Collection
 * Date : 20120107
 * Author : Qesy
 * QQ : 762264
 * Mail : 762264@qq.com
 *
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class Common {

	private static $s_instance;
	public $TempArr = array ();
	public $Ret = array('Code' => 0, 'Data' => array(), 'Msg' => '');

	public static function get_instance() {
		if (! isset ( self::$s_instance )) {
			self::$s_instance = new self ();
		}
		return self::$s_instance;
	}

	public function GetQuery(){
	    return empty($_SERVER['QUERY_STRING']) ? array() : explode('=', $_SERVER['QUERY_STRING']);
	}

	public function SetQuery($Key, $Val){
	    $Query = self::GetQuery();
	    $Query[$Key] = $Val;
	    return $Query;
	}
	public function isMobile() {
	    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
	        return true;
	    }
	    if (isset($_SERVER['HTTP_VIA'])) {
	        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false; // 找不到为flase,否则为true
	    }	    
	    if (isset($_SERVER['HTTP_USER_AGENT'])) { // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
	        $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger');
	        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
	        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
	            return true;
	        }
	    }
	    // 协议法，因为有可能不准确，放到最后判断
	    if (isset ($_SERVER['HTTP_ACCEPT'])) {
	        // 如果只支持wml并且不支持html那一定是移动设备
	        // 如果支持wml和html但是wml在html之前则是移动设备
	        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
	            return true;
	        }
	    }
	    return false;
	}
	
	public function rrmdir($src) { //删除文件夹
	    $dir = opendir($src);
	    while(false !== ( $file = readdir($dir)) ) {
	        if (( $file != '.' ) && ( $file != '..' )) {
	            $full = $src . '/' . $file;
	            if ( is_dir($full) ) {
	                self::rrmdir($full);
	            }
	            else {
	                unlink($full);
	            }
	        }
	    }
	    closedir($dir);
	    return @rmdir($src);
	}
	
	public function readAll ($dir, $Filter = array()){
	    if(!is_dir($dir)) return false;
	    $handle = opendir($dir);
	    $Arr = array();
	    if($handle){
	        while(($fl = readdir($handle)) !== false){	 
	            $temp = $dir.DIRECTORY_SEPARATOR.$fl;
	            //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
	            if(is_dir($temp) && $fl!='.' && $fl != '..' && !in_array($fl, $Filter)){
	                $Arr[] = $temp;
	                $SubArr = self::readAll($temp);
	                $Arr = array_merge($Arr, $SubArr);
	            }else{
	                if($fl!='.' && $fl != '..' && !in_array($fl, $Filter)){
	                    $Arr[] = $temp;
	                    //echo '文件：'.$temp.'<br>';
	                }
	            }
	        }
	    }
	    return $Arr;
	}
	
	public function writeIni($Path, $Data, $HasSections = false){
	    $Arr = array();
	    if($HasSections){
	        foreach($Data as $k => $v){
	            $Arr[] = '['.$k.']'.PHP_EOL;
	            foreach($v as $sk => $sv){
	                $Arr[] = $sk.'=\''.$sv.'\''.PHP_EOL;
	            }
	        }
	        $Str = implode('', $Arr);
	        return @file_put_contents($Path, $Str);
	    }
	    foreach($Data as $k => $v) $Arr[] = $k.'='.$v.PHP_EOL;
	    $Str = implode('', $Arr);
	    return @file_put_contents($Path, $Str);
	}
	
	public function delBOM($Content){	    
	    $charset[1] = substr($Content, 0, 1);	    
	    $charset[2] = substr($Content, 1, 1);	    
	    $charset[3] = substr($Content, 2, 1);	    
	    if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
	        return substr($Content, 3);	        
	    }
	    return $Content;
	}

	public function Err($Str){
	    self::ExecScript('alert("'.$Str.'");window.history.go(-1);');
	}

	public function Success($Url, $Str = ''){	    
	    $GetStr = empty($_GET) ? '' : '?'.http_build_query($_GET);
	    if(!empty($Str)){
	        self::ExecScript('alert("'.$Str.'"); window.location.href="'.$Url.$GetStr.'"');
	    }else{
	        self::ExecScript('window.location.href="'.$Url.$GetStr.'"');
	    }
	}

	public function ApiErr($Code, $Msg = '参数错误'){
	    $this->Ret['Code'] = $Code;
	    $this->Ret['Msg'] = $Msg;
	    die(json_encode($this->Ret, JSON_UNESCAPED_UNICODE));
	}

	public function ApiSuccess($Data = array()){
	    $this->Ret['Data'] = $Data;
	    die(json_encode($this->Ret, JSON_UNESCAPED_UNICODE));
	}

	public function TimeView($Ts){
	    if($Ts == 0) return '';
	    $Now = time();
	    list($NowY, $NowM, $NowD) =  explode('-', date('Y-m-d', $Now));
	    list($TsY, $TsM, $TsD) =  explode('-', date('Y-m-d', $Ts));
	    $Time = $Now-$Ts;
	    $Str = '';
	    if($Time < 60){
	        $Str = '刚刚';
	    }elseif($Time >= 60 && $Time < 3600){
	        $Str = intval($Time/60).'分钟前';
	    }elseif($Time >= 3600 && $Time < 86400){
	        $Str = ceil($Time/3600).'小时前';
	    }elseif($Time >= 86400 && $Time < 84600*30){ //本月内
	        $NowWeek = date('W', $Now);
	        $TsWeek = date('W', $Ts);
	        if($NowWeek == $TsWeek){
	            $Str = ceil($Time/86400).'天前';
	        }else{
	            $Str = $NowWeek-$TsWeek.'周前';
	        }
	    }elseif($Time < (84600*365)){ //一年内
	        $Str = round($Time/(86400*30)).'个月前';
	    }else{
	        $Str = $NowY-$TsY.'年前';
	    }
	    return $Str;
	}
	

	public function CreateSn() { // -- Name : 生成编号 --
	    return WEB_PREFIX . '-' . uniqid ( rand ( 100, 999 ), false );
	}

	public function Size($Size){
	    if($Size < 1000) return ''.$Size.' <span class="">B</span>';
	    $Size = round($Size/1000, 2);
	    if($Size < 1000) return ''.$Size.' <span class="">KB</span>';
	    $Size = round($Size/1000, 2);
	    if($Size < 1000) return ''.$Size.' <span class="">MB</span>';
	    $Size = round($Size/1000, 2);
	    return ''.$Size.' <span class="">GB</span>';
	}


	public function  AZ26($n) { //导出excel有用
	    $Letter = range('A', 'Z', 1);
	    $s = '';
	    while ($n > 0) {
	        $m = $n % 26;
	        if ($m == 0)
	            $m = 26;
	        $s = $Letter[$m - 1] . $s;
	        $n = ($n - $m) / 26;
	    }
	    return $s;
	}

	public function GetRefer(){ //获取上一页
	    return htmlentities($_SERVER['HTTP_REFERER']);
	}

	public function GetUa(){ //获取UA
	    return htmlentities($_SERVER['HTTP_USER_AGENT']);
	}

	public function HttpBuildQueryQ($Arr){
	    $RetArr = array();
	    foreach($Arr as $k => $v) $RetArr[] = $k.'='.$v;
	    return implode('&', $RetArr);
	}

	public function ip() { // -- 获取IP --
	    $cip = 0;
	    if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] )) {
	        $cip = $_SERVER ["HTTP_CLIENT_IP"];
	    } elseif (! empty ( $_SERVER ["HTTP_X_FORWARDED_FOR"] )) {
	        $cip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
	    } else if (! empty ( $_SERVER ["REMOTE_ADDR"] )) {
	        $cip = $_SERVER ["REMOTE_ADDR"];
	    }
	    return htmlentities($cip);
	}

	public function Html2Js($Str){
	    $Str = str_replace(array('"', '/', PHP_EOL), array('\"', '\/', ''), $Str);
	    return $Str;
	}

	public function thumb($url, $width, $heiht, $noWaterMark = 0) { // -- 缩略图 --
	    $url = str_replace ( 'source', 'thumb', $url );
	    $ext = substr ( $url, - 4 );
	    $path = substr ( $url, 0, - 4 );
	    return empty ( $noWaterMark ) ? $path . '_w' . $width . '_h' . $heiht . $ext : $path . '_w' . $width . '_h' . $heiht . '_' . $noWaterMark . $ext;
	}

	/* public function LoadView($Temp, $Data = array()) { // -- Name : 加载模版 --
	    if (! is_file ( PATH_SYS . 'View/' . $Temp . EXTEND )) die ( PATH_SYS . 'View/' . $Temp . EXTEND . ' not found !' );

	    $this->TempArr = empty ( $Data ) ? $this->TempArr : $Data;
	    foreach ( $this->TempArr as $Key => $Val ) $$Key = $Val;
	    require PATH_SYS . 'View/' . $Temp . EXTEND;
	} */
    public function LoadCss(array $CssArr, $IsBoot = false) { // -- Name : 加载CSS --
	    $Path = $IsBoot ? URL_BOOT.'css/' : URL_CSS;
	    foreach ( $CssArr as $Val ) echo "<link href=\"" . $Path . $Val . ".css?v=". VERSION ."\" rel=\"stylesheet\" type=\"text/css\" />\n";
	}
	public function loadScripts(array $jsArr, $IsBoot = false) { // -- Name : 加载JS --
	    $Path = $IsBoot ? URL_BOOT.'js/' : URL_JS;
	    foreach ( $jsArr as $key => $val ) echo "<script type=\"text/javascript\" src=\"" . $Path . $val .".js?v=" . VERSION . "\" charset=\"utf-8\"></script>";
	}

	public function ExecScript($Str) { // -- 运行JS --
	    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script>' . $Str . '</script>';
	    exit ();
	}
	public function GoUrl(array $UrlArr) { // -- JS跳转 --
	    self::exec_script ( 'window.location.href="' . self::Url ( $UrlArr ) . '"' );
	    exit ();
	}
	public function Url(array $UrlArr = array ('index')) { // -- 路径函数 --
	    $Url = array ();
	    foreach ( $UrlArr as $key => $val ) {
	        $Url [] = $val;
	    }
	    return URL_ROOT . implode ( '/', $Url ) . '.html';
	}
	
	/**
	 * 压缩文件
	 * @param array $files 待压缩文件 array('d:/test/1.txt'，'d:/test/2.jpg');【文件地址为绝对路径】
	 * @param string $filePath 输出文件路径 【绝对文件地址】 如 d:/test/new.zip
	 * @return string|bool
	 */
	public function Zip($files, $filePath) {
	    //检查参数
	    if (empty($files) || empty($filePath)) {
	        return false;
	    }
	    
	    //压缩文件
	    $zip = new \ZipArchive();
	    $zip->open($filePath, \ZipArchive::CREATE);
	    foreach ($files as $key => $file) {
	        //检查文件是否存在
	        if (!file_exists($file)) {
	            return false;
	        }
	        $zip->addFile($file, basename($file));
	    }
	    $zip->close();
	    
	    return true;
	}
	
	/**
	 * zip解压方法
	 * @param string $filePath 压缩包所在地址 【绝对文件地址】d:/test/123.zip
	 * @param string $path 解压路径 【绝对文件目录路径】d:/test
	 * @return bool
	 */
	public function UnZip($filePath, $path) {
	    if (empty($path) || empty($filePath)) {
	        return false;
	    }
	    
	    $zip = new \ZipArchive();
	    
	    if ($zip->open($filePath) === true) {
	        $zip->extractTo($path);
	        $zip->close();
	        return true;
	    } else {
	        return false;
	    }
	}

	/**
	 * 文件夹文件拷贝
	 *
	 * @param string $src 来源文件夹
	 * @param string $dst 目的地文件夹
	 * @return bool
	 */
	public function DirCopy($Src = '', $Dst = ''){
	    if (empty($Src) || empty($Dst)){
	        return false;
	    }
	    $Dir = opendir($Src);
	    $this->DirMkDir($Dst);
	    while (false !== ($file = readdir($Dir))){
	        if (($file != '.') && ($file != '..')){
	            if (is_dir($Src . '/' . $file)){
	                $this->DirCopy($Src . '/' . $file, $Dst . '/' . $file);
	            }else{
	                copy($Src . '/' . $file, $Dst . '/' . $file);
	            }
	        }
	    }
	    closedir($Dir);
	    return true;
	}
	
	/**
	 * 创建文件夹
	 *
	 * @param string $path 文件夹路径
	 * @param int $mode 访问权限
	 * @param bool $recursive 是否递归创建
	 * @return bool
	 */
	public function DirMkDir($path = '', $mode = 0777, $recursive = true){
	    clearstatcache();
	    if (!is_dir($path)){
	        mkdir($path, $mode, $recursive);
	        return chmod($path, $mode);
	    }
	    return true;
	}
	
	public function PageBar($Count, $Size, $IsSimple = false) { // -- 分页 --
	    $Num = 9;
	    $PageNum = !empty($_GET['Page']) ? intval($_GET['Page']) : 1;
	    $Url = URL_ROOT.URL_CURRENT;
	    if ($Count <= 0) return '';
	    $Toall = ceil ( $Count / $Size );
	    ($PageNum <= $Toall) || $PageNum = $Toall;
	    $JumpGet = $PreGet = $NextGet = $PageListGet = $_GET;
	    $PreGet['Page'] = ($PageNum <= 1) ? 1 : $PageNum-1;
	    $PreUrl = $Url.'?'.http_build_query($PreGet);
	    $PreStr = '<li class="page-item '.(($PageNum == 1) ? 'disabled' : '').'"><a href="' . $PreUrl . '" class="page-link">上一页</a></li>';
	    $NextGet['Page'] = ($PageNum >= $Toall) ? 1 : $PageNum+1;
	    $NextUrl = $Url.'?'.http_build_query($NextGet);
	    $NextStr = '<li class="page-item '.(($PageNum == $Toall) ? 'disabled' : '').'"><a href="' . $NextUrl . '" class="page-link">下一页</a></li>';
	    $PageListGet['Page'] = 1;
	    $FirstPage = '<li class="page-item '.(($PageNum == 1) ? 'disabled' : '').'"><a href="'.$Url.'?'.http_build_query($PageListGet).'" class="page-link">首页</a></li>';
	    $PageListGet['Page'] = $Toall;
	    $LastPage = '<li class="page-item '.(($PageNum == $Toall) ? 'disabled' : '').'"><a href="'.$Url.'?'.http_build_query($PageListGet).'" class="page-link">尾页</a></li>';
	    $Start = $End = 1;
	    $ToallStr = $Str = '';
	    if ($Toall <= $Num) {
	        $Start = 1;
	        $End = $Toall;
	    } elseif (($Toall - $PageNum) > ceil ( $Num / 2 ) && $PageNum < ceil ( $Num / 2 )) {
	        $Start = 1;
	        $End = $Num;
	    } elseif (($Toall - $PageNum) < ceil ( $Num / 2 )) {
	        $Start = ($Toall - $Num + 1);
	        $End = $Toall;
	    } else {
	        $Start = ($PageNum - floor ( $Num / 2 ));
	        $End = ($PageNum + floor ( $Num / 2 ));
	    }
	    for($i = $Start; $i <= $End; $i ++) {
	        $PageListGet['Page'] = $i;
	        $Str .= ($PageNum == $i) ? '<li class="page-item active"><a class="page-link">' . $i . '</a></li>' : '<li class="page-item"><a href="' . $Url.'?'.http_build_query($PageListGet). '" class="page-link">' . $i . '</a></li>';
	    }
	    unset($JumpGet['Page']);
	    $Jump = "
		    <div class='input-group input-group-sm mb-3 p-1' style='width:80px'>
                <input type='text' class='form-control'  id='QFramePageNum' value='".$PageNum."'>
	                <div class='input-group-append'>
	                <button class='btn btn-primary' type='button' onclick='QFramePageJump()'>GO!</button>
	                </div>
	                </div>
	                <script>
	                function QFramePageJump(){
	                window.location.href='{$Url}?".http_build_query($JumpGet)."&P='+document.getElementById('QFramePageNum').value+'';
            }
		    </script>";
	    $Jump = '';
	    if($IsSimple) return '<ul class="pagination justify-content-center py-3 my-0">'.$FirstPage . $PreStr . $NextStr . $ToallStr . $LastPage.'<li class="page-item  disabled "><a  class="page-link">总'.$Count.'条</a></li>'.$Jump.'</ul>';
	    return '<ul class="pagination justify-content-center py-3 my-0">'.$FirstPage . $PreStr . $Str . $NextStr . $ToallStr . $LastPage.'<li class="page-item  disabled "><a  class="page-link">总'.$Count.'条</a></li>'.$Jump.'</ul>';
	}


	/**
	 * 生成缩略图
	 * @param string   源图绝对完整地址{带文件名及后缀名}
	 * @param string   目标图绝对完整地址{带文件名及后缀名}
	 * @param int    缩略图宽{值设为0时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
	 * @param int    缩略图高{值设为0时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
	 * @param int    是否裁切{宽,高必须非0}
	 * @param int/float 缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
	 * @return boolean
	 */
	public function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
	{
	    if(!is_file($src_img))
	    {
	        return false;
	    }
	    $ot = pathinfo($dst_img, PATHINFO_EXTENSION);
	    $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
	    $srcinfo = getimagesize($src_img);
	    $src_w = $srcinfo[0];
	    $src_h = $srcinfo[1];
	    $type = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
	    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
	    $dst_h = $height;
	    $dst_w = $width;
	    $x = $y = 0;
	    /**
	     * 缩略图不超过源图尺寸（前提是宽或高只有一个）
	     */
	    if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
	    {
	        $proportion = 1;
	    }
	    if($width> $src_w)
	    {
	        $dst_w = $width = $src_w;
	    }
	    if($height> $src_h)
	    {
	        $dst_h = $height = $src_h;
	    }
	    if(!$width && !$height && !$proportion)
	    {
	        return false;
	    }
	    if(!$proportion)
	    {
	        if($cut == 0)
	        {
	            if($dst_w && $dst_h)
	            {
	                if($dst_w/$src_w> $dst_h/$src_h)
	                {
	                    $dst_w = $src_w * ($dst_h / $src_h);
	                    $x = 0 - ($dst_w - $width) / 2;
	                }
	                else
	                {
	                    $dst_h = $src_h * ($dst_w / $src_w);
	                    $y = 0 - ($dst_h - $height) / 2;
	                }
	            }
	            else if($dst_w xor $dst_h)
	            {
	                if($dst_w && !$dst_h) //有宽无高
	                {
	                    $propor = $dst_w / $src_w;
	                    $height = $dst_h = $src_h * $propor;
	                }
	                else if(!$dst_w && $dst_h) //有高无宽
	                {
	                    $propor = $dst_h / $src_h;
	                    $width = $dst_w = $src_w * $propor;
	                }
	            }
	        }
	        else
	        {
	            if(!$dst_h) //裁剪时无高
	            {
	                $height = $dst_h = $dst_w;
	            }
	            if(!$dst_w) //裁剪时无宽
	            {
	                $width = $dst_w = $dst_h;
	            }
	            $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
	            $dst_w = (int)round($src_w * $propor);
	            $dst_h = (int)round($src_h * $propor);
	            $x = ($width - $dst_w) / 2;
	            $y = ($height - $dst_h) / 2;
	        }
	    }
	    else
	    {
	        $proportion = min($proportion, 1);
	        $height = $dst_h = $src_h * $proportion;
	        $width = $dst_w = $src_w * $proportion;
	    }
	    $src = $createfun($src_img);
	    $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
	    $white = imagecolorallocate($dst, 255, 255, 255);
	    imagefill($dst, 0, 0, $white);
	    if(function_exists('imagecopyresampled'))
	    {
	        imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
	    }
	    else
	    {
	        imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
	    }
	    $otfunc($dst, $dst_img);
	    imagedestroy($dst);
	    imagedestroy($src);
	    return true;
	}


}