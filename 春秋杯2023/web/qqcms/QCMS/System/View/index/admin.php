<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?=$this->SysRs['WebName']?> - 网站后台</title>
    <!-- vector map CSS -->
    <link href="<?=URL_BOOT?>css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?=URL_BOOT?>css/style-init.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        body{background-color:#1572e8!important;touch-action:none}
        .login{max-width:400px}
        .login .bi{font-size:1.4rem}
        .loginContainer{max-width:400px}
        .login .logo{width:110px}
    </style>
</head>

<body class="d-flex align-items-center" style="min-height: 100vh;">
    <div class="container loginContainer p-3">
        <div class="rounded bg-white px-4 py-5" style="width: 400px;">
            <form class="form-signin text-center" method="post">
                <svg class="logo mb-2" style="fill: #1572E8!important;width: 160px;height: 53px;" >
                  <use xlink:href="/Static/images/logo.svg#layer"/>
                </svg>

                <div class=" mb-3 text-muted">登录管理后台</div>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="Phone" id="inputPhone" placeholder="输入手机号码" required>
                    <div class="input-group-append">
                        <span class="input-group-text bg-white rounded-0 form-control" id="basic-addon2"><i class="bi bi-phone"></i></span>
                    </div>
                </div>
                <div class="input-group mb-2">
                    <input type="password" name="Password" class="form-control" placeholder="输入你的密码" required>
                    <div class="input-group-append">
                        <span class="input-group-text bg-white rounded-0 form-control" id="basic-addon2"><i class="bi bi-lock"></i></span>
                    </div>
                </div>
                <div class="input-group mb-4">
                    <input type="text" name="VCode" class="form-control" placeholder="输入验证码" required>
                    <div class="input-group-append">
                        <span class="input-group-text bg-white rounded-0 form-control p-0" id="basic-addon2"><img class="btn btn-outline-secondary d-none" id="veroCode" src="#" name="/index/code.html" alt="验证码加载失败" title="看不清则点击图片" onclick="this.src = this.name+'?'+'img='+Math.random();" style="padding: 0px;"></span>
                    </div>
                </div>
                <div class="input-group ">
                    <button class="btn btn-primary btn-block" type="submit">立即登录</button>
                </div>

            </form>
        </div>
        <p class="my-4 text-white" style="text-align: center;"> Copyrignt &copy; 2022 <?=WEB_TITLE?>
        </p>
    </div>
</body>
<? $this->LoadView('index/js'); ?>
<script type="text/javascript">
$(function() {
    $('#veroCode').attr('src', '/index/code.html?img=' + Math.random());
    $('#veroCode').removeClass('d-none')
})
</script>

</html>