import msgpack
from libnum import gcd
from Crypto.Util.number import long_to_bytes, bytes_to_long

def egcd(a, b):
    if a == 0:
        return (b, 0, 1)
    else:
        g, y, x = egcd(b % a, a)
        return (g, x - (b // a) * y, y)

data = open('msg.enc', 'rb').read()
data = msgpack.unpackb(data, raw=True)
r1 = bytes_to_long(data[0][0])
c1 = bytes_to_long(data[0][1])
r2 = bytes_to_long(data[1][0])
c2 = bytes_to_long(data[1][1])

msg = open('msg.txt', 'rb').read()
m1 = bytes_to_long(msg[:256])
m2 = bytes_to_long(msg[256:])

N = 23927411014020695772934916764953661641310148480977056645255098192491740356525240675906285700516357578929940114553700976167969964364149615226568689224228028461686617293534115788779955597877965044570493457567420874741357186596425753667455266870402154552439899664446413632716747644854897551940777512522044907132864905644212655387223302410896871080751768224091760934209917984213585513510597619708797688705876805464880105797829380326559399723048092175492203894468752718008631464599810632513162129223356467602508095356584405555329096159917957389834381018137378015593755767450675441331998683799788355179363368220408879117131

# p = pow(k, r, N)
p1 = c1 * pow(m1, -1, N) % N
p2 = c2 * pow(m2, -1, N) % N

_, a1, a2 = egcd(r1, r2)
a2 = -a2

# z = pow(k, r*a, N) = pow(p, a, N)
z1 = pow(p1, a1, N)
z2 = pow(p2, a2, N)

# k = pow(k, r1*a1 - r2*a2, N) 
#   = (pow(k, r1*a1, N) / pow(k, r2*a2, N)) % N
#   = pow(k, r1*a1, N) * modinv(pow(k, r2*a2, N)) % N
#   = pow(k, 1, N)
z2_inv = pow(z2, -1, N)

k = z1 * z2_inv % N
print(k)
print(r1, r2)
print(a1, a2)

def decrypt(c, k):
    out = b''
    for r_s, c_s in msgpack.unpackb(c, raw=True):
        r = bytes_to_long(r_s)
        c = bytes_to_long(c_s)
        k_inv = pow(k, -1, N)
        out += long_to_bytes(pow(k_inv, r, N) * c % N)
    return out

print(decrypt(open("flag.enc", 'rb').read(), k))
