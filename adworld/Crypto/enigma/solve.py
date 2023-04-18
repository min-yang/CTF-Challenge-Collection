# 通过xortool发现key为\x06时，会输出有意义的文字

key = b'\x06'[0]
for i in range(1, 7):
    data = open('%se' %i, 'rb').read()
    for ele in data:
        print(chr(ele ^ key), end='')