import string

candidates = string.ascii_uppercase + string.ascii_lowercase + string.digits + '+/'
lines = open('stego.txt').readlines()
flag = ''
for i, line in enumerate(lines):
    line = line.strip()
    line = line.replace('=', '')
    hidden_bits = len(line) * 6 % 8
    if hidden_bits > 0:
        flag += bin(candidates.index(line[-1]))[2:].rjust(6, '0')[-hidden_bits:]
print(flag)
