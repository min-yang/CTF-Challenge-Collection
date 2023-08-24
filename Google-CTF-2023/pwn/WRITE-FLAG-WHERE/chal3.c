int __cdecl main(int argc, const char **argv, const char **envp)
{
  __int64 buf[9]; // [rsp+0h] [rbp-70h] BYREF
  _DWORD n[3]; // [rsp+4Ch] [rbp-24h] BYREF
  int v6; // [rsp+58h] [rbp-18h]
  int v7; // [rsp+5Ch] [rbp-14h]
  int v8; // [rsp+60h] [rbp-10h]
  int v9; // [rsp+64h] [rbp-Ch]
  int v10; // [rsp+68h] [rbp-8h]
  int fd; // [rsp+6Ch] [rbp-4h]

  fd = open("/proc/self/maps", 0, envp);
  read(fd, maps, 0x1000uLL);
  close(fd);
  v10 = open("./flag.txt", 0);
  if ( v10 == -1 )
  {
    puts("flag.txt not found");
    return 1;
  }
  else
  {
    if ( read(v10, &flag, 0x80uLL) > 0 )
    {
      close(v10);
      v9 = dup2(1, 1337);
      v8 = open("/dev/null", 2);
      dup2(v8, 0);
      dup2(v8, 1);
      dup2(v8, 2);
      close(v8);
      alarm(0x3Cu);
      dprintf(
        v9,
        "Your skills are considerable, I'm sure you'll agree\n"
        "But this final level's toughness fills me with glee\n"
        "No writes to my binary, this I require\n"
        "For otherwise I will surely expire\n");
      dprintf(v9, "%s\n\n", maps);
      while ( 1 )
      {
        memset(buf, 0, 64);
        v7 = read(v9, buf, 0x40uLL);
        if ( (unsigned int)__isoc99_sscanf(buf, "0x%llx %u", &n[1], n) != 2
          || n[0] > 0x7Fu
          || *(_QWORD *)&n[1] >= (unsigned __int64)main - 20480 && (unsigned __int64)main + 20480 >= *(_QWORD *)&n[1] )
        {
          break;
        }
        v6 = open("/proc/self/mem", 2);
        lseek64(v6, *(__off64_t *)&n[1], 0);
        write(v6, &flag, n[0]);
        close(v6);
      }
      exit(0);
    }
    puts("flag.txt empty");
    return 1;
  }
}