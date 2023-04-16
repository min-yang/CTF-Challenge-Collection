#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <asm/prctl.h>
#include <sys/prctl.h>
#include <sys/mman.h>
#include <string.h>

#define CODE_LIMIT 0x1000
#define JIT_BASE (void*)0x13371337000
#define JIT_SIZE 0x2000

#define STACK_BASE (void*)0x12341234000
#define STACK_SIZE 0x2000

void jit_start();
void enter_jit();

int clear_state() {
    if (arch_prctl(ARCH_SET_FS, 0)) exit(1);
    if (arch_prctl(ARCH_SET_GS, 0)) exit(1);
}

int main() {
    mmap(JIT_BASE, JIT_SIZE, PROT_READ | PROT_WRITE, MAP_PRIVATE | MAP_ANONYMOUS | MAP_FIXED, -1, 0);
    int code_size = fread(JIT_BASE, 1, CODE_LIMIT, stdin);

    if (strcmp(getenv("LEVEL"), "1") == 0) {
        void *r = mmap(STACK_BASE, STACK_SIZE, PROT_READ | PROT_WRITE, MAP_PRIVATE | MAP_ANONYMOUS | MAP_FIXED, -1, 0);
        if (!r) exit(1);
    }
    enter_jit();
}

void enter_jit() {
    /* Mark JIT region executable, not writable */
    mprotect(JIT_BASE, JIT_SIZE, PROT_READ | PROT_EXEC);

    printf("Entering jit...\n");

    /* Reset some state */
    clear_state();

    /* go! */
    jit_start();
}
