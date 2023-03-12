Ever wanted to hack a tiny OS written in x86-32 assembly and C--? Me neither but it’s hxp CTF 2022.

Give us an URL, the user in the KolibriOS VM will visit it. You need to get the flag from /hd0/1/flag.txt

The source code you could get from https://repo.or.cz/kolibrios.git/tree/7fc85957a89671d27f48181d15e386cd83ee7f1a

The browser is at programs/cmm/browser in the source tree. It relies on a couple of different libraries (e.g. programs/develop/libraries), grep around.

KolibriOS has its own debugger, DEBUG, available on the desktop. It may come in useful.

The kernel ABI is at kernel/trunk/docs/sysfuncs.txt

For building random pieces:

```
INCLUDE=path_to_header.inc fasm -m 1000000 -s debug.s file.asm file.out
```