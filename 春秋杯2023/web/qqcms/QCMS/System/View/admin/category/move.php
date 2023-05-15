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
                            </div>
                            <div class="panel-wrapper ">
                                <div class="panel-body">
                                    <form method="post" class="BuildForm  ">
                                        <div class="form-group col-12  col-lg-12">
                                            <label for="Input_Phone" class=" mb-1">当前分类</label>
                                            <input type="text" class="form-control " name="Name" id="Input_Name" placeholder="请输入当前分类" value="<?=$CateRs['Name']?>" disabled="disabled" >
                                        </div>

                                        <div class="form-group col-12  col-lg-12">
                                            <label for="Input_PCateId" class="mb-1 ">目标分类<span class="text-danger ml-2" style="font-weight: 900;">*</span></label>
                                            <select class="form-control " name="PCateId" id="Input_PCateId" required="required">
                                                <option value="0">顶级分类</option>
                                                <?=$this->CategoryObj->CateTreeSelectHtml?>
                                            </select></div>
                                        <div class="form-group col-12  col-lg-12"><button type="submit" class="btn btn-primary " id="Button_submit">提交</button></div>
                                    </form>
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

</html>