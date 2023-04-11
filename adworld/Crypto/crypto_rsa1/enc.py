from Crypto.Util.number import getPrime, isPrime, getRandomNBitInteger, bytes_to_long, long_to_bytes
from gmpy2 import powmod,invert,gcd
from flag import flag
import sympy

q = getPrime(1024)
p = sympy.nextprime(q)
N = p * q 
e = 0x10001
flag = flag.ljust(80)
m = bytes_to_long(flag)
c = pow(m,e,N)

print('N = ',N)
print('e = ',e)
print('c = ',c)

'''
N =  12194420073815392880989031611545296854145241675320130314821394843436947373331080911787176737202940676809674543138807024739454432089096794532016797246441325729856528664071322968428804098069997196490382286126389331179054971927655320978298979794245379000336635795490242027519669217784433367021578247340154647762800402140321022659272383087544476178802025951768015423972182045405466448431557625201012332239774962902750073900383993300146193300485117217319794356652729502100167668439007925004769118070105324664379141623816256895933959211381114172778535296409639317535751005960540737044457986793503218555306862743329296169569
e =  65537
c =  4504811333111877209539001665516391567038109992884271089537302226304395434343112574404626060854962818378560852067621253927330725244984869198505556722509058098660083054715146670767687120587049288861063202617507262871279819211231233198070574538845161629806932541832207041112786336441975087351873537350203469642198999219863581040927505152110051313011073115724502567261524181865883874517555848163026240201856207626237859665607255740790404039098444452158216907752375078054615802613066229766343714317550472079224694798552886759103668349270682843916307652213810947814618810706997339302734827571635179684652559512873381672063
'''