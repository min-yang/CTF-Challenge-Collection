from pwn import *

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

fn_content = open(r"../file/firstnames.py", 'r').readlines()
ln_content = open(r"../file/lastnames.py", 'r').readlines()

fn_content = [ele.strip() for ele in fn_content]
ln_content = [ele.strip() for ele in ln_content]

s = remote('34.90.236.228', 1337)
s.recvline()
firstname, lastname, _, firstname2, lastname2 = s.recvline().decode().split()
a = fn_content.index(firstname)
b = ln_content.index(lastname)
N = 1300
random_1 = fn_content.index(firstname2)
random_2 = ln_content.index(lastname2)

for i in range(N):
    lcg = RNG(i, a, b, N)
    gen=lcg.gen()
    if next(gen) == random_1 and next(gen) == random_2:
        print(i)
        seed = i

print('随机数生成器参数：', seed, a, b, N)
lcg = RNG(seed, a, b, N)
gen = lcg.gen()

for i in range(2):
    next(gen)

winner = str(next(gen) % 2) + '\n'
s.send(winner.encode())

for i in range(9):
    s.recvline()
    s.recvline()
    s.recvline()

    for j in range(4):
        next(gen)

    winner = str(next(gen) % 2) + '\n'
    s.send(winner.encode())

s.interactive()

'''
某次运行的参数（每次运行参数是不一样的，这些参数需要放到solve2.py中）：
seed = 999
a = 934
b = 75
N = 1300
Can you decode this secret message?
4902325273006503888718861346968982849810716245230499934783811569011371846131843984881577639044040849888796603266040382010712402354572190401441400976405966634103027861249055593726479526278983843571361640245392300052607631216136652146885203719828705931058885625467278107336547251772790160723387135625977005927649576161955811962719310793209186904828104027881526399013202844628689931938355432285659757375910149126124823070397402363243576811236789440671592774594002575951300730022864178827102546932232793661923642634666536601764933966910668923054641800685647447212304096429303107700020706228154505657757447064679312974316
'''