from random import randint, seed



def is_prime(n):
    if n == 2 or n == 3:
        return True
    if n % 2 == 0 or n < 2:
        return False
    for i in range(3, int(n**0.5)+1, 2):
        if n % i == 0:
            return False
    return True


def get_prime(n):
    while True:
        p = randint(2**(n-1), 2**n)
        if is_prime(p):
            return p


def encrypt(ch, g, p, y, k):
    pt_int = int.from_bytes(ch.encode(), "big")
    c1 = pow(g, k, p)
    c2 = (pt_int * pow(y, k, p)) % p

    return c1, c2


def main():
    
    flag = open("flag.txt", "r").read().strip()
    
    x = randint(2, 9999999)
    p = get_prime(43)
    g = get_prime(43)

    while g == p:
        g = get_prime(43)

    if g > p:
        g, p = p, g

    y = pow(g, x, p)

    with open("flag.enc", "w") as out:
        for ch in list(flag):
            #   encrypt
            k = randint(2, 9999)
            c1, c2 = encrypt(ch, g, p, y, k)
            out.write(f"{c1},{c2}\n")

    with open("key.pub", "w") as out:
        out.write(f"p: {p}\n")


if __name__ == "__main__":
    seed(0)
    main()