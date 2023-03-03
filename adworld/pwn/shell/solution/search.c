# include <stdio.h>
# include <string.h>

int main(){
    FILE *f = fopen("../file/ld-linux-x86-64.so.2", "r");
    char *buf = NULL;
    size_t n;
    if (f == NULL){
        printf("error\n");
    }

    while (getline(&buf, &n, f) != -1) {
        char* user = strtok(buf, ":");
        char* password = strtok(0LL, ":");
        printf("%s:%s\n", user, password);
    }
    fclose(f);
    return 0;
}