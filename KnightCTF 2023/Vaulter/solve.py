
concat = open('vaulter-output.enc.txt', 'r').read()

for i in range(1, 1025):
	out = []
	for ele in concat:
		out.append(chr(ord(ele) ^ i))
	bdh = ''.join(out)
	try:
		byte_data = bytes.fromhex(bdh)
		open('vaulter-input', 'wb').write(byte_data)
		break
	except:
		pass