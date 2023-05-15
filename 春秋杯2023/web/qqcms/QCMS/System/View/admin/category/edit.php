
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
                                    <?=$this->HeadHtml?>
                                    <?=$this->BuildObj->Html?>
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
</body>
<script type="text/javascript">
var ModelTempKV = <?=json_encode($ModelTempKV)?>;
var ModelId = $('#Input_ModelId').val();
$(function(){
    initModel();
    changeLinkUrl();
    $('#Input_ModelId').change(changeModel)
    $('#Attr_IsLink').change(function(){
        changeLinkUrl();
    })
})

var changeModel = function(){
    ModelId = $(this).val();
    if(ModelId != ''){
        $('#Input_TempList').val('list_'+ModelTempKV[ModelId]+'.html');
        $('#Input_TempDetail').val('detail_'+ModelTempKV[ModelId]+'.html');
    }
}
var changeLinkUrl = function(){
    let dom = $('#Input_LinkUrl').parent();
    if($('#Attr_IsLink').prop('checked')){
        dom.removeClass('d-none')
        $('#Input_SeoTitle').parent().addClass('d-none');
        $('#Input_Keywords').parent().addClass('d-none');
        $('#Input_Description').parent().addClass('d-none');
        $('#Input_UserLevel').parent().addClass('d-none');
    }else{
        dom.addClass('d-none')
        $('#Input_SeoTitle').parent().removeClass('d-none');
        $('#Input_Keywords').parent().removeClass('d-none');
        $('#Input_Description').parent().removeClass('d-none');
        $('#Input_UserLevel').parent().removeClass('d-none');
    }
}
var initModel = function(){
    if(ModelId != ''){
        if($('#Input_TempList').val() == ''){
            $('#Input_TempList').val('list_'+ModelTempKV[ModelId]+'.html');
        }
        if($('#Input_TempDetail').val() == ''){
            $('#Input_TempDetail').val('detail_'+ModelTempKV[ModelId]+'.html');
        }
    }
}
</script>
</html>
