
undefined8 main(void)

{
  int iVar1;
  ssize_t sVar2;
  undefined8 local_78;
  undefined8 local_70;
  undefined8 local_68;
  undefined8 local_60;
  undefined8 local_58;
  undefined8 local_50;
  undefined8 local_48;
  undefined8 local_40;
  uint local_2c;
  __off64_t local_28;
  int local_20;
  undefined4 local_1c;
  int local_18;
  int local_14;
  int local_10;
  int local_c;
  
  local_c = open("/proc/self/maps",0);
  read(local_c,maps,0x1000);
  close(local_c);
  local_10 = open("./flag.txt",0);
  if (local_10 == -1) {
    puts("flag.txt not found");
  }
  else {
    sVar2 = read(local_10,flag,0x80);
    if (0 < sVar2) {
      close(local_10);
      local_14 = dup2(1,0x539);
      local_18 = open("/dev/null",2);
      dup2(local_18,0);
      dup2(local_18,1);
      dup2(local_18,2);
      close(local_18);
      alarm(0x3c);
      dprintf(local_14,
              "This challenge is not a classical pwn\nIn order to solve it will take skills of your  own\nAn excellent primitive you get for free\nChoose an address and I will write what  I see\nBut the author is cursed or perhaps it\'s just out of spite\nFor the flag that  you seek is the thing you will write\nASLR isn\'t the challenge so I\'ll tell you what \nI\'ll give you my mappings so that you\'ll have a shot.\n"
             );
      dprintf(local_14,"%s\n\n",maps);
      while( true ) {
        dprintf(local_14,
                "Give me an address and a length just so:\n<address> <length>\nAnd I\'ll write it wh erever you want it to go.\nIf an exit is all that you desire\nSend me nothing and I  will happily expire\n"
               );
        local_78 = 0;
        local_70 = 0;
        local_68 = 0;
        local_60 = 0;
        local_58 = 0;
        local_50 = 0;
        local_48 = 0;
        local_40 = 0;
        sVar2 = read(local_14,&local_78,0x40);
        local_1c = (undefined4)sVar2;
        iVar1 = __isoc99_sscanf(&local_78,"0x%llx %u",&local_28,&local_2c);
        if ((iVar1 != 2) || (0x7f < local_2c)) break;
        local_20 = open("/proc/self/mem",2);
        lseek64(local_20,local_28,0);
        write(local_20,flag,(ulong)local_2c);
        close(local_20);
      }
                    /* WARNING: Subroutine does not return */
      exit(0);
    }
    puts("flag.txt empty");
  }
  return 1;
}

