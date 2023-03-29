#include <stdio.h>

int main(){
        unsigned int random;
        random = rand();        // random value! 没有设置随机种子，每次生成的值都是一样的

        unsigned int key=0;
        scanf("%d", &key);

        if( (key ^ random) == 0xdeadbeef ){
                printf("Good!\n");
                system("/bin/cat flag");
                return 0;
        }

        printf("Wrong, maybe you should try 2^32 cases.\n");
        return 0;
}