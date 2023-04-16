# From https://github.com/decompiler-explorer/decompiler-explorer/blob/master/runners/decompiler/Dockerfile
FROM ubuntu:22.04
USER root
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    openjdk-17-jdk \
    unzip \
    wget \
    build-essential \
    socat \
    libprotobuf-dev \
    libnl-route-3-dev \
    python3 \
    python3-pip \
    python3-dev \
    cgroup-tools \
    && rm -rf /var/lib/apt/lists/*

RUN wget https://github.com/NationalSecurityAgency/ghidra/releases/download/Ghidra_10.2.2_build/ghidra_10.2.2_PUBLIC_20221115.zip -O ghidra.zip \
    && unzip ghidra.zip \
    && rm ghidra.zip \
    && mv ghidra_* ghidra

RUN mkdir /chall && mkdir /chall/scripts && mkdir /chall/workspace

WORKDIR /chall

# Install python3 bridge for ghidra

RUN wget https://services.gradle.org/distributions/gradle-7.6.1-bin.zip -P /tmp \
    && unzip -d /opt/gradle /tmp/gradle-7.6.1-bin.zip

RUN export GRADLE_HOME=/opt/gradle/gradle-7.6.1/ \
    && export PATH=$PATH:/opt/gradle/gradle-7.6.1/bin \
    && wget https://github.com/mandiant/Ghidrathon/archive/refs/tags/v2.0.1.zip \
    && unzip v2.0.1.zip \
    && cd Ghidrathon-2.0.1 \
    && gradle -PGHIDRA_INSTALL_DIR=/ghidra/ -PPYTHON_BIN=/usr/bin/python3 \
    && unzip -d /ghidra/Ghidra/Extensions/ dist/ghidra_10.2.2_PUBLIC_*_Ghidrathon-2.0.1.zip

RUN pip3 install flask

COPY entrypoint.sh .
COPY decompile_ghidra.py scripts/
COPY linker.lds .
COPY empty_elf.S .

RUN gcc -c empty_elf.S && ld --no-gc-sections -o empty_elf -T linker.lds empty_elf.o

EXPOSE 5000
CMD ["./entrypoint.sh"]
