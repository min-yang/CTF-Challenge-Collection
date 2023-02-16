from pwn import *

context.log_level = 'DEBUG'
context.binary = ELF('../file/hacknote_patch')

p = process('../file/hacknote_patch')
libc = ELF('/mnt/d/security/glibc-all-in-one/libs/2.23-0ubuntu11.3_i386/libc-2.23.so')

