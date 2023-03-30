## 方案1

```sh
base64 rootkit.ko > rootkit.ko.b64
sed 's/JgBQDyDADQAAAQAPIsBYXcMAVaE0oF\/BieXHBQAAAAAgoF\/BowAAAAChvKRfwaMAAAAAoWyhX8Gj/JgBQDyDADQAAAQAPIsBYXcMAVbhwjRXBieXHBQAAAAAgoF\/BowAAAAChvKRfwaMAAAAAoWyhX8Gj/g' -i rootkit.ko.b64
sed 's/AAAABAAAAMcFCAAAAAQAAABdw1WJ5V3DZmxhZwBOb3Qgc28gZmFzdCA6KQoAWW91IHdpbGwgbm90/AAAABAAAAMcFCAAAAAQAAABdw1WJ5V3DZ2FsZgBOb3Qgc28gZmFzdCA6KQoAWW91IHdpbGwgbm90/g' -i rootkit.ko.b64
sed 's/IHNlZSB0aGUgZmxhZy4uLgoAAAAABwAAAFcAAACnAAAABwEAAFcBAACnAQAA9wEAAFcCAACjAgAA/IHNlZSB0aGUgZ2FsZi4uLgoAAAAABwAAAFcAAACnAAAABwEAAFcBAACnAQAA9wEAAFcCAACjAgAA/g' -i rootkit.ko.b64
sed 's/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHJvb3RraXQAAAAAAAAAAAAAAAAAAAAA/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHRpa3Rvb3IAAAAAAAAAAAAAAAAAAAAA/g' -i rootkit.ko.b64
base64 -d rootkit.ko.b64 > solve.ko
insmod solve.ko
```

## 方案2

```sh
cat /dev/ram0 | grep -i "root"
```

## 方案3

```sh
mount --bind ./flag ./f.tar.gz
```

# 方案4

```sh
# by vakzz
# sys_open in table entry 0xC15FA034 (from rootkit.ko)

# find real sys_open call
cat /proc/kallsyms |grep sys_open
# c1158d70 T sys_open

cp rootkit.ko sootkit.ko

# call sys_open directly instead of via table
ORIG=`printf "\xA1\x34\xA0\x5F\xC1"`
REPLACE=`printf "\xB8\x70\x8D\x15\xC1"`

sed -i "s/$ORIG/$REPLACE/g" sootkit.ko

#replace module name
sed -i 's/rootkit/sootkit/g' sootkit.ko

#replace flag string
sed -i 's/flag/aaaa/g' sootkit.ko
insmod sootkit.ko
tar xzO < flag
```