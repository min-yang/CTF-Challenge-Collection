from pwn import *

def get_sig():
    p = remote('61.147.171.105', '50158')
    if os.path.exists('sigs.txt'):
        sig_set = eval(open('sigs.txt').read())
    else:
        sig_set = set()
    while True:
        p.sendlineafter(b'signed:', b'2')
        p.recvuntil(b'signature:')
        sig_set.add(int(p.recvuntil(b',', drop=True)))
        p.recvuntil(b'N:')
        p.recvuntil(b'\n', drop=True)
        p.sendlineafter(b'(yes, no):', b'yes')
        if len(sig_set) > 1025:
            break
    open('sigs.txt', 'w').write(repr(sig_set))

def get_good_sig(sig_set):
    for sig in sig_set:
        if pow(sig, e, N) == 2:
            print('good sig: %s' %sig)
            return sig

get_sig()

sig_set = eval(open('sigs.txt').read())

N = 172794691472052891606123026873804908828041669691609575879218839103312725575539274510146072314972595103514205266417760425399021924101213043476074946787797027000946594352073829975780001500365774553488470967261307428366461433441594196630494834260653022238045540839300190444686046016894356383749066966416917513737
e = 0x10001
# good_sig = get_good_sig(sig_set)
good_sig = 22611972523744021864587913335128267927131958989869436027132656215690137049354670157725347739806657939727131080334523442608301044203758495053729468914668456929675330095440863887793747492226635650004672037267053895026217814873840360359669071507380945368109861731705751166864109227011643600107409036145468092331

k_dict = {}
for k in range(1024):
    k_dict[pow(2, pow(2, k, N), N)] = k

d = {}
for sig in sig_set:
    if pow(sig, e, N) == 2:
        continue
    
    tmp = (sig * pow(good_sig, -1, N)) % N
    if tmp in k_dict:
        d[k_dict[tmp]] = 0
        continue

    tmp = (good_sig * pow(sig, -1, N)) % N
    if tmp in k_dict:
        d[k_dict[tmp]] = 1
        continue

print(d, len(d))

d_bin = ''
for i in range(1024):
    if i not in d:
        d_bin = d_bin + '?'
    else:
        d_bin = d_bin + str(d[i])
print(d_bin)
