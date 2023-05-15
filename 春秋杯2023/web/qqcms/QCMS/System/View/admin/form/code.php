
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
<a href="<?=$this->CommonObj->url(array('admin', 'form', 'index'))?>" class="btn btn-default btn-sm">返回</a>

<?
$Html = '<form method="post" action="/index/form/'.$Rs['KeyName'].'.html">';

foreach($Rs['FieldJson'] as $v){
    $DataTmp = empty($v['Data']) ? array() : explode('|', $v['Data']);
    $Data = array();
    foreach($DataTmp as $dv) $Data[$dv] = $dv;
    $FormRs = array('Name' => $v['Name'], 'Desc' => $v['Comment'],  'Type' => $v['Type'], 'Data' => $Data, 'Value' => $v['Content'], 'Required' => $v['NotNull'], 'Col' => 12, 'Row' => 6);
    $Html .= $this->BuildObj->FormOne($FormRs).PHP_EOL;

}
$Html .= $this->BuildObj->FormOne(array('Desc' => '提交', 'Type' => 'button', 'ButtonType' => 'submit', 'Col' => 12));


$Html .= '</form>'
?>

                            </div>
                            <div class="panel-wrapper ">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="mb-2">获取代码</h5>
                                            <div class="border p-3"><textarea class="form-control" rows="20"><?= htmlentities($Html)?></textarea></div>
                                        </div>

                                        <div class="col-6">
                                            <h5 class="mb-2">表单演示</h5>
                                            <div class="border p-3"><?=$Html?></div>
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
</body>
</html>
