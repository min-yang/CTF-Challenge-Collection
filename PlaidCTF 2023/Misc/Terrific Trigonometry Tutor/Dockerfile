FROM ubuntu:22.04

# Grab dependencies
RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
        python3 \
        python3-pip \
        curl
RUN pip install flask gunicorn sympy

# Grab things from the dir, and set up a fake flag if there isn't one already
COPY . /app
WORKDIR /app
RUN if [ -f flag ]; then true; else echo FAKEFLAG > flag; fi

# Run the server
CMD gunicorn --workers 4 --reuse-port --bind '0.0.0.0:1337' 'trig:app'
