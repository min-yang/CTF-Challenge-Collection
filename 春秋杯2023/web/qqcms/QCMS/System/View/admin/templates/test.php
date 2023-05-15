
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

                                    <form method="'Get" class="BuildForm form-inline mb-2">
                                        <div class="form-group mr-3">
                                            <label for="Input_Type" class="mr-2 ">页面类型</label>
                                            <select class="form-control " name="Type" id="Input_Type">
                                                <option value="">请选择标签名字</option>
                                                <option value="index" selected="">首页</option>
                                                <option value="cate">分类页</option>
                                                <option value="detail">详情页</option>
                                                <option value="page">单页</option>
                                            </select>
                                        </div>
                                        <div class="form-group mr-3">
                                            <label for="Input_Index" class="mr-2 ">索引ID</label>
                                            <input class="form-control " type="text" id="Input_Index" value="" placeholder="分类ID/文章ID/单页ID">

                                        </div>
                                        <button class="btn btn-primary " type="button" id="actBtn">执行</button>

                                    </form>
                                    <div class="row">
                                        <div class="col-6">
                                            <button class="btn btn-default btn-sm disbaled">代码区</button>
                                            <textarea class="form-control" style="height:480px " id="Input_Html"></textarea>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-primary btn-sm ChangeBtn" data="Compile">结果区</button>
                                            <button type="button" class="btn btn-default btn-sm ChangeBtn" data="CompileHtml">Html</button>
                                            <textarea class="form-control"  style="height:480px " id="Compile"></textarea>
                                            <div id="CompileHtml" class="border d-none" style="height:480px; color: #2f2c2c; font-size: 1rem;padding: 0.375rem 0.75rem; "></div>
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
    <script type="text/javascript">
        var ResultView = 'Compile';
        $(function(){
            ResultViewFunc();
            $('#actBtn').click(function(){
                $.post('/admin/templates/test', {'Html':$('#Input_Html').val(), 'Type':$('#Input_Type').val(), 'Index':$('#Input_Index').val()}, function(Res){
                    $('#Compile').val(Res);
                    $('#CompileHtml').html(Res);
                }, 'html')
            })
            $('.ChangeBtn').click(function(){
                ResultView = $(this).attr('data');
                console.log(ResultView)
                ResultViewFunc();
            })

        })

        var ResultViewFunc = function(){
            $('.ChangeBtn').removeClass('btn-primary').addClass('btn-default');
            $('.ChangeBtn[data='+ResultView+']').removeClass('btn-default').addClass('btn-primary');
            if(ResultView == 'Compile'){
                $('#Compile').removeClass('d-none');
                $('#CompileHtml').addClass('d-none');
            }else{
                $('#Compile').addClass('d-none');
                $('#CompileHtml').removeClass('d-none');
            }
        }
    </script>
</body>
</html>
