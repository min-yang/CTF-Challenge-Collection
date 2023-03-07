import string

just_a_string = [115, 116, 114, 97, 110, 103, 101, 95, 115, 116, 114, 105, 110, 103, 95, 105, 116, 95, 105, 115]
difference = [0, 9, -9, -1, 13, -13, -4, -11, -9, -1, -7, 6, -13, 13, 3, 9, -13, -11, 6, -7]

key = ''
for ele in just_a_string:
    key += chr(ele)
print(key)

for first_letter in string.ascii_lowercase:
    key = ''
    for i, ele in enumerate(just_a_string):
        key += chr((ord(first_letter) + difference[i]) ^ 0)
    print(key)