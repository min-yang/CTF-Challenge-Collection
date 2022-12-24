import gmpy2
import libnum

prime_arr = open(r"../file/primes.py", 'r').readlines()

class RNG:
    def __init__ (self, seed, a, b, p):
        self.seed = seed
        self.a = a
        self.b = b
        self.p = p

    def gen(self):
        out = self.seed
        while True:
            out = (self.a * out + self.b) % self.p
            self.a += 1
            self.b += 1
            self.p += 1
            yield out

# 服务器每次使用的参数不一样，需要对应更新
seed = 999
a = 934
b = 75
N = 1300
ciphertext = 4902325273006503888718861346968982849810716245230499934783811569011371846131843984881577639044040849888796603266040382010712402354572190401441400976405966634103027861249055593726479526278983843571361640245392300052607631216136652146885203719828705931058885625467278107336547251772790160723387135625977005927649576161955811962719310793209186904828104027881526399013202844628689931938355432285659757375910149126124823070397402363243576811236789440671592774594002575951300730022864178827102546932232793661923642634666536601764933966910668923054641800685647447212304096429303107700020706228154505657757447064679312974316

lcg = RNG(seed, a, b, N)
gen=lcg.gen()
for i in range(3 + 5*9):
	next(gen)

def getPrime():
    prime = int(prime_arr[next(gen)].strip())
    return prime

def generate_keys():
    p = getPrime()
    q = getPrime()
    n = p*q
    g = n+1
    l = (p-1)*(q-1)
    mu = gmpy2.invert(((p-1)*(q-1)), n)
    return (n, g, l, mu)

key=generate_keys()
print(key)

def pallier_encrypt(key, msg, rand):
    n_sqr = key[0]**2
    return (pow(key[1], msg, n_sqr)*pow(rand, key[0], n_sqr) ) % n_sqr

rand = next(gen)
n_sqr = key[0] ** 2

msg = (((pow(ciphertext, key[2], n_sqr) - 1) // key[0]) * key[3]) % key[0]
print(int(msg))
print(libnum.n2s(int(msg)))