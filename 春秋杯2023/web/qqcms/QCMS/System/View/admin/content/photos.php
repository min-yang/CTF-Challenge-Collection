<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?=$this->SysRs['WebName']?> - 网站后台</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <?=$this->LoadView('admin/common/meta')?>
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
                        <h5 class="txt-light">
                            <?=$this->PageTitle?>
                        </h5>
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
                        <div class="panel panel-default card-view">
                            <div class="panel-heading mb-3 pb-2 d-flex justify-content-between align-items-center border-bottom">

                                <h5 class="txt-dark">
                                    <?=$this->PageTitle2?>
                                </h5>

                                 <div class="fileupload btn btn-primary btn-anim  ">
                                    <i class="fa fa-upload"></i><span class="btn-text">上传照片</span>
                                    <input type="file" class="upload" multiple="multiple" id="file_input">
                                </div>
                            </div>
                            <div class="panel-wrapper ">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12" id="PhotoList">

                                        </div>
                                    </div>

                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm mr-2" id="checkAllBtn">全选</button>
                                    <button type="button" class="btn btn-primary btn-sm mr-2" id="emptyAllBtn">清空</button>
                                    <button type="button" class="btn btn-danger btn-sm mr-2" id="DelBatchBtn">删除</button>
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
    <?=$this->LoadView('admin/common/js')?>
</body>
<script type="text/javascript">
    var PhotoArr = <?=json_encode($Photos)?>;
    new Sortable(PhotoList, {
        animation: 150,
        sort: true, // 设为false，禁止sort
        onEnd: function( /**Event*/ evt) {
            //console.log(evt);return;
            $.post('<?=$this->CommonObj->Url(array('admin', 'content', 'photoSort')).'?'.http_build_query($_GET)?>', {'newIndex': evt.newIndex, 'oldIndex':evt.oldIndex }, function(Res) {
                if(Res.Code){
                    alert(Res.Msg);return;
                }
                PhotoArr = Res.Data;
                    fillPhoto();
            }, 'json')
        }
    });
    window.onload = function(){
        var input = document.getElementById("file_input");
        var result,div;

        if(typeof FileReader==='undefined'){
            result.innerHTML = "抱歉，你的浏览器不支持 FileReader";
            input.setAttribute('disabled','disabled');
        }else{
            input.addEventListener('change', readFile, false);
        }
        function readFile(){
            var fd = new FormData();
            for(var i=0;i<this.files.length;i++){
                if (!this.files[i].name.match(/.jpg|.gif|.png|.bmp/i)){　　//判断上传文件格式
                    return alert("上传的图片格式不正确，请重新选择");
                }
                fd.append(i,this.files[i]);
            }
            $.ajax({
                url : '<?=$this->CommonObj->Url(array('admin', 'content', 'photos')).'?'.http_build_query($_GET)?>',
                dataType: 'json',
                type : 'POST',
                data : fd,
                cache: false,
                processData: false,
                contentType: false,
                success:function(Res){
                    if(Res.Code != 0){
                        alert(Res.msg);
                        return;
                    }
                    PhotoArr = Res.Data;
                    fillPhoto();
                    //location.reload();
                }
            })
        }
    }
    $(function(){
        fillPhoto();
        $('#checkAllBtn').click(function(){
            $('.ImgView').prop('checked', true);
        })
        $('#emptyAllBtn').click(function(){
            $('.ImgView').prop('checked', false);
        })
        $('#DelBatchBtn').click(function(){
            if(!confirm('确定删除?')) return false;
            let Ids = getAllChecked();
            $.post('<?=$this->CommonObj->Url(array('admin', 'content', 'photoDel')).'?'.http_build_query($_GET)?>', {'PhotoIndex':Ids.join('|')}, function(Res){
                if(Res.Code){
                    alert(Res.Msg);return;
                }
                PhotoArr = Res.Data;
                fillPhoto();
            }, 'json')
        })
        $('#PhotoList').on('click', '.delBtn', function(){
            let Index = $(this).attr('data-index');
            $.post('<?=$this->CommonObj->Url(array('admin', 'content', 'photoDel')).'?'.http_build_query($_GET)?>', {'PhotoIndex':Index}, function(Res){
                if(Res.Code){
                    alert(Res.Msg);return;
                }
                PhotoArr = Res.Data;
                fillPhoto();
            }, 'json')
        })
    })

    var getAllChecked = function(){
        let Ids = [];
        $('.ImgView:checked').each(function(index, item){
            Ids.push($(this).val())
        })
        return Ids;
    }

    var fillPhoto = function(){
        $('#PhotoList').html('');
        let Html = '';
        for(let Index in PhotoArr){
            let Rs = PhotoArr[Index];
            Html += `
            <div class="file-box">
                <div class="file">
                <div class="checkbox checkbox-primary position-absolute pt-0" style="right:0px;top:0px;">
                    <input class="ImgView" id="checkbox_`+Index+`" value="`+Index+`" type="checkbox" >
                    <label for="checkbox_`+Index+`"></label>
                </div>
                    <a target="_blank" href="`+Rs.Path+`">

                        <div class="image" style="height: 120px;">
                            <img alt="image" class="img-fluid" src="`+Rs.Path+`">
                        </div>
                        </a>
                        <div class="file-name">
                            <div class="text-nowrap overflow-hidden">`+Rs.Name+`</div>
                            <span class="d-flex justify-content-between">
                                <span>`+Rs.SizeView+`</span>
                                <span><a class="text-primary delBtn" data-index="`+Index+`" href="javascript:void(0);">删除</a></span>
                            </span>
                        </div>

                </div>
            </div>
            `;
        }
        $('#PhotoList').html(Html);
    }
</script>
</html>