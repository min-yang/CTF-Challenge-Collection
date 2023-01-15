import json

import requests

r = requests.post('http://172.18.211.1:1337/just-read-it', data=json.dumps({'Orders': [21]}).encode())
print(r.content)