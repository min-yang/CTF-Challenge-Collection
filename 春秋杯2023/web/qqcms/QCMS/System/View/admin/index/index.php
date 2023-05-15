
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
                            //var_dump($this->BreadCrumb);exit;
                            foreach($this->BreadCrumb as $v){
                                echo '<li class="'.($v['IsActive'] ? 'active' : '').'"><a href="'.$v['Url'].'"><span>'.$v['Name'].'</span></a></li>';
                            }
                            ?>
                        </ol>
                    </div>
                    <!-- /Breadcrumb -->
                </div>
                <?
                if(empty($LicenseRs)){
                ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <a href="https://www.q-cms.cn/" class="text-white" target="_blank">系统未经授权，请到官方购买授权。 https://www.q-cms.cn/</a>
                </div>
                <? }else{?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <a href="https://www.q-cms.cn/" class="text-white" target="_blank">域名已经获得正版授权，授权域名：<?=$LicenseRs['Domain']?>, 到期日期：<?=$LicenseRs['Date']?></a>
                </div>
                <? } ?>
                <!-- /Title -->
                <div class="row">
                    <div class="col-12 col-md-3">
                        <a href="<?=$this->CommonObj->Url(array('admin', 'content', 'index'))?>?ModelId=1">
                        <div class="panel panel-default card-view pa-0 mb-4">
                            <div class="panel-wrapper ">
                                <div class="panel-body pa-0">
                                    <div class="sm-data-box bg-green">
                                        <div class="row ma-0">
                                            <div class="col-5 text-center pa-0 icon-wrap-left">
                                                <i class="bi bi-file-text text-white"></i>
                                            </div>
                                            <div class="col-7 text-center data-wrap-right">
                                                <h6 class="txt-light">内容数量</h6>
                                                <span class="txt-light counter counter-anim"><?=$Stat['TableCount']?>篇</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div></a>
                    </div>

                    <div class="col-12 col-md-3">
                        <a href="<?=$this->CommonObj->Url(array('admin', 'category', 'index'))?>">
                        <div class="panel panel-default card-view pa-0 mb-4">
                            <div class="panel-wrapper ">
                                <div class="panel-body pa-0">
                                    <div class="sm-data-box bg-red">
                                        <div class="row ma-0">
                                            <div class="col-5 text-center pa-0 icon-wrap-left">
                                                <i class="bi bi-list-ol txt-light"></i>
                                            </div>
                                            <div class="col-7 text-center data-wrap-right">
                                                <h6 class="txt-light">分类数量</h6>
                                                <span class="txt-light counter counter-anim"><?=$Stat['CateCount']?>个</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                    <div class="col-12 col-md-3">
                        <a href="<?=$this->CommonObj->Url(array('admin', 'user', 'index'))?>">
                        <div class="panel panel-default card-view pa-0 mb-4">
                            <div class="panel-wrapper ">
                                <div class="panel-body pa-0">
                                    <div class="sm-data-box bg-primary">
                                        <div class="row ma-0">
                                            <div class="col-5 text-center pa-0 icon-wrap-left">
                                                <i class="bi bi-people txt-light"></i>
                                            </div>
                                            <div class="col-7 text-center data-wrap-right">
                                                <h6 class="txt-light">用户数量</h6>
                                                <span class="txt-light counter counter-anim"><?=$Stat['UserCount']?>个</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                    <div class="col-12 col-md-3">
                        <a href="<?=$this->CommonObj->Url(array('admin', 'file', 'index'))?>">
                        <div class="panel panel-default card-view pa-0 mb-4">
                            <div class="panel-wrapper ">
                                <div class="panel-body pa-0">
                                    <div class="sm-data-box bg-pink">
                                        <div class="row ma-0">
                                            <div class="col-5 text-center pa-0 icon-wrap-left">
                                                <i class="bi bi-file-image text-white"></i>
                                            </div>
                                            <div class="col-7 text-center data-wrap-right">
                                                <h6 class="txt-light">附件占用空间</h6>
                                                <span class="txt-light counter counter-anim"><?=$this->CommonObj->Size($Stat['FileSum'])?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
                <div id="qcmsAdDiv"></div>
                <div class="row">

                    <div class="col-12">
                        <div class="panel panel-default card-view">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark"><i class="icon-share mr-10"></i>网站流量统计</h6>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper ">
                                <div id="BarChart"></div>
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
    <script src="/Static/bootstrap/Highcharts-7.1.1/highcharts.js"></script>
    <script src="/Static/bootstrap/Highcharts-7.1.1/modules/series-label.js"></script>
    <script src="/Static/bootstrap/Highcharts-7.1.1/modules/exporting.js"></script>
    <script src="/Static/bootstrap/Highcharts-7.1.1/modules/export-data.js"></script>
<script type="text/javascript">
    ChartsFull('<?=date('Y-m')?> 流量报表', 'BarChart', <?=json_encode(array_keys($DataArr))?>,
    [
      {
        'name' : '总流量： <?=$Total?> 个',
        'color':"#3cb878",
        'data' : <?=json_encode(array_values($DataArr))?>,
        'dataLabels': {enabled: true},
      },

    ]
  );
</script>


</body>
</html>
