from PIL import Image

img = Image.open('low.bmp')
target = img.copy()

for i in range(img.width):
    for j in range(img.height):
        if img.getpixel((i, j)) & 1 == 0:
            target.putpixel((i, j), 0)
        else:
            target.putpixel((i, j), 255)

target.save('flag.bmp')
