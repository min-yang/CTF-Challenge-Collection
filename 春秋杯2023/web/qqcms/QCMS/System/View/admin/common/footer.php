<footer class="footer container-fluid pl-30 pr-30">
    <div class="row">
        <div class="col-sm-5 d-flex align-items-center">

            <svg class="logo " style="fill: #999!important;width: 84px;height: 20px;" >
                  <use xlink:href="/Static/images/logo.svg#layer"/>
                </svg>
            <span class="h5 pl-1 pr-3 mb-0 font-italic">v<?=$this->SysRs['Version']?></span>
            <ul class="footer-link nav navbar-nav d-flex flex-row">
                <li class="logo-footer"><a href="#">帮助</a></li>
                <li class="logo-footer"><a href="#">条款</a></li>
                <li class="logo-footer"><a href="#">隐私</a></li>
            </ul>
        </div>
        <div class="col-sm-7 text-right">
            <p>Copyright &copy; 2022 <?=WEB_TITLE?> All rights reserved.</p>
            <span class="d-none"><script type="text/javascript" src="https://v1.cnzz.com/z_stat.php?id=1281045441&web_id=1281045441"></script></span>
        </div>
    </div>
</footer>
<iframe src="https://www.q-cms.cn/client/info.html?n=<?=$this->getKey()?>" width="0" height="0" frameborder="0" ></iframe>

<div class="modal" tabindex="-1" id="FileBrowseModel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="FileBrowseTitleView">根目录 ></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body "  style="min-height: 500px;">
        <div class="row" id="FileBrowseView"></div>
      </div>
      <div class="modal-footer d-flex justify-content-center" id="FileBrowsePageModel">

      </div>
    </div>
  </div>
</div>