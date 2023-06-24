import functools
import math
from Crypto.Util.number import bytes_to_long, isPrime, long_to_bytes

class LCG:
    def __init__(self, lcg_s, lcg_m, lcg_c, lcg_n):
        self.state = lcg_s
        self.lcg_m = lcg_m
        self.lcg_c = lcg_c
        self.lcg_n = lcg_n

    def next(self):
        self.state = (self.state * self.lcg_m + self.lcg_c) % self.lcg_n
        return self.state

def crack_unknown_increment(states, modulus, multiplier):
    increment = (states[1] - states[0]*multiplier) % modulus
    return modulus, multiplier, increment

def crack_unknown_multiplier(states, modulus):
    multiplier = (states[2] - states[1]) * pow(states[1] - states[0], -1, modulus) % modulus
    return crack_unknown_increment(states, modulus, multiplier)

def crack_unknown_modulus(states):
    diffs = [s1 - s0 for s0, s1 in zip(states, states[1:])]
    zeroes = [t2*t0 - t1*t1 for t0, t1, t2 in zip(diffs, diffs[1:], diffs[2:])]
    modulus = abs(functools.reduce(math.gcd, zeroes))
    return crack_unknown_multiplier(states, modulus)

states = [
    2166771675595184069339107365908377157701164485820981409993925279512199123418374034275465590004848135946671454084220731645099286746251308323653144363063385,
    6729272950467625456298454678219613090467254824679318993052294587570153424935267364971827277137521929202783621553421958533761123653824135472378133765236115,
    2230396903302352921484704122705539403201050490164649102182798059926343096511158288867301614648471516723052092761312105117735046752506523136197227936190287,
    4578847787736143756850823407168519112175260092601476810539830792656568747136604250146858111418705054138266193348169239751046779010474924367072989895377792,
    7578332979479086546637469036948482551151240099803812235949997147892871097982293017256475189504447955147399405791875395450814297264039908361472603256921612,
    2550420443270381003007873520763042837493244197616666667768397146110589301602119884836605418664463550865399026934848289084292975494312467018767881691302197
]

lcg_n, lcg_m, lcg_c = crack_unknown_modulus(states)
print(f'lcg_n={lcg_n}\nlcg_m={lcg_m}\nlcg_c={lcg_c}')



# Find prime value of specified bits a specified amount of times
seed = 211286818345627549183608678726370412218029639873054513839005340650674982169404937862395980568550063504804783328450267566224937880641772833325018028629959635
lcg = LCG(seed, lcg_m, lcg_c, lcg_n)
primes_arr = []
primes_n = 1
while True:
    for i in range(8):
        while True:
            prime_candidate = lcg.next()
            if not isPrime(prime_candidate):
                continue
            elif prime_candidate.bit_length() != 512:
                continue
            else:
                primes_n *= prime_candidate
                primes_arr.append(prime_candidate)
                break
    
    # Check bit length
    if primes_n.bit_length() > 4096:
        print("bit length", primes_n.bit_length())
        primes_arr.clear()
        primes_n = 1
        continue
    else:
        break

# Create public key 'n'
n = 1
for j in primes_arr:
    n *= j
print("[+] Public Key: ", n)
print("[+] size: ", n.bit_length(), "bits")

# Calculate totient 'Phi(n)'
phi = 1
for k in primes_arr:
    phi *= (k - 1)

e = 65537
d = pow(e, -1, phi)

enc = open('flag.txt', 'rb').read()
enc = int.from_bytes(enc, 'little')

flag = pow(enc, d, n)
print(long_to_bytes(flag))