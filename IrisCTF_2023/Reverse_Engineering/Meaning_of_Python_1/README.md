# 题目描述

Python. Python! What is Python? Is this Python? Why does this Python look so strange? All this and more on in... the... textfield...

# 解决方案

根据[源码](file/python1.py)，scramble1和scramble2两个函数不会改变flag的值，因此直接zlib解压即可拿到flag，具体参考脚本[solve.py](solution/solve.py)。