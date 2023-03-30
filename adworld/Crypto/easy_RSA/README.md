## 描述

在一次RSA密钥对生成中，假设p=473398607161，q=4511491，e=17

求解出d

## 解决方案

要求：python >= 3.8
```python
p = 473398607161
q = 4511491
e = 17
phi = (p-1) * (q-1)
print('cyberpeace{%s}' %pow(e, -1, phi))
```