import string

ciphertext = open('../file/Important_encrypted', 'rb').read()

add_spice = lambda b: 0xff & ((b << 1) | (b >> 7))
mid = bytearray(add_spice(ord(c)) for c in 'HiDeteXT')

passwords = []
for i in range(len(ciphertext) - 7):
	password = ''
	for j in range(8):
		password += chr(ciphertext[i+j] ^ mid[j])

	hit = True
	for ele in password:
		if ele not in string.printable:
			hit = False
			break

	if hit:
		print(password)
		passwords.append(password)

for ele in passwords:
	for i in range(len(ele)):
		password = ele[i:] + ele[:i]
		remove_spice = lambda b: 0xff & ((b >> 1) | (b << 7))
		plaintext = bytearray(remove_spice(c ^ ord(password[i % len(password)])) for i, c in enumerate(ciphertext))
		if b'NITE' in plaintext:
			print(plaintext)
