<!doctype html>
<html lang="zh">

<head>
    <title>
       QCMS V<?=VERSION?> 安装

    </title>
    <link href="<?=URL_BOOT?>css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?=URL_BOOT?>css/style-init.css" rel="stylesheet" type="text/css">

</head>

<body class="d-flex align-items-center" style="height: 100vh;">
    <div class="container  p-3 install-w" style="width: 600px; " >
        <div class="rounded bg-white px-3 py-4">
            <form class="form-signin " method="post" id="InstallForm">
                <div class="text-center mb-3 mt-4">
                    <svg class="logo mx-3" style="fill: #1572E8!important;width: 98px;height: 24px;" >
                      <use xlink:href="/Static/images/logo.svg#layer"/>
                    </svg>
                </div>
                <div class=" mb-4 text-muted text-center install-br">
                    欢迎使用 QCMS V<?=VERSION?> ！现在进行安装
                </div>
                <div class="Setp" data-step="0">

                    <div id="CheckList" class="border overflow-auto p-3 mb-4 install-write scrollbar" style="height: 400px; ">

                    </div>
                    <button type="button" class="btn btn-block my-3 NextBtn" data-step="1" ></button>
                </div>
                <div class="Setp d-none" data-step="2">
                    <?
                    foreach($Step2 as $v){?>
                        <div class="form-group pt-0 pb-1 row">
                        <label for="input<?=$v['Name']?>" class="col-sm-2 col-form-label text-right"><?=$v['Desc']?> : </label>
                        <div class="col-sm-10">
                          <input type="text" name="<?=$v['Name']?>" class="form-control" id="input<?=$v['Name']?>" value="<?=$v['Value']?>" placeholder="请输入<?=$v['Desc']?>" required="required">
                        </div>
                      </div>
                    <?
                    }
                    ?>
                    <button type="button" class="btn btn-primary btn-block my-3 NextBtn" data-step="3">现在安装 ！</button>
                </div>
                <div class="Setp d-none" data-step="1">
                <?
                foreach($Step1 as $v){?>
                    <div class="form-group pt-0 pb-1 row">
                    <label for="input<?=$v['Name']?>" class="col-sm-2 col-form-label text-right"><?=$v['Desc']?> : </label>
                    <div class="col-sm-10">
                      <input type="text" name="<?=$v['Name']?>" class="form-control" id="input<?=$v['Name']?>" value="<?=$v['Value']?>" placeholder="请输入<?=$v['Desc']?>" required="required">
                    </div>
                  </div>
                <?
                }
                ?>
                <button type="button" class="btn btn-primary btn-block my-3 NextBtn" data-step="2">下一步</button>
                </div>
            </form>
        </div>
        <p class="my-4 text-dark" style="text-align: center;"> Copyrignt &copy;
            <?=WEB_TITLE?>
        </p>
    </div>
</body>
<?=$this->loadView('index/js');?>
<script type="text/javascript">
    var CheckRet = true;
    $(function(){
        $('#CheckList').html('');
        $.ajax({
            url: "/install/checkPermission",
            async: false,
            dataType:'json',
            success: function(Res){
                if(Res.Data.IsOk != 1) CheckRet = false;
                $('#CheckList').append('<div class="text-dark font-weight-bold install-title">权限检测</div>');
                for(let Index in Res.Data.FileArr){
                    let Rs = Res.Data.FileArr[Index];
                    if(Rs.IsWriteAble == 1){
                        $('#CheckList').append('<div class="text-success d-flex justify-content-between"><div>'+Rs.Path+'</div><div>可写</div></div>');
                    }else{
                        $('#CheckList').append('<div class="text-danger d-flex justify-content-between"><div>'+Rs.Path+'</div><div>不可写</div></div>');
                    }
                }
            }
        })

        $.ajax({
            url: "/install/checkExtend",
            async: false,
            dataType:'json',
            success: function(Res){
                if(Res.Data.IsOk != 1) CheckRet = false;
                $('#CheckList').append('<div class="text-dark font-weight-bold mt-3 install-title">扩展检测</div>');
                for(let Index in Res.Data.ExtArr){
                    let Rs = Res.Data.ExtArr[Index];
                    if(Rs.IsInstall == 1){
                        $('#CheckList').append('<div class="text-success d-flex justify-content-between"><div>'+Rs.Ext+'</div><div>支持</div></div>');
                    }else{
                        $('#CheckList').append('<div class="text-danger d-flex justify-content-between"><div>'+Rs.Ext+'</div><div>不支持!</div></div>');
                    }
                }
            }
        })

        if(CheckRet == true){
            $('.NextBtn[data-step="1"]').html(': ) 检测成功,下一步');
            $('.NextBtn[data-step="1"]').removeClass('btn-danger').addClass('btn-primary').attr('disabled', false);
        }else{
            $('.NextBtn[data-step="1"]').html(': ( 检测失败,请检查环境是否缺失。');
            $('.NextBtn[data-step="1"]').removeClass('btn-primary').addClass('btn-danger').attr('disabled', true);
        }

        $('.NextBtn').click(function(){
            let Step = $(this).attr('data-step');

            if(Step == 2){
                console.log('Step', Step)
                if($('#inputHost').val() == ''){
                    $('#inputHost').focus();
                    return;
                }
                if($('#inputName').val() == ''){
                    $('#inputName').focus();
                    return;
                }
                if($('#inputAccounts').val() == ''){
                    $('#inputAccounts').focus();
                    return;
                }
                if($('#inputPassword').val() == ''){
                    $('#inputPassword').focus();
                    return;
                }
                if($('#inputPort').val() == ''){
                    $('#inputPort').focus();
                    return;
                }
                let checkDbRet = {};
                $.ajax({
                    type: "POST",
                    url: "/install/checkDb",
                    async: false,
                    data:{Host:$('#inputHost').val(),Name:$('#inputName').val(),Accounts:$('#inputAccounts').val(),Password:$('#inputPassword').val(),Port:$('#inputPort').val(),},
                    dataType:'json',
                    success: function(Res){
                        checkDbRet = Res;
                    }
                })
                if(checkDbRet.Code != 0){
                    alert(checkDbRet.Msg);
                    return;
                }
            }
            if(Step == 3){
                if($('#inputPhone').val() == ''){
                    $('#inputPhone').focus();
                    return;
                }
                if(!checkPhone($('#inputPhone').val())){
                    alert('手机号码错误');
                    $('#inputPhone').focus();
                    return;
                }
                if($('#inputRegPassword').val() == ''){
                    $('#inputRegPassword').focus();
                    return;
                }
                if($('#inputRegPassword2').val() == ''){
                    $('#inputRegPassword2').focus();
                    return;
                }
                if($('#inputRegPassword').val() != $('#inputRegPassword2').val()){
                    alert('密码和确认密码不一致');
                    $('#inputRegPassword').focus();
                    return;
                }
                $('#InstallForm').submit();
                return;
            }

            $('.Setp').addClass('d-none');
            $('.Setp[data-step="'+Step+'"]').removeClass('d-none');
        })
    })

function checkPhone(phone){
    if(!(/^1\d{10}$/.test(phone))){
        return false;
    }
    return true;
}
</script>

</html>