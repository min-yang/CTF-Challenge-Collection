import time
from pwn import *

def solve(data):
	t0 = time.time()
	data = data.split()
	n = int(len(data) ** 0.5)
	print('矩阵大小：%s' %n)

	total_move = 0
	for idx in range(n):
		ele = int(data[idx*n + idx])

		target_row = int((ele-1) / n)
		target_column = int((ele-1) % n)

		current_row = idx
		current_column = idx
		
		row_move = target_row - current_row
		column_move = target_column - current_column

		total_move += min(abs(row_move), n-abs(row_move))
		total_move += min(abs(column_move), n-abs(column_move))

	print('耗时: %s' %(time.time() - t0))
	return total_move

if __name__ == '__main__':
	s = remote('challs.htsp.ro', 14001)

	for i in range(15):
		s.recvuntil(b'15!')
		s.recvline()
		s.recvline()
		data = s.recvuntil(b'Ans', drop=True)
		total_move = str(solve(data.decode())) + '\n'
		s.recvuntil(b'= ')
		s.send(total_move.encode())

	s.interactive()