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

RUN mkdir /chall && mkdir /chall/scripts && mkdir /binja
WORKDIR /chall

RUN pip3 install flask requests

# Now install binary ninja
WORKDIR /binja

RUN wget https://github.com/Vector35/binaryninja-api/raw/dev/scripts/download_headless.py
ARG BN_LICENSE_SERIAL=0
RUN python3 download_headless.py --serial $BN_LICENSE_SERIAL
RUN unzip *.zip
RUN python3 binaryninja/scripts/install_api.py

WORKDIR /chall

COPY entrypoint.sh .
COPY decompile_binja.py scripts/
COPY empty_elf.S .
COPY linker.lds .

RUN gcc -c empty_elf.S && ld --no-gc-sections -o empty_elf -T linker.lds empty_elf.o

EXPOSE 5001
CMD ["./entrypoint.sh"]
