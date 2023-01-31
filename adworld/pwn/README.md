# Pwn挑战合集

- [CGfsb](CGfsb)：格式化字符串漏洞任意写
- [cgpwn2](cgpwn2)：缓冲区溢出修改返回地址
- [dice_game](dice_game)：缓冲区溢出修改随机数种子
- [forgot](forgot)：缓冲区溢出修改稍后会调用的函数指针
- [guess_num](guess_num)：缓冲区溢出修改随机数种子
- [int_overflow](int_overflow)：首先利用整数溢出绕过检查，然后利用缓冲区溢出修改返回地址
- [level0](level0)：缓冲区溢出修改返回地址
- [level2](level2)：缓冲区溢出控制执行流程
- [level3](level3)：利用缓冲区溢出构造ROP泄露libc基址，然后跳转到system函数
- [Mary_Morton](Mary_Morton)：首先利用格式化字符串漏洞泄漏canary的值，然后利用缓冲区溢出漏洞控制执行流程
- [new-easypwn](new-easypwn)：无法成功利用，有待进一步研究
- [pwn-100](pwn-100)：存在缓冲区溢出，但是不知道libc的地址，需要使用DynELF找出libc的地址，然后控制执行流程
- [stack2](stack2)：代码逻辑漏洞，可以往某段内存区域写入任意数据，借此直接修改返回地址控制执行流程
- [string](string)：利用格式化字符串漏洞任意写绕过检查，然后就可以传入一段shellcode拿到shell权限
- [warmup](warmup)：缓冲区溢出盲打，需要爆破出返回地址的位置然后控制执行流程