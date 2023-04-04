from base64 import b64decode
from string import ascii_uppercase, ascii_lowercase, digits
from Crypto.Util.number import long_to_bytes

def solve():
    with open('1a351e90fb2b476a929d1e2666d7c511','r') as f:
        codes=f.read()
    Lc=codes.split('\n')[:-1]
    base=ascii_uppercase+ascii_lowercase+digits+'+/'
    re2=[]
    for code in Lc:
        if '==' in code:
            re2.append(bin(base.find(code[-3]))[2:].rjust(6,'0')[2:])
        elif '=' in code:
            re2.append(bin(base.find(code[-2]))[2:].rjust(6,'0')[4:])
    ret=''.join(re2)
    print(ret)
    return long_to_bytes(int(ret[:ret.rfind('1')+1],2))

if __name__=='__main__':
    print(solve())