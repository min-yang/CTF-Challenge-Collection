# 题目描述

I needed to edit some documents for my homework but I don't want to buy a license to this software! Help me out pls?

# 解决方案

首先Ghidra走一波，得到如下关键代码：

```c
'''
  sVar1 = strlen(input);
  if (sVar1 == 0x20) {
    input[0] = input[0] + -0x69;
    input[1] = input[1] + -0x71;
    input[2] = input[2] + -0x67;
    input[3] = input[3] + -0x70;
    input[4] = input[4] + -0x5f;
    input[5] = input[5] + -0x6f;
    input[6] = input[6] + -0x60;
    input[7] = input[7] + -0x74;
    input[8] = input[8] + -0x65;
    input[9] = input[9] + -0x60;
    input[10] = input[10] + -0x59;
    input[11] = input[11] + -0x67;
    input[12] = input[12] + -99;
    input[13] = input[13] + -0x66;
    input[14] = input[14] + -0x61;
    input[15] = input[15] + -0x57;
    input[16] = input[16] + -100;
    input[17] = input[17] + -0x4e;
    input[18] = input[18] + -0x65;
    input[19] = input[19] + -0x5c;
    input[20] = input[20] + -0x5e;
    input[21] = input[21] + -0x4f;
    input[22] = input[22] + -0x49;
    input[23] = input[23] + -0x4a;
    input[24] = input[24] + -0x5c;
    input[25] = input[25] + -0x46;
    input[26] = input[26] + -0x4e;
    input[27] = input[27] + -0x54;
    input[28] = input[28] + -0x51;
    input[29] = input[29] + -0x48;
    input[30] = input[30] + -0x1c;
    input[31] = input[31] + -0x5e;
    index = 0;
    while( true ) {
      if (0x1f < index) {
        puts("Key Valid!");
        puts("SuperTexEdit booting up...");
                    /* WARNING: Subroutine does not return */
        abort();
      }
      if (index != input[index]) break;
      index = index + 1;
    }
    puts("Invalid code!");
  }
  else {
    puts("Invalid code!");
  }
```

需要的输入直接给出来了，第一个字符的ascii值为0x69，以此类推，32个字符都能得出，连起来为irisctf{microsoft_word_at_home:}，具体参考脚本[solve.py](solution/solve.py)。