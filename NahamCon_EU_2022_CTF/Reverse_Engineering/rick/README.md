# 题目描述

These files were on a USB I bought from a pawn shop.

# 解决方案

Ghidra逆向分析program文件，发现如下关键代码：

```c
bool FUN_00101309(void)

{
  int iVar1;
  ulong uVar2;
  void *__ptr;
  bool bVar3;
  
  DAT_00104050 = fopen("input.txt","rb");
  bVar3 = DAT_00104050 != (FILE *)0x0;
  if (bVar3) {
    fseek(DAT_00104050,0,2);
    uVar2 = ftell(DAT_00104050);
    fseek(DAT_00104050,0,0);
    __ptr = malloc((long)(int)uVar2);
    fread(__ptr,(long)(int)uVar2,1,DAT_00104050);
    fclose(DAT_00104050);
    iVar1 = FUN_0010144b(__ptr,uVar2 & 0xffffffff);
    DAT_00104020 = fopen("ct.enc","wb");
    fwrite(DAT_00104028,(long)iVar1,1,DAT_00104020);
    fclose(DAT_00104020);
  }
  else {
    puts("Could not open flag.txt\n");
  }
  return bVar3;
}
```

```c
int FUN_0010144b(uchar *param_1,int param_2)

{
  long in_FS_OFFSET;
  int local_28;
  int local_24;
  EVP_CIPHER_CTX *local_20;
  EVP_CIPHER *local_18;
  long local_10;
  
  local_10 = *(long *)(in_FS_OFFSET + 0x28);
  local_28 = param_2;
  memset(&iv,0,0x10);
  DAT_00104028 = (uchar *)malloc((long)param_2);
  local_20 = EVP_CIPHER_CTX_new();
  local_18 = EVP_aes_128_cbc();
  FUN_00101539();
  EVP_EncryptInit(local_20,local_18,&key,&iv);
  EVP_EncryptUpdate(local_20,DAT_00104028,&local_28,param_1,param_2);
  EVP_EncryptFinal_ex(local_20,DAT_00104028 + local_28,&local_24);
  if (local_10 != *(long *)(in_FS_OFFSET + 0x28)) {
                    /* WARNING: Subroutine does not return */
    __stack_chk_fail();
  }
  return local_24 + local_28;
}
```

根据`memset(&iv, 0, 0x10)`可以知道iv为16个字节的0，key可能跟FUN_00101539相关，其代码如下：

```c
void FUN_00101539(void)

{
  uint local_10;
  int local_c;
  
  for (local_c = 0; local_c < 0x10; local_c = local_c + 1) {
    if (local_c == 0) {
      local_10 = 0x27e2;
    }
    else {
      local_10 = (local_10 + local_c) * 4 ^ 0x29fa;
    }
    (&key)[local_c] = (char)local_10;
  }
  return;
}
```

看不懂，考虑直接动态调试，看对应地址的值，由于该程序开启了PIE保护，其运行时地址是随机生成的，无法下断点，这里借助[gef](https://github.com/hugsy/gef)工具的`entry-break`命令，该命令在第一条指令处自动中断；此外根据FUN_00101309的代码发现，如果当前目录下没有input.txt文件，程序就不会运行到加密部分，因此需要在当前目录下新建input.txt文件，随便写些内容进去，此外需要备份以下ct.enc文件，因为会被覆盖掉，然后单步调试，跟到如下汇编部分：

```
001014c1 48 8d 0d        LEA        RCX,[iv]                                         = ??
         78 2b 00 00
001014c8 48 8d 15        LEA        RDX,[key]                                        = ??
         61 2b 00 00
001014cf 48 89 c7        MOV        RDI,RAX
001014d2 e8 99 fc        CALL       <EXTERNAL>::EVP_EncryptInit                      int EVP_EncryptInit(EVP_CIPHER_C
         ff ff
```

然后查看相关地址，就能知道key为e2761a8eb2264abee2567aee1286aa1e（16进制表示），通过key和iv使用AES解密ct.enc即可得到如下内容：

```
Iâ..m Rick Harrison, and this is my pawn shop. I work here with my old man and my son, Big Hoss. Everything in here has a story and a price. One thing Iâ..ve learned after 21 years â.. you never know WHAT is gonna come through that door.

flag{6265a883a2d001d4fe291277bb171bac}
```
