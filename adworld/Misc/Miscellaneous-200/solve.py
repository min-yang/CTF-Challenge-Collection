from PIL import Image

lines = open('info.txt').readlines()

# 尝试不同的width，不断调整，直到图片可以清晰看到flag
width = 500
height = len(lines) // width
img = Image.new('RGB', (width, height), (255, 255, 255))
print(width, height)

for i in range(width):
    for j in range(height):
        img.putpixel((i, j), eval(lines[i * height + j]))

img.save('flag.png')