from PIL import Image

orig = Image.open('orig.png')
steg = Image.open('stego100.png')
new = Image.new('RGBA', size=orig.size)

for i in range(orig.width):
    for j in range(orig.height):
        v1 = orig.getpixel((i, j))
        v2 = steg.getpixel((i, j))
        if v1 == v2:
            pass
        else:
            new.putpixel((i, j), (255, 255, 255))
new.save('qrcode.png')