# 题目信息

daeMOn

Just read the flag!

http://readme.chal.idek.team:1337/


# 解决方案

分析源码发现，我们只能上传最多10个整数，且每个整数必须大于0，小于等于100，服务器按照我们指定的整数作为size来读取buffer，我们需要读到12625处，服务器才会返回flag，但是按照逻辑我们最多读到1000处，因此需要分析代码中可以利用的点，最后找到下面这段代码：

```go
func GetValidatorCtxData(ctx context.Context) (io.Reader, int) {
    reader := ctx.Value(reqValReaderKey).(io.Reader)
    size := ctx.Value(reqValSizeKey).(int)
    if size >= 100 {
        reader = bufio.NewReader(reader)
    }
    return reader, size
}
```

这里的reader直接通过bufio.NewReader构造，通过查阅文档，发现这样构造出的reader每次会读取默认大小的字节，即4096个，利用这一点我们可以读到12625处，发送`{"Orders": [100, 100, 100, 99, 99, 99, 40]}`到服务器，即可拿到flag。

```
idek{BufF3r_0wn3rsh1p_c4n_b1t3!}
```