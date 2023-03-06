middle = b'xIrCj~<r|2tWsv3PtI\x7fzndka'
middle2 = ''
for ele in middle:
    middle2 += chr((ele ^ 6) - 1)
print(middle2)

middle3 = ''
idx = len(middle2) - 1
while idx>=0:
    middle3 += middle2[idx]
    idx -= 1
print(middle3)