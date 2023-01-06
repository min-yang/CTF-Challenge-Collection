# 题目描述

We've gone ahead and pulled this file, but the jargon is just too much for us.

Go go gadget reverse engineer - can you understand the jargon and extract the flag?

# 解决方案

解压[go-jargon-go.txt.7z](file/go-jargon-go.txt.7z)得到go-jargon-go.txt文件，然后运行[solve.py](solution/solve.py)得到[jargon.bin](solution/jargon.bin)，这是一个ELF二进制文件，可以直接运行，运行后输出flag。

参考链接：

- https://github.com/jselliott/NahamConEU22/tree/main/challenges/ReverseEngineering/go-jargon-go