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
                                <div class="text-dark">附件占用空间：<?=$this->CommonObj->Size($SizeTotal)?></div>
                            </div>
                            <div class="panel-wrapper ">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <?
                                            foreach($Arr as $k => $v){
                                            ?>
                                            <div class="file-box">
                                                <div class="file">
                                                    <div class="checkbox checkbox-primary position-absolute pt-0" style="right:0px;top:0px;">
                                                        <input class="ImgView" id="checkbox_<?=$k?>" value="<?=$v['FileId']?>" type="checkbox" >
                                                        <label for="checkbox_<?=$k?>"></label>
                                                    </div>
                                                    <a target="_blank" href="<?=$v['Img']?>">
                                                        <?=$this->fileView($v['Img'], $v['Ext'])?>
                                                        <div class="file-name">
                                                            <div class="text-nowrap overflow-hidden"><?=empty($v['Name']) ? '未命名' : $v['Name']?></div>
                                                            <span><span class="float-right"><?=$this->CommonObj->Size($v['Size'])?></span>时间: <?=date('Y-m-d', $v['Ts'])?></span>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <? } ?>

                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                    <button type="button" class="btn btn-primary btn-sm mr-2" id="checkAllBtn">全部选中</button>
                                    <button type="button" class="btn btn-primary btn-sm mr-2" id="emptyAllBtn">全部不选</button>
                                    <button type="button" class="btn btn-danger btn-sm mr-2" id="DelBatchBtn">选中删除附件</button>
                                    <button type="button" class="btn btn-success btn-sm mr-2" id="cleanFileBtn">清空无效附件</button>
                                </div>
                                    <?=$PageBar?>
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
<script type="text/javascript">
    $(function(){
        $('#checkAllBtn').click(function(){
            $('.ImgView').prop('checked', true);
        })
        $('#emptyAllBtn').click(function(){
            $('.ImgView').prop('checked', false);
        })
        $('#DelBatchBtn').click(function(){
            if(!confirm('将物理删除文件，确定删除?')) return false;
            let Ids = getAllChecked();
            //console.log('ids', Ids); return;
            $.post('<?=$this->CommonObj->Url(array('admin', 'api', 'fileDel')).'?'.http_build_query($_GET)?>', {'Ids':Ids.join('|')}, function(Res){
                if(Res.Code){
                    alert(Res.Msg);return;
                }
                location.reload();
            }, 'json')
        })
        $('#cleanFileBtn').click(function(){
            if(!confirm('确定清空无效附件?')) return false;
            $.post('<?=$this->CommonObj->Url(array('admin', 'api', 'fileClean')).'?'.http_build_query($_GET)?>', {}, function(Res){
                if(Res.Code){
                    alert(Res.Msg);return;
                }
                location.reload();
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
</script>
</html>