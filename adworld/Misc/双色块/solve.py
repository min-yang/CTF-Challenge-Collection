from PIL import Image
from Crypto.Util.number import long_to_bytes

data = ''
for i in range(576):
    img = Image.open('frames/frame_%s_delay-0.1s.gif' %str(i).rjust(3, '0'))
    img = img.convert('RGB')
    if img.getcolors()[0][1] != (255, 0, 255):
        data += '1'
    else:
        data += '0'

print(long_to_bytes(int(data, 2)))