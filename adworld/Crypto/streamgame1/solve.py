'''
R=int(flag[5:-1],2)
mask    =   0b1010011000100011100

f=open("key","ab")   #以二进制追加模式打开
for i in range(12):
    tmp=0
    for j in range(8):
        (R,out)=lfsr(R,mask)
        tmp=(tmp << 1)^out   #按位异或运算符：当两对应的二进位相异时，结果为1
    f.write(chr(tmp))  #chr() 用一个范围在 range（256）内的（就是0～255）整数作参数，返回一个对应的字符。
f.close()
'''

def lfsr(R,mask):
    output = (R << 1) & 0xffffff    #将R向左移动1位，bin(0xffffff)='0b111111111111111111111111'=0xffffff的二进制补码
    i=(R&mask)&0xffffff             #按位与运算符&：参与运算的两个值,如果两个相应位都为1,则该位的结果为1,否则为0
    lastbit=0
    while i!=0:
        lastbit^=(i&1)    #按位异或运算符：当两对应的二进位相异时，结果为1
        i=i>>1
    output^=lastbit
    return (output,lastbit)

mask = 0b1010011000100011100
key = open('key', 'rb').read()
for R in range(2 ** 19):
    orig_R = R
    hit = True
    for i in range(12):
        tmp = 0
        for j in range(8):
            (R, out) = lfsr(R, mask)
            tmp = (tmp << 1) ^ out
        if tmp != key[i]:
            hit = False
            break
    if hit:
        print(bin(orig_R))
        break

