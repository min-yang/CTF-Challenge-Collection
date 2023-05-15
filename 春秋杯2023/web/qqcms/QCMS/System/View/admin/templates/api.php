
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?=$this->SysRs['WebName']?> - 网站后台</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <?=$this->LoadView('admin/common/meta')?>
    <link href="<?=URL_BOOT?>css/jsoneditor.css" rel="stylesheet" type="text/css">
</head>

<body>
    <!--/Preloader-->
    <div class="wrapper">
            <!-- Top Menu Items -->
            <?=$this->LoadView('admin/common/nav')?>
            <!-- /Top Menu Items -->

            <!-- Left Sidebar Menu -->
            <?=$this->LoadView('admin/common/sidebar')?>
            <!-- /Left Sidebar Menu -->

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="container-fluid">

                <!-- Title -->
                <div class="row heading-bg  bg-primary">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h5 class="txt-light"><?=$this->PageTitle?></h5>
                    </div>
                    <!-- Breadcrumb -->
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <ol class="breadcrumb">
                            <li><a href="index.html">用户中心</a></li>
                            <?
                            foreach($this->BreadCrumb as $v){
                                echo '<li class="'.($v['IsActive'] ? 'active' : '').'"><a href="'.$v['Url'].'"><span>'.$v['Name'].'</span></a></li>';
                            }
                            ?>
                        </ol>
                    </div>
                    <!-- /Breadcrumb -->
                </div>
                <!-- /Title -->
                <div class="row" style="min-height: 600px;">
<div class="col-sm-12">
                        <div class="panel panel-default card-view" >
                            <div class="panel-heading mb-3 pb-2 d-flex justify-content-between align-items-center border-bottom">

<h5 class="txt-dark"><?=$this->PageTitle2?></h5>



                            </div>
                            <div class="panel-wrapper ">
                                <div class="panel-body">
                                    <div class="mb-3">
                                    <?
                                    foreach($ApiArr as $k => $v){
                                        $BtnColor = ($k == 'sys') ? 'btn-primary' : 'btn-default';
                                        echo '<button class="btn '.$BtnColor.' mr-2 DisplayBtn" data="'.$k.'">'.$v['Name'].'</button>';
                                    }
                                    ?>
                                    </div>
                                    <div>
                                        <div class="row">
                                            <div class="col-sm-6 mb-2">
                                                <label class="control-label mb-10">接口地址</label>
                                                <input type="text" id="Address" class="form-control" value="">
                                            </div>
                                            <div class="col-sm-3 mb-2">
                                                <label class="control-label mb-10">接口秘钥</label>
                                                <input type="text" id="Secret" class="form-control" value="<?=$this->SysRs['Secret']?>">
                                            </div>
                                            <div class="col-sm-3 mb-2">
                                                <label class="control-label mb-10">时间戳</label>
                                                <input type="text" id="Ts" class="form-control" value="<?=time()?>">
                                            </div>
                                        </div>
                                        <div class="row" id="ParaArr"></div>
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-primary" id="SubmitBtn">请求接口</button>
                                                <input type="text" id="PostPara" class="form-control " disabled="disabled" value="" placeholder="请求参数">
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <button class="btn btn-default btn-sm disabled">返回结果</button>
                                            <div>
                                            <div class="" id="ResultHtml" style="height: 600px;"></div>
                                        </div>
                                        </div>

                                        <button class="btn btn-primary btn-sm">签名生成规则</button>
                                        <div class="mb-3">

                                            <div class="text-dark row" >

                                                <div class="col-6 " >
                                                    <button class="btn btn-default btn-sm disabled float-right">请求数据</button>
                                                    <div class="p-2 border" style="background-color: #f5f5f5;">
                                                    {<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ts: 1649157257,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Model: article,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CateId: 0,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page: 1,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Row: 10,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State: 1<br>
}<br><br>
                                                </div></div>
                                                <div class="col-6 " >
                                                    <button class="btn btn-default btn-sm disabled float-right">加签名后请求数据</button>
                                                    <div class="p-2 border" style="background-color: #f5f5f5;">
{<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ts: 1649157257,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Model: article,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CateId: 0,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page: 1,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Row: 10,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State: 1,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sign: 3cc9b893c354d9ddca7a8bea70f421c5<br>
}
                                                </div></div>
                                            </div>
                                            <div class="p-3 border border-top-0 text-dark  overflow-auto">



                                                <div class="font-weight-bold">第一步：将参与签名的参数按照键值(key)进行字典排序</div>

例如：将上述请求参数中的Ts、Model、CateId、Page、Row、State 进行字典排序。结果为：CateId、Model、Page、Row、State、Ts<br><br>

<div class="font-weight-bold">第二步：将排序过后的参数，进行key和value字符串拼接</div>

将参数中的key和value按照key的顺序进行字符串拼接。结果为：CateId=0&Model=article&Page=1&Row=10&State=1&Ts=1649157648<br><br>

<div class="font-weight-bold">第三步：将拼接后的字符串尾部加上Secret秘钥，合成签名字符串</div>

将第二步的字符窜首尾拼接上Secret秘钥。结果为：CateId=0&Model=article&Page=1&Row=10&State=1&Ts=1649158037&Secret=<?=$this->SysRs['Secret']?><br><br>

<div class="font-weight-bold">第四步：对签名字符串进行MD5加密，生成32位的字符串</div>

对生成签名字符串进行MD5加密。结果为：3cc9b893c354d9ddca7a8bea70f421c5<br><br>

<div class="font-weight-bold">注：生成最终的签名的字符串：3cc9b893c354d9ddca7a8bea70f421c5作为请求参数的Sign的值传入即可</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
            <!-- Footer -->
            <?=$this->LoadView('admin/common/footer')?>
            <!-- /Footer -->

        </div>
        <!-- /Main Content -->

    </div>
    <!-- jQuery -->
    <?=$this->LoadView('admin/common/js')?>
    <script src="<?=URL_BOOT?>js/md5.js"></script>
    <script src="<?=URL_BOOT?>js/jsoneditor.js"></script>
    <script type="text/javascript">
        var Host = '<?=$_SERVER['REQUEST_SCHEME'].'://'.URL_DOMAIN.'/'?>';
        var ApiArr = <?=json_encode($ApiArr)?>;
        var SelectApi = 'sys';
        var container = document.getElementById('ResultHtml');
        var options = {
            mode: 'code',
            onError: function (err) {
                alert(err.toString());
            },
            onModeChange: function (newMode, oldMode) {
                console.log('Mode switched from', oldMode, 'to', newMode);
            },
        };
        var editor = new JSONEditor(container, options,null);
        $(function(){
            Fill();
            $('.DisplayBtn').click(function(){
                SelectApi = $(this).attr('data');
                Fill();
            })
            $('#SubmitBtn').click(function(){
                let Para = {Ts:$('#Ts').val()};
                $.each($('.ParaInput'), function(){
                    let Key = $(this).attr('data-name');
                    let Val = $(this).val()
                    if(Val != '') Para[Key] = Val;
                })

                ApiPost($('#Address').val(), Para, $('#Secret').val()).then(Res => {
                    //$('#ResultHtml').html(Res)
                    editor.set(Res);
                })
            })

        })

        var Fill = function(){
            $('.DisplayBtn').removeClass('btn-primary').addClass('btn-default');
            $('.DisplayBtn[data='+SelectApi+']').removeClass('btn-default').addClass('btn-primary');

            $('#ParaArr').html('');
            $('#Address').val(Host+ApiArr[SelectApi].Path)
            $.each(ApiArr[SelectApi].Para, function(key, val){
                console.log(val)
                let IsMustStr = (val.IsMust == 1) ? '<span class="text-danger ml-2">[必填]</span>' : '';
                $('#ParaArr').append(`
                    <div class="col-6 mb-3">
                        <label class="control-label mb-1">`+val.Desc+IsMustStr+`</label>
                        <input type="text" class="form-control ParaInput" data-name="`+val.Key+`" value="`+val.Default+`">
                    </div>
                `)
            })
        }

        var ApiPost = function(Url, Param, Secret){

            return new Promise((resolve, reject) => {

                let SortArr = Object.keys(Param).sort();
                let TempArr = [];
                for (let key in SortArr) {
                    let val = SortArr[key]
                    TempArr.push(val + '=' + Param[val])
                }
                let Str = TempArr.join('&');
                Param['Sign'] = hex_md5(Str + '&Secret=' + Secret);
                $('#PostPara').val(JSON.stringify(Param));
                $.ajax({
                    type: "POST",
                    url: Url,
                    dataType:'json',
                    data:JSON.stringify(Param),
                    success:function(Res){
                        resolve(Res)
                    },
                    error:function(XHR, TS){
                        editor.set(XHR.responseText)
                    }

                })
                /*$.post(Url, Param, function(Res){

                    resolve(Res)
                }, 'json')*/

                /*wx.request({
                    url: Config.API_URL + '/' + Url,
                    method: 'POST',
                    data: Param,
                    success(res) {
                        resolve(res.data)
                    },
                    fail(err) {
                        reject(err)
                    }
                })*/
            })
        }
    </script>
</body>
</html>
