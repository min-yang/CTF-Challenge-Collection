from PIL import Image

im = Image.open('9266eadf353d4ada94ededaeb96d0c50.gif')
w = im.width * im.n_frames
h = im.height
out = Image.new('RGBA', (w, h))

for i in range(im.n_frames):
    im.seek(i)
    im.save('%s.png' %i)
    out.paste(im, (i * im.width, 0))
out.save('out.png')
