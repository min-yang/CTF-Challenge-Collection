import string

data_map = string.digits + string.ascii_lowercase + string.ascii_uppercase

target = 'KanXueCTF2019JustForhappy'
middle = 'abcdefghiABCDEFGHIJKLMNjklmn0123456789opqrstuvwxyzOPQRSTUVWXYZ'
password_idx = []

for i, ele in enumerate(target):
    idx = middle.index(ele)
    password_idx.append(idx)
print(password_idx)

password = ''
for idx in password_idx:
    password += data_map[idx]
print(password)