import string
from collections import Counter

data = open('output.txt').readlines()
data = [ele.strip() for ele in data]

c = Counter(data)
print(c, len(c))

# assert all([x.isupper() or x in '{_} ' for x in MESSAGE])
d = {}
d[c.most_common(len(c))[-1][0]] = '}'
d[c.most_common(len(c))[-2][0]] = '{'
d[c.most_common(len(c))[0][0]] = ' '
d[c.most_common(len(c))[-4][0]] = '_'

idx = 0
candidates = string.ascii_uppercase
new_string = ''
for ele in data:
    if ele not in d:
        d[ele] = candidates[idx]
        idx += 1
    new_string += d[ele]
print(new_string)
