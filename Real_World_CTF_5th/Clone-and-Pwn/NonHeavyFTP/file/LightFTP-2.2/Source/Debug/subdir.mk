################################################################################
# Automatically-generated file. Do not edit!
################################################################################

# Add inputs and outputs from these tool invocations to the build variables 
C_SRCS += \
../cfgparse.c \
../fspathtools.c \
../ftpconst.c \
../ftpserv.c \
../main.c \
../x_malloc.c 

OBJS += \
./cfgparse.o \
./fspathtools.o \
./ftpconst.o \
./ftpserv.o \
./main.o \
./x_malloc.o 

C_DEPS += \
./cfgparse.d \
./fspathtools.d \
./ftpconst.d \
./ftpserv.d \
./main.d \
./x_malloc.d 


# Each subdirectory must supply rules for building sources it contributes
%.o: ../%.c
	@echo 'Building file: $<'
	@echo 'Invoking: GCC C Compiler'
	$(CC) $(CFLAGS) -std=c99 -D_GNU_SOURCE -D_LARGEFILE64_SOURCE -D_FILE_OFFSET_BITS=64 -O0 -g3 -pedantic -Wall -Wextra -Wno-unused-parameter -c -fmessage-length=0 -fno-ident  -D_GNU_SOURCE -v -pthread -MMD -MP -MF"$(@:%.o=%.d)" -MT"$(@)" -o "$@" "$<"
	@echo 'Finished building: $<'
	@echo ' '


