<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class File extends ControllersAdmin {
    
    public function index_Action(){
        $Page = intval($_GET['Page']);
        $this->PageNum = 42;
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        $SizeTotal = $this->FileObj->SetField('SUM(Size) AS s')->ExecSelectOne();
        $tmp['SizeTotal'] = $SizeTotal['s'];
        $tmp['Arr'] = $this->FileObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('FileId' => 'DESC'))->ExecSelectAll($Count);
        $tmp['PageBar'] = $this->CommonObj->PageBar($Count, $this->PageNum);
        $this->LoadView('admin/file/index', $tmp);
    }
    
    public function fileView($path, $ext){
        $Html = '';
        if(in_array($ext, array('jpg', 'jepg', 'png', 'gif', 'webp', 'bmp'))){
            $Html = '<div class="image" style="height:120px">
                        <img alt="image" class="img-fluid" src="'.$path.'">
                    </div>';
        }else{
            $Icon = '';
            switch($ext){
                case 'doc':
                    $Icon = 'bi bi-filetype-doc';
                    break;
                case 'xls':
                    $Icon = 'bi bi-filetype-doc';
                    break;
                default:
                    $Icon = 'fa fa-file';
                    break;
            }
            $Html = '<div class="icon" style="height:200px">
                        <i class="'.$Icon.'"></i>
                    </div>';
        }
        return $Html;
    }
    
}