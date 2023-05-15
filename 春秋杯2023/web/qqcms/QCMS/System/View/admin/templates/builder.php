<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>
        <?=$this->SysRs['WebName']?> - 网站后台
    </title>
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

                                    <div>
                                    <?
                                    foreach($LabelArr as $k => $v){
                                        $BtnColor = ($k == 'include') ? 'btn-primary' : 'btn-default';
                                        echo '<button class="btn '.$BtnColor.' mr-2 mb-2 DisplayBtn" data="'.$k.'">'.$v.'</button>';
                                    }
                                    ?>
                                    </div>
                                    <div class="my-3 DemoDiv d-none" data="include">
                                        <h5 >引入组件</h5>
                                        <div class="mb-2">引入一些通用代码页面，比如一个网站的导航和底部都是一样的，就单独做一个组件，通过include标签引入</div>
                                        <textarea class="form-control text-dark mb-3 p-2" rows="15">{{include  filename='component_header.html'/}}</textarea>
                                        <h5 >标签说明 <span class="text-sm text-secondary">filename : 文件名</span></h5>

                                        <?
                                        foreach($componentList as $v){
                                            echo "{{include filename='".$v."'}}<br>";
                                        }
                                        ?>
                                    </div>
                                    <div class="my-3 DemoDiv d-none" data="label">
                                        <h5 >自定义标签</h5>
                                        <div class="mb-2">后台自定义一些文字，列表等代码，在网站任意地方调用，通常用于广告代码，特殊JS统计代码等</div>
                                        <textarea class="form-control text-dark mb-3 p-2" rows="15">{{label:testlabel}}</textarea>
                                        <h5 >标签说明 <span class="text-sm text-secondary">label : 标签调用名 （后台创建的时候命名）</span></h5>
                                        <div>
                                            <?
                                            if(empty($DiyLabelArr)) echo '没有自定义标签';
                                            foreach($DiyLabelArr as $v){
                                                echo '{{label:'.$v['KeyName'].'}} : '.$v['Name'].'<br>';
                                            }
                                            ?>
                                        </div>

                                    </div>
                                    <div class="my-3 DemoDiv d-none" data="global">
                                        <h5 >全局标签</h5>
                                        <div class="mb-2">网站模板任何页面都可以调用的标签</div>
                                        <textarea class="form-control text-dark mb-3 p-2" rows="15">{{qcms:WebName}}</textarea>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Domain}} ： 网站域名<br>
                                            {{qcms:Static}} ： 静态文件夹路径 （/Static/）<br>
                                            {{qcms:PathImg}} ： 图片文件夹路径 （/Static/images/）<br>
                                            {{qcms:PathJs}} ： JS文件夹路径 （/Static/scripts/）<br>
                                            {{qcms:PathCss}} ： CSS文件夹路径 （/Static/styles/）<br>
                                            {{qcms:Scheme}} ： 协议 （用于区分 http, https）<br>
                                            {{qcms:WebName}} ： 网站名字<br>
                                            {{qcms:Logo}} ： 网站LOGO图片地址<br>
                                            {{qcms:Email}} ： 电子邮箱<br>
                                            {{qcms:Icp}} ： ICP备案号<br>
                                            {{qcms:WaBeian}} ： 网安备案号<br>
                                            {{qcms:Keywords}} ： SEO关键字<br>
                                            {{qcms:Description}} ： SEO简介<br>
                                            {{qcms:Copyright}} ： 网站版权<br>
                                            {{qcms:RegLenMin}} ： 注册最小长度<br>
                                            {{qcms:RegLenMax}} ： 注册最大长度<br>
                                            {{qcms:StatsCode}} ： 统计代码<br>
                                            {{qcms:Crumbs}} ： 面包屑地址<br>
                                            {{qcms:PathTemplate}} ： 模板静态文件路径<br>
                                            {{qcms:Search}} ： 搜索关键字 ($_GET['Search'])<br>
                                            {{qcms:Method}} ： 当前页方法 (首页:index, 分类:cate, 详情页:detail, 单页:page, 搜索页:search) <br>

                                            <?
                                            if(!empty($DiyField)){
                                                echo '<span class="text-danger">以下是自定义系统变量</span><br>';
                                            }
                                            foreach($DiyField as $v){
                                                echo '{{qcms:'.$v['Name'].'}} ： '.$v['Info'].'<br>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="my-3 DemoDiv d-none" data="cate">
                                        <h5 >分类标签</h5>
                                        <div class="mb-2">分类页和详情页专属标签，比如分类名等</div>
                                        <textarea class="form-control text-dark mb-3 p-2" rows="15">{{qcms:Cate_Name}}</textarea>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Cate_CateId}} ： 分类ID<br>
                                            {{qcms:Cate_PCateId}} ： 分类上级ID<br>
                                            {{qcms:Cate_TCateId}} ： 顶级分类ID<br>
                                            {{qcms:Cate_Name}} ： 分类名<br>
                                            {{qcms:Cate_NameEn}} ： 分类英文别名<br>
                                            {{qcms:Cate_Url}} ： 链接地址<br>
                                            {{qcms:Cate_Pic}} ： 分类图片<br>
                                            {{qcms:Cate_IsShow}} ： 分类显示（1：显示，2：不显示）<br>
                                            {{qcms:Cate_IsLink}} ： 是否外链 （1：外链， 2 不是外链）<br>
                                            {{qcms:Cate_LinkUrl}} ： 外链地址<br>
                                            {{qcms:Cate_SeoTitle}} ： SEO标题<br>
                                            {{qcms:Cate_Keywords}} ： SEO关键字<br>
                                            {{qcms:Cate_Description}} ： SEO简介<br>
                                            {{qcms:Cate_Content}} ： 分类内容详情<br>
                                            {{qcms:Cate_PinYin}} ： 全拼<br>
                                            {{qcms:Cate_PY}} ： 拼音首字母<br>
                                            {{qcms:Cate_HasSub}} ： 是否有子类<br>
                                            <?
                                            if(count($this->CateFieldArr) > 0){
                                                echo '<span class="text-danger">以下是分类自定义变量</span><br>';
                                                foreach($this->CateFieldArr as $v){
                                                    echo '{{qcms:Cate_'.$v['Name'].'}} ： '.$v['Comment'].'<br>';
                                                }
                                            }
                                            ?>

                                            <br><span class="text-danger font-weight-bold">以下是顶级分类变量</span><br>
                                            {{qcms:TopCate_CateId}} ： 顶级分类ID<br>
                                            {{qcms:TopCate_PCateId}} ： 顶级分类上级ID<br>
                                            {{qcms:TopCate_TCateId}} ： 顶级分类ID<br>
                                            {{qcms:TopCate_Name}} ： 顶级分类名<br>
                                            {{qcms:TopCate_NameEn}} ： 分类英文别名<br>
                                            {{qcms:TopCate_Url}} ： 顶级链接地址<br>
                                            {{qcms:TopCate_Pic}} ： 顶级分类图片<br>
                                            {{qcms:TopCate_IsShow}} ： 顶级分类显示（1：显示，2：不显示）<br>
                                            {{qcms:TopCate_IsLink}} ： 顶级是否外链 （1：外链， 2 不是外链）<br>
                                            {{qcms:TopCate_LinkUrl}} ： 顶级外链地址<br>
                                            {{qcms:TopCate_SeoTitle}} ： 顶级SEO标题<br>
                                            {{qcms:TopCate_Keywords}} ： 顶级SEO关键字<br>
                                            {{qcms:TopCate_Description}} ： 顶级SEO简介<br>
                                            {{qcms:TopCate_Content}} ： 顶级分类内容详情<br>
                                            {{qcms:TopCate_PinYin}} ： 顶级全拼<br>
                                            {{qcms:TopCate_PY}} ： 顶级拼音首字母<br>
                                            {{qcms:TopCate_HasSub}} ： 顶级拼音首字母<br>
                                            <?
                                            if(count($this->CateFieldArr) > 0){
                                                echo '<span class="text-danger">以下是顶级分类自定义变量</span><br>';
                                                foreach($this->CateFieldArr as $v){
                                                    echo '{{qcms:TopCate_'.$v['Name'].'}} ： '.$v['Comment'].'<br>';
                                                }
                                            }
                                            ?>

                                        </div>
                                    </div>
                                    <div class="my-3 DemoDiv d-none" data="detail">
                                        <h5 >详情页标签</h5>
                                        <div class="mb-2">文章详情页面专属标签，比如文章标题，文章内容等</div>
                                        <textarea class="form-control text-dark mb-3 p-2" rows="15">{{qcms:Detail_Title}}</textarea>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Detail_Id}} ： 详情ID<br>
                                            {{qcms:Detail_CateId}} ： 分类ID<br>
                                            {{qcms:Detail_Title}} ： 标题<br>
                                            {{qcms:Detail_STitle}} ：短标题<br>
                                            {{qcms:Detail_Url}} ： 链接地址<br>
                                            {{qcms:Detail_Tag}}： Tag<br>
                                            {{qcms:Detail_Pic}} ：图片<br>
                                            {{qcms:Detail_Source}} ： 来源<br>
                                            {{qcms:Detail_Author}} ： 作者<br>
                                            {{qcms:Detail_Keywords}} ： SEO关键字<br>
                                            {{qcms:Detail_Description}} ： SEO简介<br>
                                            {{qcms:Detail_TsAdd}} ： 添加时间（UNIX时间戳）<br>
                                            {{qcms:Detail_TsUpdate}} ： 最后更新时间（UNIX时间戳）<br>
                                            {{qcms:Detail_ReadNum}} ： 浏览次数<br>
                                            {{qcms:Detail_DownNum}} ： 下载次数<br>
                                            {{qcms:Detail_Coins}} ： 所需金币<br>
                                            {{qcms:Detail_Money}} ： 所需费用<br>
                                            {{qcms:Detail_Color}} ： 标题颜色<br>
                                            {{qcms:Detail_UserId}} ： 发布人ID<br>
                                            {{qcms:Detail_Good}} ： 好评数<br>
                                            {{qcms:Detail_Bad}} ： 差评数<br>
                                            {{qcms:Detail_Content}} ： 内容详情<br>
                                            {{qcms:Detail_IsLink}}  ： 是否外链 （1：外链， 2：不是外链）<br>
                                            {{qcms:Detail_LinkUrl}} ： 外链地址<br>
                                            {{qcms:Detail_IsBold}} ： 是否加粗 （1：加粗， 2：不加粗）<br>
                                            {{qcms:Detail_IsPic}} ： 是否有缩略图 （1：有， 2 没有）<br>
                                            {{qcms:Detail_IsSpuerRec}} ： 是否特推（1是， 2：不是）<br>
                                            {{qcms:Detail_IsHeadlines}} ： 是否头条（1是， 2：不是）<br>
                                            {{qcms:Detail_IsRec}} ： 是否推荐（1是， 2：不是）<br>
                                            {{qcms:Detail_PinYin}} ： 全拼<br>
                                            {{qcms:Detail_PY}} ： 拼音首字母<br>
                                            {{qcms:Detail_Prev}} : 上一篇<br>
                                            {{qcms:Detail_Next}} ： 下一篇<br>
                                            {{qcms:Detail_DownAddress}} : 下载地址 (下载字段名必须为 Address)<br>
                                            <?
                                            foreach($ModelArr as $k => $v){
                                                if(count($v['FieldJson']) > 0 ){
                                                    echo '<br><span class="text-danger">以下是'.$v['Name'].'模块自定义变量</span><br>';
                                                    foreach($v['FieldJson'] as $sk => $sv){
                                                        echo '{{qcms:Detail_'.$sv['Name'].'}} ： '.$sv['Comment'].'<br>';
                                                    }
                                                }
                                            }
                                            ?>

                                        </div>
                                    </div>
                                    <div class="my-3 DemoDiv d-none" data="page">
                                        <h5 >单页标签</h5>
                                        <div class="mb-2">单页专属标签，比如单页名字，单页内容等</div>
                                        <textarea class="form-control text-dark mb-3 p-2" rows="15">{{qcms:Page_Name}}</textarea>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Page_PageId}} ： 单页ID<br>
                                            {{qcms:Page_Name}} ： 单页名字<br>
                                            {{qcms:Page_NameEn}} ： 单页英文别名<br>
                                            {{qcms:Page_SeoTitle}} ： SEO标题<br>
                                            {{qcms:Page_Keywords}} ： SEO关键字<br>
                                            {{qcms:Page_Description}} ： SEO简介<br>
                                            {{qcms:Page_Content}} ： 内容<br>
                                        </div>
                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="get">
                                        <h5 >获取单条数据</h5>
                                        <div class="mb-2">任何页面调用 分类，单页，文章详情 的数据调用</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
// 分类数据单条调用例子
{{get Type='cate' Index='1'}}
    <a href="{{qcms:Get_Url}}">{{qcms:Get_Name}}</a>
{{/get}}

// 单页数据单条调用例子
{{get Type='page' Index='1'}}
    <a href="{{qcms:Get_Url}}">{{qcms:Get_Name}}</a>
{{/get}}

// 详情数据单条调用例子
{{get Type='detail' Index='1'}}
    <a href="{{qcms:Get_Url}}">{{qcms:Get_Title}}</a>
{{/get}}

// 幻灯片数据单条调用例子
{{get Type='swiper' Index='1'}}
    <a href="{{qcms:Get_Link}}"><img src="{{qcms:Get_Pic}}"></a>
{{/get}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            Type : 调用类型 (cate:分类, page:单页, detail:内容详情)
                                            Index : 索引ID (分类：分类的ID，单页：单页ID，内容：详情ID)
                                        </div>
                                        <h5 >标签说明</h5>
                                        <div class="d-flex">
                                            <div class="mr-3">
                                                <h6 class="py-2 font-weight-bold">分类字段</h6>
                                                {{qcms:Get_CateId}} ： 分类ID<br>
                                                {{qcms:Get_PCateId}} ： 分类上级ID<br>
                                                {{qcms:Get_Name}} ： 分类名<br>
                                                {{qcms:Get_NameEn}} ： 分类英文别名<br>
                                                {{qcms:Get_Pic}} ： 分类图片<br>
                                                {{qcms:Get_IsShow}} ： 分类显示（1：显示，2：不显示）<br>
                                                {{qcms:Get_IsLink}} ： 是否外链 （1：外链， 2 不是外链）<br>
                                                {{qcms:Get_LinkUrl}} ： 外链地址<br>
                                                {{qcms:Get_SeoTitle}} ： SEO标题<br>
                                                {{qcms:Get_Keywords}} ： SEO关键字<br>
                                                {{qcms:Get_Description}} ： SEO简介<br>
                                                {{qcms:Get_Content}} ： 分类内容详情<br>
                                                {{qcms:Get_PinYin}} ： 全拼<br>
                                                {{qcms:Get_PY}} ： 拼音首字母<br>
                                                {{qcms:Get_Url}} ： 链接地址<br>
                                                <?
                                            if(count($this->CateFieldArr) > 0){
                                                echo '<span class="text-danger">以下是分类自定义变量</span><br>';
                                                foreach($this->CateFieldArr as $v){
                                                    echo '{{qcms:Get_'.$v['Name'].'}} ： '.$v['Comment'].'<br>';
                                                }
                                            }
                                            ?>
                                            </div>
                                            <div class="mr-3">
                                                <h6 class="py-2 font-weight-bold">单页字段</h6>
                                                {{qcms:Get_PageId}} ： 单页ID<br>
                                                {{qcms:Get_Name}} ： 单页名字<br>
                                                {{qcms:Get_NameEn}} ： 分类英文别名<br>
                                                {{qcms:Get_SeoTitle}} ： SEO标题<br>
                                                {{qcms:Get_Keywords}} ： SEO关键字<br>
                                                {{qcms:Get_Description}} ： SEO简介<br>
                                                {{qcms:Get_Content}} ： 内容<br>
                                                {{qcms:Get_Url}} ： 链接地址<br>
                                            </div>
                                            <div class="mr-3">
                                                <h6 class="py-2 font-weight-bold">详情字段</h6>
                                                {{qcms:Get_Id}} ： 详情ID<br>
                                                {{qcms:Get_CateId}} ： 分类ID<br>
                                                {{qcms:Get_Title}} ： 标题<br>
                                                {{qcms:Get_STitle}} ：短标题<br>
                                                {{qcms:Get_Tag}}： Tag<br>
                                                {{qcms:Get_Pic}} ：图片<br>
                                                {{qcms:Get_Source}} ： 来源<br>
                                                {{qcms:Get_Author}} ： 作者<br>
                                                {{qcms:Get_Keywords}} ： SEO关键字<br>
                                                {{qcms:Get_Description}} ： SEO简介<br>
                                                {{qcms:Get_Summary}} ： 摘要<br>
                                                {{qcms:Get_TsAdd}} ： 添加时间（UNIX时间戳）<br>
                                                {{qcms:Get_TsUpdate}} ： 最后更新时间（UNIX时间戳）<br>
                                                {{qcms:Get_ReadNum}} ： 浏览次数<br>
                                                {{qcms:Get_DownNum}} ： 下载次数<br>
                                                {{qcms:Get_Coins}} ： 所需金币<br>
                                                {{qcms:Get_Money}} ： 所需费用<br>
                                                {{qcms:Get_Color}} ： 标题颜色<br>
                                                {{qcms:Get_UserId}} ： 发布人ID<br>
                                                {{qcms:Get_Good}} ： 好评数<br>
                                                {{qcms:Get_Bad}} ： 差评数<br>
                                                {{qcms:Get_Content}} ： 内容详情<br>
                                                {{qcms:Get_IsLink}}  ： 是否外链 （1：外链， 2：不是外链）<br>
                                                {{qcms:Get_LinkUrl}} ： 外链地址<br>
                                                {{qcms:Get_IsBold}} ： 是否加粗 （1：加粗， 2：不加粗）<br>
                                                {{qcms:Get_IsPic}} ： 是否有缩略图 （1：有， 2 没有）<br>
                                                {{qcms:Get_IsSpuerRec}} ： 是否特推（1是， 2：不是）<br>
                                                {{qcms:Get_IsHeadlines}} ： 是否头条（1是， 2：不是）<br>
                                                {{qcms:Get_IsRec}} ： 是否推荐（1是， 2：不是）<br>
                                                {{qcms:Get_PinYin}} ： 全拼<br>
                                                {{qcms:Get_PY}} ： 拼音首字母<br>
                                                {{qcms:Get_Url}} ： 链接地址<br>
                                                <?
                                            foreach($ModelArr as $k => $v){
                                                if(count($v['FieldJson']) > 0 ){
                                                    echo '<br><span class="text-danger">以下是'.$v['Name'].'模块自定义变量</span><br>';
                                                    foreach($v['FieldJson'] as $sk => $sv){
                                                        echo '{{qcms:Get_'.$sv['Name'].'}} ： '.$sv['Comment'].'<br>';
                                                    }
                                                }
                                            }
                                            ?>
                                            </div>
                                            <div class="mr-3">
                                                <h6 class="py-2 font-weight-bold">幻灯片单张图片</h6>
                                                {{qcms:Get_SwiperId}} ： 图片ID<br>
                                                {{qcms:Get_Pic}} ： 图片地址<br>
                                                {{qcms:Get_Title}} ： 图片标题<br>
                                                {{qcms:Get_Summary}} ： 图片摘要<br>
                                                {{qcms:Get_Link}} ： 链接地址<br>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="menu">
                                        <h5 >一级菜单列表</h5>
                                        <div class="mb-2">一级菜单列表，循环列表</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{menu PCateId='0'}}
    <a href="{{qcms:Menu_Url}}">{{qcms:Menu_Name}}</a>
{{/menu}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            PCateId : 上级分类ID （不填默认为0）<br>
                                            Row : 调用数量<br>
                                            Start : 开始记录，默认：0 （比如要获取第3-6条记录，Start='3' Row='3'）<br>
                                        </div>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Menu_CateId}} ： 分类ID<br>
                                            {{qcms:Menu_PCateId}} ： 上级分类ID<br>
                                            {{qcms:Menu_Name}} ： 分类名字<br>
                                            {{qcms:Menu_NameEn}} ： 分类英文别名<br>
                                            {{qcms:Menu_Pic}} ： 分类图片<br>
                                            {{qcms:Menu_SeoTitle}} ： SEO标题<br>
                                            {{qcms:Menu_Keywords}} ： SEO关键字<br>
                                            {{qcms:Menu_Description}} ： SEO简介<br>
                                            {{qcms:Menu_Url}} ： 分类链接<br>
                                            {{qcms:Menu_HasSub}} ： 是否包含子分类 （1：是 0：否）<br>
                                            {{qcms:Menu_i}} ： 自曾数（从1开始）<br>
                                            {{qcms:Menu_n}} ： 自曾数（从0开始）<br>
                                            {{qcms:Menu_m}} ： 隔行数（第一行0，第二行1，第三行0 以此类推）<br>
                                            <?
                                            if(count($this->CateFieldArr) > 0){
                                                echo '<span class="text-danger">以下是分类自定义变量</span><br>';
                                                foreach($this->CateFieldArr as $v){
                                                    echo '{{qcms:Menu_'.$v['Name'].'}} ： '.$v['Comment'].'<br>';
                                                }
                                            }
                                            ?>

                                        </div>
                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="smenu">
                                        <h5 >二级菜单列表</h5>
                                        <div class="mb-2">二级菜单列表，循环列表</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{smenu PCateId='0'}}
    <a href="{{qcms:sMenu_Url}}">{{qcms:sMenu_Name}}</a>
{{/smenu}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            PCateId : 上级分类ID （不填默认为0）<br>
                                            Row : 调用数量<br>
                                            Start : 开始记录，默认：0 （比如要获取第3-6条记录，Start='3' Row='3'）<br>
                                        </div>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:sMenu_CateId}} ： 分类ID<br>
                                            {{qcms:sMenu_PCateId}} ： 上级分类ID<br>
                                            {{qcms:sMenu_Name}} ： 分类名字<br>
                                            {{qcms:sMenu_NameEn}} ： 分类英文别名<br>
                                            {{qcms:sMenu_Pic}} ： 分类图片<br>
                                            {{qcms:sMenu_SeoTitle}} ： SEO标题<br>
                                            {{qcms:sMenu_Keywords}} ： SEO关键字<br>
                                            {{qcms:sMenu_Description}} ： SEO简介<br>
                                            {{qcms:sMenu_Url}} ： 分类链接<br>
                                            {{qcms:sMenu_HasSub}} ： 是否包含子分类 （1：是 0：否）<br>
                                            {{qcms:sMenu_i}} ： 自曾数（从1开始）<br>
                                            {{qcms:sMenu_n}} ： 自曾数（从0开始）<br>
                                            {{qcms:sMenu_m}} ： 隔行数（第一行0，第二行1，第三行0 以此类推）<br>
                                            <?
                                            if(count($this->CateFieldArr) > 0){
                                                echo '<span class="text-danger">以下是分类自定义变量</span><br>';
                                                foreach($this->CateFieldArr as $v){
                                                    echo '{{qcms:sMenu_'.$v['Name'].'}} ： '.$v['Comment'].'<br>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="my-3 DemoDiv d-none" data="ssmenu">
                                        <h5 >三级菜单列表</h5>
                                        <div class="mb-2">三级菜单列表，循环列表</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{ssmenu PCateId='0'}}
    <a href="{{qcms:ssMenu_Url}}">{{qcms:ssMenu_Name}}</a>
{{/ssmenu}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            PCateId : 上级分类ID （不填默认为0）<br>
                                            Row : 调用数量<br>
                                            Start : 开始记录，默认：0 （比如要获取第3-6条记录，Start='3' Row='3'）<br>
                                        </div>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:ssMenu_CateId}} ： 分类ID<br>
                                            {{qcms:ssMenu_PCateId}} ： 上级分类ID<br>
                                            {{qcms:ssMenu_Name}} ： 分类名字<br>
                                            {{qcms:ssMenu_NameEn}} ： 分类英文别名<br>
                                            {{qcms:ssMenu_Pic}} ： 分类图片<br>
                                            {{qcms:ssMenu_SeoTitle}} ： SEO标题<br>
                                            {{qcms:ssMenu_Keywords}} ： SEO关键字<br>
                                            {{qcms:ssMenu_Description}} ： SEO简介<br>
                                            {{qcms:ssMenu_Url}} ： 分类链接<br>
                                            {{qcms:ssMenu_HasSub}} ： 是否包含子分类 （1：是 0：否）<br>
                                            {{qcms:ssMenu_i}} ： 自曾数（从1开始）<br>
                                            {{qcms:ssMenu_n}} ： 自曾数（从0开始）<br>
                                            {{qcms:ssMenu_m}} ： 隔行数（第一行0，第二行1，第三行0 以此类推）<br>
                                            <?
                                            if(count($this->CateFieldArr) > 0){
                                                echo '<span class="text-danger">以下是分类自定义变量</span><br>';
                                                foreach($this->CateFieldArr as $v){
                                                    echo '{{qcms:ssMenu_'.$v['Name'].'}} ： '.$v['Comment'].'<br>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="list">
                                        <h5 >列表标签</h5>
                                        <div class="mb-2">列表形式调用内容数据</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{list Model='article' CateId='1' Row='10'}}
    <a href="{{qcms:List_Url}}">{{qcms:List_Title}}</a>
{{/list}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            Model : 模型调用名，默认 ：article （article：文章, product:产品， album：相册， down：下载 ）<br>
                                            Row：行数，默认：10<br>
                                            CateId : 分类ID， 默认：0 （模型下所有文章）<br>
                                            Sort ： 排序方式 (默认:Sort, ReadNum:点击数,TsUpdate:更新时间,Good:好评数)<br>
                                            Keyword：关键字 (精准匹配Tag)<br>
                                            Search：关键字 (模糊匹配标题)<br>
                                            Ids ： 文章ID (用 | 分割，例：12|23|33)<br>
                                            Attr：属性 (sr:特推、hl:头条、re:推荐、ip:带图, 例 ：sr,hl,re,ip hl)<br>
                                            Start：开始记录，默认：0 （比如要获取第3-6条记录，Start='3' Row='3'）<br>
                                            IsPage：开启分页 （默认 0：关闭, 1：开启 ）<br>

                                        </div>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:List_Id}} ： 内容ID<br>
{{qcms:List_Title}} ： 标题<br>
{{qcms:List_STitle}} ： 短标题<br>
{{qcms:List_Tag}} ： Tag<br>
{{qcms:List_Pic}} ： 图片<br>
{{qcms:List_Source}} ： 来源<br>
{{qcms:List_Author}} ： 作者<br>
{{qcms:List_Keywords}} ： SEO关键字<br>
{{qcms:List_Description}} ： SEO简介<br>
{{qcms:List_Summary}} ： 摘要<br>
{{qcms:List_TsAdd}} ： 添加时间<br>
{{qcms:List_TsUpdate}} ： 更新时间<br>
{{qcms:List_ReadNum}} ： 阅读数量<br>
{{qcms:List_DownNum}} ： 下载数量<br>
{{qcms:List_Coins}} ： 所需金币<br>
{{qcms:List_Money}} ： 所需费用<br>
{{qcms:List_Color}} ： 标题颜色<br>
{{qcms:List_UserId}} ： 用户ID<br>
{{qcms:List_Good}} ： 好评数<br>
{{qcms:List_Bad}} ： 差评数<br>
{{qcms:List_IsLink}} ： 是否外链 （1：外链， 2：不是外链）<br>
{{qcms:List_LinkUrl}} ： 外链地址<br>
{{qcms:List_IsBold}} ： 是否加粗 （1：加粗， 2：不加粗）<br>
{{qcms:List_IsPic}} ： 是否有缩略图 （1：有， 2 没有）<br>
{{qcms:List_IsSpuerRec}} ： 是否特推（1是， 2：不是）<br>
{{qcms:List_IsHeadlines}} ： 是否头条（1是， 2：不是）<br>
{{qcms:List_IsRec}} ： 是否特推（1是， 2：不是）<br>
{{qcms:List_PinYin}} ： 拼音全拼<br>
{{qcms:List_PY}} ： 拼音首字母<br>
{{qcms:List_i}} ： 自曾数（从1开始）<br>
{{qcms:List_n}} ： 自曾数（从0开始）<br>
{{qcms:List_m}} ： 隔行数（第一行0，第二行1，第三行0 以此类推）<br>
{{qcms:List_Url}} ： 内容地址<br>
{{qcms:List_CateId}} ： 分类ID<br>
{{qcms:List_CateName}} ： 分类名<br>
{{qcms:List_CateNameEn}} ： 分类英文别名<br>
{{qcms:List_CatePic}} ： 分类图片<br>
{{qcms:List_CateUrl}} ： 分类地址<br>
{{qcms:List_PageBar}} ： 分页 (IsPage 必须是1 才生效)<br>

<?
                                            foreach($ModelArr as $k => $v){
                                                if(count($v['FieldJson']) > 0 ){
                                                    echo '<br><span class="text-danger">以下是'.$v['Name'].'模块自定义变量</span><br>';
                                                    foreach($v['FieldJson'] as $sk => $sv){
                                                        if($sv['IsList'] != 1) continue;
                                                        echo '{{qcms:List_'.$sv['Name'].'}} ： '.$sv['Comment'].'<br>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>

<div class="my-3 DemoDiv d-none" data="link">
                                        <h5 >友情链接</h5>
                                        <div class="mb-2">列表形式调用友情链接数据</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{link IsIndex='1'}}
    <a target="_blank" href="{{qcms:Link_Link}}">{{qcms:Link_Name}}</a>
{{/link}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            LinkCateId : 分类ID<br>
                                            IsIndex : 是否首页 (1:只选首页的链接，2：非首页)<br>
                                            Row : 数量 (默认取100条)<br>
                                        </div>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Link_Name}} ： 网站名字<br>
                                            {{qcms:Link_Logo}} ： 网站Logo<br>
                                            {{qcms:Link_Link}} ： 链接网站地址<br>
                                            {{qcms:Link_Info}} ： 网站简介<br>
                                            {{qcms:Link_Mail}} ： 站长邮箱<br>
                                            {{qcms:Link_IsIndex}} ： 是否首页<br>
                                            {{qcms:Link_i}} ： 自增编号 (从1开始)<br>
                                            {{qcms:Link_n}} ： 自增编号 (从0开始)<br>
                                            {{qcms:Link_m}} ： 隔行数（第一行0，第二行1，第三行0 以此类推）<br>
                                        </div>
                                    </div>


                                    <div class="my-3 DemoDiv d-none" data="loop">
                                        <h5 >万能标签</h5>
                                        <div class="mb-2">列表形式调用数据库里任何数据</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{loop sql='select * from qc_user'}}
    用户昵称：{{qcms:Loop_NickName}}
    用户头像： <img src="{{qcms:Loop_Head}}" style="height: 100px;width:100px;" />
{{/loop}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            sql : SQL语句
                                        </div>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:loop_字段名}} ： 字段名就是标签，字段意思请查看数据库字段
                                        </div>
                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="slide">
                                        <h5 >幻灯片</h5>
                                        <div class="mb-2">列表形式调用幻灯片数据</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{slide SwiperCateId='1'}}
    <a href="{{qcms:Slide_Link}}"><img src="{{qcms:Slide_Pic}}"/></a>
{{/slide}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            SwiperCateId : 幻灯片ID
                                        </div>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Slide_SwiperId}} ： 图片ID<br>
                                            {{qcms:Slide_Pic}} ： 图片地址<br>
                                            {{qcms:Slide_Title}} ： 图片标题<br>
                                            {{qcms:Slide_Link}} ： 链接地址<br>
                                            {{qcms:Slide_Sort}} ： 排序<br>
                                            {{qcms:Slide_Summary}} ： 摘要<br>
                                            {{qcms:Slide_i}} ： 自曾数（从1开始）<br>
                                            {{qcms:Slide_n}} ： 自曾数（从0开始）<br>
                                            {{qcms:Slide_m}} ： 隔行数（第一行0，第二行1，第三行0 以此类推）<br>
                                        </div>
                                    </div>

                                     <div class="my-3 DemoDiv d-none" data="tag">
                                        <h5 >Tag标签</h5>
                                        <div class="mb-2">列表形式调用Tag数据</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{tag Row='10'}}
    <a href="{{qcms:Tag_Url}}">{{qcms:Tag_Name}}</a>
{{/tag}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            Row : 调用数量
                                        </div>
                                        <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Tag_TagId}} ： TagId<br>
                                            {{qcms:Tag_Name}} ： 关键字<br>
                                            {{qcms:Tag_Total}} ： 数量<br>
                                            {{qcms:Tag_Url}} ： 链接地址<br>
                                            {{qcms:Tag_i}} ： 自曾数（从1开始）<br>
                                            {{qcms:Tag_n}} ： 自曾数（从0开始）<br>
                                            {{qcms:Tag_m}} ： 隔行数（第一行0，第二行1，第三行0 以此类推）<br>
                                        </div>
                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="if">
                                        <h5 >if条件标签</h5>
                                        <div class="mb-2">简单的IF判断，可用于简单判断 (可使用 >、>=、<、<=、==、!= 这6种判断)</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{if '{{qcms:WebName}}' >= 'QCMS官网'}}
我是QCMS官网
{{else}}
我是其他网站
{{/if}}

<!====================================>

{{if '{{qcms:WebName}}' >= 'QCMS官网'}}
我是QCMS官网
{{/if}}
</textarea>



                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="date">
                                        <h5 >日期标签</h5>
                                        <div class="mb-2">日期和时间 格式化标签</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{date format='Y-m-d' time='{{qcms:List_TsUpdate}}'}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            format : 格式字串 (Y-m-d H:i:s 转换成 '2022-03-06 12:20:36')
                                            特殊处理 special （3天前）<br>
                                            time : Unix 时间戳 （ 1646540436 ）
                                        </div>

                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="substr">
                                        <h5 >截取字符串</h5>
                                        <div class="mb-2">截取字符串长度</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{cut Len='20' Str='{{qcms:List_Title}}'}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            Len : 字符串长度<br>
                                            Str : 字符串内容
                                        </div>

                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="thumb">
                                        <h5 >缩略图</h5>
                                        <div class="mb-2">生成缩略图(系统管理，基本设置，附件设置 里需设置要需要的尺寸)</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{thumb Width='240' Height='180' Img='{{qcms:List_Pic}}'}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            Width : 缩略图宽度<br>
                                            Height : 缩略图高度<br>
                                            Img ： 缩略图原图地址 (注：只适用于上传的图片)<br>
                                        </div>

                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="math">
                                        <h5 >数学标签</h5>
                                        <div class="mb-2">实现了 加 减 乘 除 和 求余 功能</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{math '5'+'2'}}    // 加法
{{math '5'-'2'}}    // 减法
{{math '5'*'2'}}    // 乘法
{{math '5'/'2'}}    // 除法
{{math '5'%'2'}}    // 求余
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            无
                                        </div>

                                    </div>
                                    <div class="my-3 DemoDiv d-none" data="replace">
                                        <h5 >替换标签</h5>
                                        <div class="mb-2">实现了替换字符串功能</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{replace Search='刘德华' Replace='张学友' Str='我是刘德华'}}  //结果为 ： 我是张学友

//高级一点，批量替换
{{replace Search='刘德华|张学友' Replace='关之琳|小犹太' Str='我看到刘德华和张学友在一起拍电影'}}  //结果为 ： 我看到关之琳和小犹太在一起拍电影
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            Search ： 替换前得字符串<br>
                                            Replace：替换后得字符串<br>
                                            Str ： 字符串内容<br>
                                        </div>

                                    </div>

                                    <div class="my-3 DemoDiv d-none" data="photo">
                                        <h5 >相片标签</h5>
                                        <div class="mb-2">相片循环列表（相册模型专有）</div>

<textarea class="form-control text-dark mb-3 p-2" rows="15">
{{photo}}
    <li class="swiper-slide"><img class="w-100 d-block" src="{{qcms:Photo_Path}}"></li>
{{/photo}}
</textarea>
                                        <h5 >属性说明</h5>
                                        <div class="mb-2">
                                            Index : 索引ID （默认当前文章的ID）<br>
                                            Row : 数量 （默认调用全部）<br>

                                            <h5 >标签说明</h5>
                                        <div>
                                            {{qcms:Photo_Path}} ： 图片地址<br>
                                            {{qcms:Photo_Name}} ： 图片名称<br>
                                            {{qcms:Photo_Size}} ： 图片大小<br>
                                            {{qcms:Photo_i}} ： 自曾数（从1开始）<br>
                                            {{qcms:Photo_n}} ： 自曾数（从0开始）<br>
                                            {{qcms:Photo_m}} ： 隔行数（第一行0，第二行1，第三行0 以此类推）<br>
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
    <script type="text/javascript">
        var SelectLabel = 'include';
        $(function(){
            DemoDivView();
            $('.DisplayBtn').click(function(){

                SelectLabel = $(this).attr('data');
                DemoDivView();
            })
        })

        var DemoDivView = function(){
            $('.DisplayBtn').removeClass('btn-primary').addClass('btn-default');
            $('.DisplayBtn[data='+SelectLabel+']').removeClass('btn-default').addClass('btn-primary');
            $('.DemoDiv').addClass('d-none');
            $('.DemoDiv[data='+SelectLabel+']').removeClass('d-none');
        }
    </script>
</body>

</html>