import hashlib

for i in range(100000000):
    if hashlib.md5(str(i).encode()).hexdigest()[-6:] == '8b184b':
        print('b = %s' %i)
        break

# a=1e9&b=53724&c={"m":"2023a","n":[[0],0]}