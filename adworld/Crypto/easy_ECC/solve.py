Gx = 6478678675
Gy = 5636379357093
a = 16546484
b = 4548674875
p = 15424654874903
k = 546768
x = Gx
y = Gy
for i in range(k - 1):
    if (x == Gx and y == Gy):
        inv = pow(2 * Gy, p - 2, p)
        temp = (3 * Gx * Gx + a) * inv % p
    else:
        inv = pow((x - Gx), p - 2, p)
        temp = (y - Gy) * inv % p
    #print(temp)
    xr = (temp * temp - Gx - x) % p
    yr = (temp * (x - xr) - y) % p
    #print(i, xr, yr)
    x = xr
    y = yr
print(x + y)