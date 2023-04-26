msg = b'REDACTED'
msgb = '0' + bin(int(msg.hex(), 16))[2:]
f = open("uncertainty", "rb").read()
b = list(f)
i = 0
bitnum = 6
for a in range(len(b)):
    b[a] = (b[a] & ~(1 << bitnum)) | ((1 if msgb[i] == '1' else 0) << bitnum)
    i += 1
    if len(msgb) == i: i = 0
open("uncertainty_modified", "wb").write(bytes(b))

# The encrypted flag is cdb3d8af3ca6eec91fc8d923be61ee8a50e8283c867cad12d9b423dc924ab0cfd913b9d6cf0c6c1ba65125d5e6ba00d28715071f9843.
