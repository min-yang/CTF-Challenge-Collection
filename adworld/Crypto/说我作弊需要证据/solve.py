import base64
from Crypto.Util import number

n1 = 0x53a121a11e36d7a84dde3f5d73cf
n2 = 0x99122e61dc7bede74711185598c7
e = 0x10001
print(n1, n2, e)

p1 = 38456719616722997
q1 = 44106885765559411
phi = (p1 - 1) * (q1 - 1)
d1 = pow(e, -1, phi)
print(d1)

p2 = 49662237675630289
q2 = 62515288803124247
phi = (p2 - 1) * (q2 - 1)
d2 = pow(e, -1, phi)

# tshark -r Basic-06.pcapng -Tfields -e data.data 'data.len>1' | xxd -r -p | base64 -d
info_list = open('info.txt').read().split(';')
enc_list = []
sig_list = []
flag = {}
print(len(info_list))
for i in range(0, len(info_list)-1, 3):
    seq = int(info_list[i].split('=')[1])
    data = int(info_list[i+1].split('=')[1][3:-1], 16)
    sig = int(info_list[i+2].split('=')[1][3:-1], 16)
    plaintext = pow(data, d2, n2)
    sig_ = pow(plaintext, d1, n1)
    if(sig == sig_):
        flag[seq] = number.long_to_bytes(plaintext).decode()

idx = 0
while True:
    if idx in flag:
        print(flag[idx], end='')
    else:
        break
    idx += 1



