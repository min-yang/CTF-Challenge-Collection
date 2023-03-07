import string
import hashlib

md5s = [
    174282896860968005525213562254350376167, 137092044126081477479435678296496849608,
    126300127609096051658061491018211963916, 314989972419727999226545215739316729360,
    256525866025901597224592941642385934114, 115141138810151571209618282728408211053,
    8705973470942652577929336993839061582, 256697681645515528548061291580728800189,
    39818552652170274340851144295913091599, 65313561977812018046200997898904313350,
    230909080238053318105407334248228870753, 196125799557195268866757688147870815374,
    74874145132345503095307276614727915885
]

md5_list = []
for md5 in md5s:
    md5_list.append(md5.to_bytes(16, 'big').hex())
print(md5_list)

candidates = string.ascii_letters + string.digits
def work1():
    for i in candidates:
        for j in candidates:
            tmp = 'TF{' + i + j
            if hashlib.md5(tmp.encode()).hexdigest() == md5_list[1]:
                print(tmp)
                return

def work2():
    for i in candidates:
        for j in candidates:
            for k in candidates:
                for l in candidates:
                    tmp = i + j + k + l + '}'
                    if hashlib.md5(tmp.encode()).hexdigest() == md5_list[-1]:
                        print(tmp)
                        return

# work1()
# work2()

# 在crackstation上查询MD5，也可以通过john ripple爆破
flag = 'ALEXC' + 'TF{dv' + '5d4s2' + 'vj8nk' + '43s8d' + '8l6m1' + 'n5l67' + 'ds9v4' + '1n52n' + 'v37j4' + '81h3d' + '28n4b' + '6v3k}'
print(flag)