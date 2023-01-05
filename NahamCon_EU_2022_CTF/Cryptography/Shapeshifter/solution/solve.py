from Crypto.Util.number import bytes_to_long as b2l
from Crypto.Util.number import long_to_bytes as l2b

OUTPUT = open('../file/output.txt', 'r').read().split()

class LFSR():
    def __init__(self, iv):
        self.state = [int(c) for c in iv]
        #self.state = self.iv

    def shift(self):
        s = self.state
        newbit = s[15] ^ s[13] ^ s[12] ^ s[10] # ^ s[0]
        s.pop()
        self.state = [newbit] + s

    def unshift(self):
        s = self.state
        newbit = s[0]
        oldbit = newbit ^ s[14] ^ s[13] ^ s[11]
        self.state = s[1:] + [oldbit]

for output in OUTPUT:
    lfsr = LFSR(output)
    for _ in range(31337):
        lfsr.unshift()
    finalstate = ''.join([str(c) for c in lfsr.state])
    print(l2b(int(finalstate, 2)).decode(), end='')
