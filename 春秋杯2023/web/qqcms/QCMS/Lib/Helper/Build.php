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
class Build {
    private static $s_instance;
    public $Arr = array();
    public $Html;
    public $Js;
    public $Module = 'admin';
    public $PrimaryKey = 'Id';
    public $IsAdd = true;
    public $IsEdit = true;
    public $IsDel = true;
    public $IsSubmit = true;
    public $IsBack = false;
    public $LinkIndex;
    public $LinkExport;
    public $LinkAdd;
    public $LinkEdit;
    public $LinkDel;
    public $NameAdd = '添加';
    public $NameEdit = '修改';
    public $NameDel = '删除';
    public $NameSubmit = '提交';
    public $UploadUrl;
    public $UploadEditUrl;
    public $UploadEditFileUrl;
    public $FormStyle = 1; //1 是正常 2 inline
    public $TableSelectIndex = -1; //选中的行
    public $FormMultipleSelectIndex = 0; //选中的行
    public $FormMultipleMerge = true; //是否合并提交
    public $TableTopBtnArr = array(); //表格顶部按钮
    public $TableFooterBtnArr = array(); //表格底部按钮
    public $FormFooterBtnArr = array(); //表单底部按钮
    public static function get_instance() {
        if (! isset ( self::$s_instance )) {
            self::$s_instance = new self ();
        }
        return self::$s_instance;
    }
    public $CommObj;
    function __construct(){
        $this->CommObj = Common::get_instance();
    }
    
    public function FormMultipleTitle(){
        $NavArr = array();
        foreach($this->Arr as $k => $v){
            $Active = ($k == $this->FormMultipleSelectIndex) ? 'active' : '';
            $NavArr[] = '<li class="'.$Active.'" ><a class="tabBnt" href="#" data-index="'.$k.'">'.$v['Title'].'</a></li>';
        }
        return '<div class="tab-struct custom-tab-1"><ul role="tablist" class="nav nav-tabs">'.implode(PHP_EOL, $NavArr).'</ul></div>';
    }
    
    public function FormTitle($Str){
        return '<div class="tab-struct custom-tab-1">
                <ul role="tablist" class="nav nav-tabs">
                    <li class="active"><a class="tabBnt" href="#" data-index="0">'.$Str.'</a></li>
                </ul></div>';
    }
    
    public function FormMultiple($Method = 'POST', $Class = '', $ExtHtml = ''){ //多页签表单
        $Arr = $this->Arr;
        $Html = '';
        foreach($Arr as $k => $v){
            $this->Arr = $v['Form'];
            self::Form($Method, $Class, $ExtHtml, $k);
            $Html .= $this->Html;
        }
        $this->Arr = $Arr;
        $this->Html = !$this->FormMultipleMerge ? $Html : '<form method="'.$Method.'" class="BuildForm ">'.$Html.'</form>';
    }
    
    public function Form($Method = 'POST', $Class = '', $ExtHtml = '', $MultipleKey = -1){
        if(!is_array($this->Arr)) return;
        $this->UploadUrl = !empty($this->UploadUrl) ? $this->UploadUrl : $this->CommObj->Url(array('backend', 'index', 'ajaxUpload'));
        $this->UploadEditUrl = !empty($this->UploadEditUrl) ? $this->UploadEditUrl : $this->CommObj->Url(array('backend', 'index', 'uploadEditor'));
        $this->LinkIndex = !empty($this->LinkIndex) ? $this->LinkIndex : $this->CommObj->Url(array($this->Module, \Router::$s_Controller, 'index'));
        $this->LinkExport = !empty($this->LinkExport) ? $this->LinkExport : $this->CommObj->Url(array($this->Module, \Router::$s_Controller, 'export'));
        self::_Clean();
        $this->FormStyle = ($Class == 'form-inline') ? 2 : 1;
        $this->Html = ($MultipleKey == -1) ? '<form method="'.$Method.'" class="BuildForm '.$Class.'">' : '<div class="w-100 '.$Class.' '.(($MultipleKey == $this->FormMultipleSelectIndex) ? '' : 'd-none').'" id="Key_'.$MultipleKey.'">';
        if(!$this->FormMultipleMerge) $this->Html .= '<form method="'.$Method.'" class="BuildForm '.$Class.'">';
        foreach($this->Arr as $k => $v){
            if(empty($v['Col']) && $Class != 'form-inline') $v['Col'] = 12;
            $v['Required'] = !isset($v['Required']) ? 2 : $v['Required'];
            switch ($v['Type']){
                case 'formgroup':
                    $this->Html .= self::_FromGroup($v['Col'], $v['Desc']); break;
                case 'radio':
                    $this->Html .= self::_FormRadio($v['Name'], $v['Desc'], $v['Value'], $v['Data'], $v['Col'], $v['Disabled'], $v['Required']); break;
                case 'checkbox':
                    $this->Html .= self::_FormCheckbox($v['Name'], $v['Desc'], $v['Value'], $v['Data'], $v['Col'], $v['Disabled'], $v['Required']); break;
                case 'select':
                    $this->Html .= self::_FromSelect($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Disabled'], $v['Required']); break;
                case 'upload':
                    $this->Html .= self::_FormUpload($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder'], $v['Required']);
                    break;
                case 'uploadBatch':
                    $this->Html .= self::_FormUploadBatch($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder']);
                    break;
                case 'slide':
                    $this->Html .= self::_FormSlide($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder']);
                    break;
                case 'textarea':
                    $this->Html .= self::_FormTextarea($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder'], $v['Required'], $v['Row']); break;
                case 'editor':
                    $this->Html .= self::_FormEditor($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder'], $v['Required']);
                    break;
                case 'money':
                    $this->Html .= self::_FromMoney($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder']); break;
                case 'date':
                    $this->Html .= self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Type'], $v['Disabled'], $v['Placeholder'], $v['Required']); break;
                case 'month':
                    $this->Html .= self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Type'], $v['Disabled'], $v['Placeholder'], $v['Required']); break;
                case 'datetime':
                    $v['Type'] = 'datetime-local';
                    $this->Html .= self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Type'], $v['Disabled'], $v['Placeholder'], $v['Required']); break;
                case 'time':
                    $this->Html .= self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], 'time', $v['Disabled'], $v['Placeholder']); break;
                case 'password':
                    $this->Html .= self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], 'password', $v['Disabled'], $v['Placeholder'], $v['Required']); break;
                case 'button':
                    if(empty($v['ButtonType'])) $v['ButtonType'] = 'submit';
                    if(empty($v['Class'])) $v['Class'] = 'primary';
                    $this->Html .= self::_FromButton($v['Name'], $v['Desc'], $v['ButtonType'], $v['Col'], $v['Class']);break;
                case 'butonGroup':                    
                    $this->Html .= self::_FromButtonGroup($v['ButtonArr'], $v['Col']);break;
                    break;
                case 'link':
                    if(empty($v['Class'])) $v['Class'] = 'primary';
                    $this->Html .= self::_FromLink($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Class']);break;
                case 'html':
                    if(empty($v['Class'])) $v['Class'] = 'primary';
                    $this->Html .= self::_html($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Class']);break;
                case 'htmlFill':
                    if(empty($v['Class'])) $v['Class'] = 'primary';
                    $this->Html .= self::_html($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Class'], 1);break;
                case 'htmlStart':
                    $this->Html .= self::_htmlStart($v['Name'], $v['Col']);break;
                case 'htmlEnd':
                    $this->Html .= self::_htmlEnd();break;
                case 'hidden':
                    if(empty($v['Class'])) $v['Class'] = 'primary';
                    $this->Html .= self::_Hidden($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Class']);break;
                case 'buttonGroup':
                    $this->Html .= self::_ButtonGroup($v['Name'], $v['Desc'], $v['Value'], $v['Data'], $v['Col'], $v['Disabled']);
                    break;
                case 'diy':
                    $this->Html .= self::_diy($v['Name'], $v['Desc'],$v['Col']);
                    break;
                case 'color':
                    $this->Html .= self::_ColorInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], 'text', $v['Disabled'], $v['Placeholder'], $v['Required']); break;
                default:
                    $this->Html .= self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], 'text', $v['Disabled'], $v['Placeholder'], $v['Required']); break;
            }
        }
        if($Class != 'form-inline') $Col = 12;
        $ButtonArr = array();
        if($this->IsSubmit) $ButtonArr[] = array('Name' => 'submit', 'Type' => 'submit', 'Desc' => $this->NameSubmit);
        if($this->IsBack) $ButtonArr[] = array('Name' => 'back', 'Type' => 'button', 'Desc' => '返回');
        foreach($this->FormFooterBtnArr as $v) $ButtonArr[] = $v;
        $this->Html .= self::_FromButtonGroup($ButtonArr, $Class);  
        $this->Html .= $ExtHtml;
        if(!$this->FormMultipleMerge) $this->Html .= '</form>';
        $this->Html .= ($MultipleKey == -1) ? '</form>' : '</div>';       
    }
    
    public function FormOne($v){
        $Html = '';
        switch ($v['Type']){
            case 'formgroup':
                $Html = self::_FromGroup($v['Col'], $v['Desc']); 
                break;
            case 'radio':
                $Html = self::_FormRadio($v['Name'], $v['Desc'], $v['Value'], $v['Data'], $v['Col'], $v['Disabled'], $v['Required']); 
                break;
            case 'checkbox':
                $Html = self::_FormCheckbox($v['Name'], $v['Desc'], $v['Value'], $v['Data'], $v['Col'], $v['Disabled'], $v['Required']); 
                break;
            case 'select':
                $Html = self::_FromSelect($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Disabled'], $v['Required']); 
                break;
            case 'upload':
                $Html = self::_FormUpload($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder'], $v['Required']);
                break;
            case 'slide':
                $Html = self::_FormSlide($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder']);
                break;
            case 'textarea':
                $Html = self::_FormTextarea($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'],  $v['Placeholder'], $v['Required'], $v['Row']); 
                break;
            case 'editor':
                $Html = self::_FormEditor($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder'], $v['Required']);
                break;
            case 'money':
                $Html = self::_FromMoney($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Disabled'], $v['Placeholder']); 
                break;
            case 'date':
                $Html = self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], 'date', $v['Disabled'], $v['Placeholder'], $v['Required']); 
                break;
            case 'password':
                $Html = self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], 'password', $v['Disabled'], $v['Placeholder'], $v['Required']); 
                break;
            case 'button':
                if(empty($v['ButtonType'])) $v['ButtonType'] = 'submit';
                if(empty($v['Class'])) $v['Class'] = 'primary';
                $Html = self::_FromButton($v['Name'], $v['Desc'], $v['ButtonType'], $v['Col'], $v['Class']);
                break;
            case 'link':
                if(empty($v['Class'])) $v['Class'] = 'primary';
                $Html = self::_FromLink($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Class']);
                break;
            case 'html':
                if(empty($v['Class'])) $v['Class'] = 'primary';
                $Html = self::_html($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Class']);
                break;
            case 'hidden':
                if(empty($v['Class'])) $v['Class'] = 'primary';
                $Html = self::_Hidden($v['Name'], $v['Desc'], $v['Value'], $v['Col'], $v['Data'], $v['Class']);
                break;
            case 'color':
                if(empty($v['Class'])) $v['Class'] = 'primary';
                $Html = self::_ColorInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], 'text', $v['Disabled'], $v['Placeholder'], $v['Required']);
                break;
            default:
                $Html= self::_FromInput($v['Name'], $v['Desc'], $v['Value'], $v['Col'], 'text', $v['Disabled'], $v['Placeholder'], $v['Required']); 
                break;
        }
        return $Html;
    }
    
    private function _FromLink($Name, $Desc, $Value, $Col = 12, $Data = '_blank', $Class = 'btn-success ml-2'){ //链接
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        return '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'"><a href="'.$Value.'" class="btn '.$Class.'" target="'.$Data.'">'.$Desc.'</a></a></div>';
    }
    
    private function _Html($Name, $Desc, $Value, $Col = 12, $Data = '_blank', $Class = 'btn-success ml-2', $IsFill = 2){
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $IsFillStr = ($IsFill == 1) ? 'd-none d-lg-block' : '';
        return '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.' '.$IsFillStr.'" ><label for="Input_'.$Name.'">'.$Desc.'</label><div class="'.$Class.'">'.$Value.'</div></div>';
    }
    
    private function _HtmlStart($Name, $Col = 12){
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        return '<div class="form-group row col-'.$SubCol.'  col-lg-'.$Col.' htmlClass" id="Html_'.$Name.'" style="margin:-10px;">';
    }
    
    private function _HtmlEnd(){
        return '</div>';
    }
    
    private function _Hidden($Name, $Desc, $Value, $Col = 12, $Data = '_blank', $Class = 'btn-success ml-2'){
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        return '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.' d-none">
                        <label for="Input_'.$Name.'">'.$Desc.'</label>
                        <input type="input" class="form-control" name="'.$Name.'" id="Input_'.$Name.'" value="'.$Value.'">
                    </div>';
    }
    
    private function _FromButton($Name, $Desc, $Type, $Col = 12, $Class = 'primary'){ //Button
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        if($Type == 'back'){
            return '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'"><button type="button" onclick="history.go(-1)" class="btn btn-'.$Class.' '.(($this->FormStyle == 2) ? 'btn-xs' : '').'" id="Button_'.$Name.'">'.$Desc.'</button></div>';
        }
        return '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'"><button type="'.$Type.'" class="btn btn-'.$Class.' '.(($this->FormStyle == 2) ? 'btn-xs' : '').'" id="Button_'.$Name.'">'.$Desc.'</button></div>';
    }
    
    private function _FromButtonGroup($ButtonArr, $FormClass = '', $Col = 12){ //Button
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $Html = '';
        foreach($ButtonArr as $v){
            $Class = isset($v['Class']) ? $v['Class'] : 'primary';
            $Type = isset($v['Type']) ? $v['Type'] : 'submit';
            $IsBack = ($v['Name'] == 'back') ? 'onclick="history.go(-1)"' : '';
            if(isset($v['Type']) && $v['Type'] == 'Link'){
                $Html .= '<a href="'.$v['Url'].'" class="mr-2 btn btn-'.$Class.' " id="Button_'.$v['Name'].'">'.$v['Desc'].'</a>';
            }else{
                $Html .= '<button type="'.$Type.'" '.$IsBack.' class="mr-2 btn btn-'.$Class.' " id="Button_'.$v['Name'].'">'.$v['Desc'].'</button>';
            }
            
        }
        return '<div class="form-group '.(($FormClass == 'form-inline') ? '' : 'col-'.$SubCol.'  col-lg-'.$Col).'">'.$Html.'</div>';
    }
    
    private function _FormRadio($Name, $Desc, $Value, $DataArr = array(),  $Col, $IsDisabled = 0, $Required = 0){
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $Str = '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'"><label  class="mr-3 mb-1 d-block">'.$Desc.$RequiredViewStr.'</label>';
        foreach($DataArr as $k => $v){
            $Checked = ($Value == $k) ? 'checked="checked"' : '';
            $Str .= '<label class="radio-inline mr-3 text-dark py-2 h6"><input type="radio" name="'.$Name.'"  value="'.$k.'" '.$Checked.'> '.$v.'</label>';
        }
        $Str .= '</div>';
        return $Str;
    }
    
    private function _FormCheckbox($Name, $Desc, $Value, $DataArr = array(),  $Col, $IsDisabled = 0, $Required = 0){ //Checkbox
        $ValueArr = explode('|', $Value);
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $Str = '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'"><label  class="mr-3 mb-1 d-block">'.$Desc.$RequiredViewStr.'</label>';
        foreach($DataArr as $k => $v){
            $Checked = in_array($k, $ValueArr) ? 'checked="checked"' : '';
            $Str .= '<div class="checkbox checkbox-primary float-left pl-1 pr-4 py-2"><input type="checkbox" name="'.$Name.'['.$k.']"  value="1" '.$Checked.' id="'.$Name.'_'.$k.'" > <label class="text-dark" for="'.$Name.'_'.$k.'" >'.$v.'</label></div>';
        }
        $Str .= '</div>';
        return $Str;
    }
    
    private function _ButtonGroup($Name, $Desc, $Value, $DataArr = array(),  $Col, $IsDisabled = 0){ //Checkbox
        $ValueArr = explode('|', $Value);
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $Str = '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'">
                    <label  class="mr-3 font-weight-bold">'.$Desc.'</label>
                        <div class="selectgroup selectgroup-pills">';
        foreach($DataArr as $k => $v){
            $Str .= '
			<button type="button"  class="btn btn-primary mr-1 btn-sm btn-round Button_'.$Name.'"  data="'.$k.'">
			'.$v.'</button>
		';
        }
        $Str .= '</div></div>';
        return $Str;
    }
    
    private function _FromSelect($Name, $Desc, $Value, $Col, $DataArr = array(),  $IsDisabled = 0, $Required = 0){ //select
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Name  ;
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $Class = ($this->FormStyle == 2) ? '' : 'col-'.$SubCol.'  col-lg-'.$Col;
        $Str = '<div class="form-group '.$Class.'"><label for="Input_'.$Name.'" class="mb-1 '.(($this->FormStyle == 2) ? 'mr-2' : '').'">'.$Desc.$RequiredViewStr.'</label><select class="form-control '.(($this->FormStyle == 2) ? 'form-control-sm' : '').'" name="'.$Name.'" id="Input_'.$Name.'" '.$Disabled.' '.$RequiredStr.'>';
        $Str .= '<option value="" >请选择'.$Desc.'</option>';
        foreach($DataArr as $sk => $sv){
            $selected = ($sk == $Value) ? 'selected' : '';
            $Str .= '<option value="'.$sk.'" '.$selected.'>'.$sv.'</option>';
        }
        $Str .= '</select></div>';
        return $Str;
    }
    
    private function _FormUploadBatch($Name, $Desc, $Value, $Col, $IsDisabled = 0, $Placeholder = ''){
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Desc  ;
        $StrHtml = '';
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $StrHtml .= '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'">
                                    <label for="Input_'.$Name.'"><button type="button" Id="Img_'.$Name.'" class="btn btn-primary btn-sm">上传图片</button>  </label>
                                    <input type="hidden" name="'.$Name.'" id="SlideInput">
                                    <div class="form-group p-0">
                                      <div class="d-flex " id="SlideArrHtml">
                                      </div>
                                    </div>
                                  </div> ';
        return $StrHtml;
    }
    
    private function _FormUpload($Name, $Desc, $Value, $Col, $IsDisabled = 0, $Placeholder = '', $Required = 0){
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Desc  ;
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $StrHtml = '';
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $StrHtml .= '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.' uploadDiv" data="'.$Name.'">
                                    <label class="mb-1" for="Input_'.$Name.'">'.$Desc.'</label>'.$RequiredViewStr.'
                                    <div class="input-group">
                                       <div class="input-group-prepend">
    <button type="button" class="btn btn-sm btn-secondary" id="ViewImg_'.$Name.'"><i class="bi bi-image text-white"></i></button>
  </div>
                                      <input type="text" class="form-control" '.$Disabled.' placeholder="'.$Placeholder.'" name="'.$Name.'" Id="Img_'.$Name.'" value="'.$Value.'" '.$RequiredStr.'>
                                      <span class="input-group-append">
                                        <button class="btn btn-success" id="uploadImg_'.$Name.'" type="button" '.$Disabled.'>上传</button>
                                      </span>
                                             <span class="input-group-append">
                                        <button class="btn btn-danger browseBtn" data-name="'.$Name.'"  type="button">浏览</button>
                                      </span>
                                    </div>
                                  </div> ';
        return $StrHtml;
    }
    
    
    private function _FormSlide($Name, $Desc, $Value, $Col, $IsDisabled = 0, $Placeholder = ''){ //多图
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Name  ;
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $StrHtml = '<div class="col-'.$SubCol.'  col-lg-'.$Col.'"><label for="Input_'.$Name.'">'.$Desc.'</label>';
        $StrJs = '';
        $ValueArr = explode('|', $Value);
        foreach($ValueArr as $sk => $sv){
            $StrHtml .= '<div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" '.$IsDisabled.' placeholder="'.$Placeholder.'" name="'.$Name.'[]" Id="Img_'.$Name.'_'.$sk.'" value="'.$sv.'">
                                <span class="input-group-btn"><button class="btn btn-success" id="uploadImg_'.$Name.'_'.$sk.'" type="button">上传图片</button></span>
                            </div>
                        </div> ';
        }
        $StrHtml .= '</div>';
        return $StrHtml;
    }
    
    private function _FormEditor($Name, $Desc, $Value, $Col, $IsDisabled = 0, $Placeholder = '', $Required = 0){ //编辑器
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Name  ;
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $StrHtml = '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'">
                        <label for="Input_'.$Name.'">'.$Desc.'</label>'.$RequiredViewStr.'
                        <textarea class="form-control Input_Editor" name="'.$Name.'" '.$IsDisabled.' rows="16" id="Input_'.$Name.'" placeholder="'.$Placeholder.'" >'.$Value.'</textarea>
                    </div>';
        return $StrHtml;
    }
    
    /* 	private function _FormEditor($Name, $Desc, $Value, $Col, $IsDisabled = 0, $Placeholder = ''){ //编辑器
     $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
     if(empty($Placeholder)) $Placeholder =  '请输入'.$Name  ;
     $StrHtml = '<div class="form-group col-'.$Col.'">
     <label for="Input_'.$Name.'">'.$Desc.'</label>
     <textarea class="form-control" name="'.$Name.'" '.$IsDisabled.' rows="16" placeholder="'.$Placeholder.'">'.$Value.'</textarea>
     </div>';
     $StrJs = 'var editor;
     KindEditor.ready(function(K) {
     editor = K.create(\'textarea[name="'.$Name.'"]\', {
     allowFileManager : true,
     themeType : "simple",
     urlType : "absolute",
     uploadJson : "'.$this->UploadEditUrl.'",
     fileManagerJson : "'.$this->UploadEditFileUrl.'",
     items : ["source","code","fontname", "fontsize", "|", "forecolor", "hilitecolor", "bold", "italic", "underline",
     "removeformat", "|", "justifyleft", "justifycenter", "justifyright", "insertorderedlist",
     "insertunorderedlist", "|", "image", "flash", "media","insertfile","link","unlink","|","table","fullscreen"]
     })
     });';
     return array($StrHtml, $StrJs);
     } */
    
    private function _FromMoney($Name, $Desc, $Value, $Col, $IsDisabled = 0, $Placeholder = ''){ //金钱
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Desc  ;
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        return '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'">
                        <label for="Input_'.$Name.'">'.$Desc.'</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text">&yen;</span></div>
                                <input type="text" class="form-control" name="'.$Name.'" '.$IsDisabled.' id="Input_'.$Name.'" placeholder="'.$Placeholder.'" value="'.$Value.'">
                                <div class="input-group-append"><span class="input-group-text">.00</span></div>
                            </div>
                    </div>';
    }
    
    private function _diy($Name, $Desc, $Col ){ //输入框
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);        
        $Class = ($this->FormStyle == 2) ? '' : 'col-'.$SubCol.'  col-lg-'.$Col;
        return '<div class="form-group '.$Class.'">'.$Desc.'</div>';
    }
    
    private function _FromInput($Name, $Desc, $Value, $Col, $Type = 'text', $IsDisabled = 0, $Placeholder = '', $Required = 2){ //输入框
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Desc  ;
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $Class = ($this->FormStyle == 2) ? '' : 'col-'.$SubCol.'  col-lg-'.$Col;
        return '<div class="form-group '.$Class.'">
        <label for="Input_'.$Name.'" class="'.(($this->FormStyle == 2) ? 'mr-2' : '').' mb-1">'.$Desc.'</label>'.$RequiredViewStr.'
        <input type="'.$Type.'" '.$Disabled.' class="form-control '.(($this->FormStyle == 2) ? 'form-control-sm' : '').'" name="'.$Name.'" id="Input_'.$Name.'" placeholder="'.$Placeholder.'" value="'.$Value.'" '.$RequiredStr.'>'.PHP_EOL.'</div>';
    }
    
    private function _ColorInput($Name, $Desc, $Value, $Col, $Type = 'text', $IsDisabled = 0, $Placeholder = '', $Required = 0){ //输入框
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Desc  ;
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $Class = ($this->FormStyle == 2) ? '' : 'col-'.$SubCol.'  col-lg-'.$Col;
        return '<div class="form-group '.$Class.'">
                        <label for="Input_'.$Name.'" class="'.(($this->FormStyle == 2) ? 'mr-2' : '').' mb-1">'.$Desc.'</label>'.$RequiredViewStr.'
                        <div class="colorpicker input-group colorpicker-component colorpicker-element">
							<input type="text" value="#00AABB" class="form-control">
							<span class="input-group-addon border border-left-0" style="padding: 6px 12px;"><i style="background-color: rgb(219, 56, 46);"></i></span>
						</div>
                            
                    </div>';
    }
    
    private function _DateTimeInput($Name, $Desc, $Value, $Col, $Type = 'text', $IsDisabled = 0, $Placeholder = '', $Required = 0){ //输入框
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Desc  ;
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        return '
                    <div class="form-group col-'.$SubCol.' col-lg-'.$Col.' "  id="Form_'.$Name.'">
                        <label for="Input_'.$Name.'">'.$Desc.'</label>'.$RequiredViewStr.'
                            <div class="input-group date '.$Type.'Only" id="FormDate_'.$Name.'" data-target-input="nearest">
                            <input type="text" '.$Disabled.' class="form-control datetimepicker-input " name="'.$Name.'" value="'.$Value.'" data-target="#FormDate_'.$Name.'" '.$RequiredStr.'/>
                            <div class="input-group-append" data-target="#FormDate_'.$Name.'" data-toggle="datetimepicker"  >
                        <div class="input-group-text"><i class="fa fa-calendar"></i>&nbsp;</div>
                    </div>
                     </div></div>';
    }
    
    private function _FromGroup($Col, $Desc){ //填充而已
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        return '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.' d-none d-lg-block">'.$Desc.'
                    </div>';
    }
    
    private function _FormTextarea($Name, $Desc, $Value, $Col, $IsDisabled = 0, $Placeholder = '', $Required = 0, $Row = 4){ //输入框
        $Disabled = ($IsDisabled) ? 'disabled="disabled"' : '';
        if(empty($Placeholder)) $Placeholder =  '请输入'.$Desc  ;
        $RequiredViewStr = $RequiredStr = '';
        if($Required == 1){
            $RequiredStr = 'required="required"';
            $RequiredViewStr = '<span class="text-danger ml-2" style="font-weight: 900;">*</span>';
        }
        $SubCol = ($Col*2 > 12) ? 12 : ($Col*2);
        return '<div class="form-group col-'.$SubCol.'  col-lg-'.$Col.'">
                        <label for="Input_'.$Name.'" class="mb-1">'.$Desc.'</label>'.$RequiredViewStr.'
                        <textarea class="form-control" name="'.$Name.'" '.$Disabled.' rows="'.$Row.'" id="Input_'.$Name.'" placeholder="'.$Placeholder.'" '.$RequiredStr.'>'.$Value.'</textarea>
                      </div>';
    }
    
    
    /*
     *  $keyArr = array('name' => ''标题'');
     */
    public function Table(array $arr, $keyArr, $Page = '', $Class= '', $IsResponsive = 2){
        $num = count($keyArr);
        if(empty($this->LinkAdd)) $this->LinkAdd = $this->CommObj->Url(array($this->Module, \Router::$s_Controller, 'add')).'?'.http_build_query($_GET);
        if(empty($this->LinkEdit)) $this->LinkEdit = $this->CommObj->Url(array($this->Module, \Router::$s_Controller, 'edit'));
        if(empty($this->LinkDel)) $this->LinkDel = $this->CommObj->Url(array($this->Module, \Router::$s_Controller, 'del'));
        $str = '<table class="table '.$Class.'"><thead><tr>';
        foreach($keyArr as $k => $v){
            if($v['Type'] == 'CheckBox'){
                $str .= '<th  scope="col">
                    <div class="checkbox">
						<input id="SelectAllBtn" type="checkbox">
						<label for="SelectAllBtn">
							全选
						</label>
					</div>
                </th>';
            }else{
                $str .= '<th  scope="col">'.$v['Name'].'</th>';
            }
        } 
        if($this->IsEdit || $this->IsDel || !empty($v['BtnArr'])) $str .= '<th scope="col">操作</th>';
        $str .= '</tr></thead><tbody>';
        foreach($arr as $k => $v){
            $str .= '<tr class="'.$v['TrClass'].' '.(($this->TableSelectIndex == $k) ? 'table-success' : '').'">';
            foreach($keyArr as $sk => $sv){
                $Pre = isset($sv['Pre']) ? $sv['Pre'] : '';
                $Td = empty($sv['Td']) ? 'td' : $sv['Td'];
                switch ($sv['Type']){
                    case 'CheckBox':
                        $str .= '<td style="'.$sv['Style'].'">
                        <div class="checkbox ">
						<input id="checkbox_'.$v[$this->PrimaryKey].'" class="CheckBoxOne" type="checkbox" value="'.$v[$this->PrimaryKey].'">
						<label for="checkbox_'.$v[$this->PrimaryKey].'">
							'.$v[$sk].'
						</label>
					</div>
                        </td>';break;
                    case 'Date':
                        $str .= '<td style="'.$sv['Style'].'">'.date('Y-m-d', $v[$sk]).'</td>';break;
                    case 'Time':
                        $str .= '<td style="'.$sv['Style'].'">'.date('Y-m-d H:i:s', $v[$sk]).'</td>';break;
                    case 'True':
                        $IsTrue = ($v[$sk]) ? 'success' : 'danger';
                        $Text = ($v[$sk]) ? '是' : '否';
                        $str .= '<td style="'.$sv['Style'].'"><span class="text-'.$IsTrue.'">'.$Text.'</span></td>';break;
                    case 'Key':
                        $str .= '<td style="'.$sv['Style'].'">'.$Pre.$keyArr[$sk]['Data'][$v[$sk]].'</td>';break;
                        break;
                    case 'Switch':
                        $str .= '<td style="'.$sv['Style'].'"><span class="switch switch-sm">
                                <input type="checkbox" class="StateBtn switch" id="switch-'.$sk.'-'.$v[$this->PrimaryKey].'" data="'.$v[$this->PrimaryKey].'" dataState="'.(($v[$sk] == 1) ? 2 : 1).'" dataField="'.$sk.'" '.(($v[$sk] == 1) ? 'checked' : '').'>
                                <label for="switch-'.$sk.'-'.$v[$this->PrimaryKey].'"></label>
                              </span></td>';
                        break;
                    default:
                        $str .= '<'.$Td.' style="'.$sv['Style'].'">'.$Pre.$v[$sk].'</'.$Td.'>';break;
                }
            }
            if($this->IsEdit || $this->IsDel || !empty($v['BtnArr'])){
                $ActArr = array();
                $_GET[$this->PrimaryKey] = $v[$this->PrimaryKey];
                if(!empty($v['BtnArr'])){
                    foreach($v['BtnArr'] as $Btn){
                        $BtnColor = isset($Btn['Color']) ? $Btn['Color'] : 'primary';
                        $ParaStr = !empty($Btn['Para']) ? '?'.http_build_query($Btn['Para']) : '';
                        $Link = (empty($Btn['Link']) || $Btn['Link'] == '#') ? 'javascript:void(0);' : $Btn['Link'].$ParaStr;
                        $Disabled = (isset($Btn['IsDisabled']) && $Btn['IsDisabled'] == 1) ? 'disabled' : '';
                        $Confirm = empty($Btn['Confirm']) ? '' : 'onclick="return confirm(\''.$Btn['Confirm'].'\')"';
                        $ActArr[] = '<a class="btn btn-sm mr-2 btn-'.$BtnColor.' '.$Disabled.' table_btn_'.$Btn['Name'].'" href="'.$Link.'" '.(($Btn['IsBlank']) ? 'target="_blank"' : '').' '.$Confirm.'>'.$Btn['Desc'].'</a>';
                    }
                }
                if($this->IsEdit) $ActArr[] = (isset($v['IsEdit']) && $v['IsEdit'] != 1) ? '<a class="btn btn-sm btn-primary mr-2 disabled" href="javascript:void(0);">'.$this->NameEdit.'</a>' : '<a class="btn btn-sm btn-primary mr-2" href="'.$this->LinkEdit.'?'.http_build_query($_GET).'">'.$this->NameEdit.'</a>';
                if($this->IsDel) $ActArr[] = (isset($v['IsDel']) && $v['IsDel'] != 1) ? '<a class="btn btn-sm btn-danger mr-2 disabled" href="javascript:void(0);" >'.$this->NameDel.'</a>' : '<a class="btn btn-sm btn-danger mr-2" href="'.$this->LinkDel.'?'.http_build_query($_GET).'" onclick="return confirm(\'是否删除?\')">'.$this->NameDel.'</a>';
                
                
                $str .= '<td >'.implode(' ', $ActArr).'</td>';
                unset($_GET[$this->PrimaryKey]);
                $num++;
            }
            
            $str .= '</tr>';
        }
        
        $TableFooterBtnArr = array();
        foreach($this->TableFooterBtnArr as $Btn){
            $TableFooterBtnArr[] = '<button id="'.$Btn['Name'].'" type="button" class="mr-2 btn btn-sm btn-'.$Btn['Class'].'">'.$Btn['Desc'].'</button>';
        }
        //var_dump($this->TableFooterBtnArr, $TableFooterBtnArr);exit;
        $str .= '</tbody>';
        if(!empty($Page)) $str .= '<tfoot><tr><td colspan="'.$num.'" class="page "><div class="d-flex justify-content-between align-items-center"><div>'.implode('', $TableFooterBtnArr).'</div>'.$Page.'</div></td></tr></tfoot>';
        $str .= '</table>';
        return ($IsResponsive == 1) ? '<div class="table-responsive-sm">'.$str.'</div>' : $str;
    }
    
    private function _Clean(){
        $this->Html = '';
        $this->Js = '';
    }
}