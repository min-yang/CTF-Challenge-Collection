local_478 = 0xad939ff59f6e70bcbfad406f2494993757eee98b91bc244184a377520d06fc35
local_4a8 = 103461035900816914121390101299049044413950405173712170434161686539878160984549
local_498 = 65537
print('local_478:', local_478)
print('local_498:', local_498)
print('local_4a8:', local_4a8)

# local_478 = flag ** local_498 % local_4a8

import libnum
from Crypto.Util.number import long_to_bytes

q = 282164587459512124844245113950593348271
p = 366669102002966856876605669837014229419
e = 65537
c = 0xad939ff59f6e70bcbfad406f2494993757eee98b91bc244184a377520d06fc35
n = 103461035900816914121390101299049044413950405173712170434161686539878160984549
 
d = libnum.invmod(e, (p - 1) * (q - 1))     #invmod(a, n) - 求a对于n的模逆,这里逆向加密过程中计算ψ(n)=(p-1)(q-1)，对ψ(n)保密,也就是对应根据e*d=1modψ(n),求出d
m = pow(c, d, n)   # 这里的m是十进制形式,pow(x, y[, z])--函数是计算 x 的 y 次方，如果 z 在存在，则再对结果进行取模，其结果等效于 pow(x,y) %z，对应前面解密算法中M=D(C)=（C^d）mod n
string = long_to_bytes(m)  # 获取m明文
print(string)
