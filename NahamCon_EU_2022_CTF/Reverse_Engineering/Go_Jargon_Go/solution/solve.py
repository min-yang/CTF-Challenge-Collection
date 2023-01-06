
words = open('../file/go-jargon-go.txt').read().split()

mapping = {}
for i in range(len(words)):
    if len(set(words[i:i+256])) == 256:
        print(i)
        for j, word in enumerate(words[i:i+256]):
            mapping[word] = j.to_bytes(1, 'big')
        break

with open('jargon.bin', 'wb') as fw:
    for word in words:
        fw.write(mapping[word])
