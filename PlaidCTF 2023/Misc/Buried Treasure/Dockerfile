# Build nsjail from source
FROM ubuntu:22.04 as nsjail-builder

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update && apt-get install -y \
autoconf \
bison \
flex \
gcc \
g++ \
git \
libprotobuf-dev \
libnl-route-3-dev \
libtool \
make \
pkg-config \
protobuf-compiler \
uidmap

RUN git clone https://github.com/google/nsjail --branch 3.3 && \
cd nsjail && \
make -j8 && \
mv nsjail /bin && \
cd / && \
rm -rf nsjail

# Now build the actual container, complete with Ghidra
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

RUN mkdir /chall
WORKDIR /chall

RUN pip3 install capstone requests iced-x86 ecdsa

COPY entrypoint.sh .
COPY wrapper.sh .
COPY runner.c .
COPY runner.S .

RUN wget https://github.com/google/kctf/raw/v1/docker-images/challenge/pow.py && chmod +x pow.py

RUN gcc -o runner -Wl,-z,relro,-z,noexecstack runner.c runner.S

# Grab the nsjail binary from the builder
COPY --from=nsjail-builder /usr/bin/nsjail /usr/bin/

# Setup user for nsjail
RUN useradd -m ctf && \
mkdir /chroot/ && \
chown root:ctf /chroot && \
chown root:ctf /home/ctf && \
chmod 770 /chroot

COPY jail.cfg .
COPY chall.py .

EXPOSE 1337
CMD ["./entrypoint.sh"]
