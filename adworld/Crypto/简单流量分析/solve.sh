 tshark -r fetus_pcap.pcap -Tfields -e data.len 'ip.src==192.168.3.73' | xargs printf '%x' | xxd -r
-p | base64 -d