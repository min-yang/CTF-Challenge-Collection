import os

# 在shell中设置“ulimit -f 0”后执行该脚本，该脚本会开一个子进程，直接运行otp会失败
os.system('./otp 0')