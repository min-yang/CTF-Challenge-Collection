# 题目信息

downgrade#0778 & IcesFont#1629

Are you constantly procrastinating real work by spending too much time on ctfs? Me too! That's why I created this lit af task manager app to help us stay on top of our duties.

[Deploy your instance here](http://instancer.idek.team/challenge/task-manager)

# 解决方案

分析源码发现用户输入通过`pydash.set_`来设置，查阅[pydash文档](https://pydash.readthedocs.io/en/latest/api.html?highlight=set_#pydash.objects.set_)，发现可以通过插入`.`来设置对象的属性，即所有属性树上的值我们都可以覆盖；

接下来就是两条路，利用上一点实现任意文件访问或者任意命令执行，首先我们关注任意文件读取；

首先从源码中发现如下部分：

```python
@app.route("/<path:path>")
def render_page(path):
    if not os.path.exists("templates/" + path):
        return "not found", 404
    return render_template(path)
```

路径我们可以控制，但是当我们设置path为`../Dockerfile`时，会报错，这个时候需要分析render_template函数的验证逻辑，一步一步跟到[Jinja框架](https://github.com/pallets/jinja/blob/36b601f/src/jinja2/loaders.py#L33)的如下函数：

```python
def split_template_path(template: str) -> t.List[str]:
    """Split a path into segments and perform a sanity check.  If it detects
    '..' in the path it will raise a `TemplateNotFound` error.
    """
    pieces = []
    for piece in template.split("/"):
        if (
            os.sep in piece
            or (os.path.altsep and os.path.altsep in piece)
            or piece == os.path.pardir
        ):
            raise TemplateNotFound(template)
        elif piece and piece != ".":
            pieces.append(piece)
    return pieces
```

发现我们只需更改os.path.pardir这一属性的值即可绕过检查；

本地测试发现TaskManger对象的`get.__globals__`属性包含如下属性：

```
['__name__', '__doc__', '__package__', '__loader__', '__spec__', '__file__', '__cached__', '__builtins__', 'pydash', 'TaskManager']
```

其中[pydash](https://github.com/dgilland/pydash/tree/master/src/pydash)是个集成库，大概率会使用到os模块，一番搜索后没找到，因此我们只能从pydash依赖的其它库中去寻找，定位到helpers模块依赖的[inspect模块](https://github.com/python/cpython/blob/3.11/Lib/inspect.py)，其中导入了os模块，因此我们的属性路径为`get.__globals__.pydash.helpers.inspect.os.path.pardir`，修改后即可绕过检查，访问任意文件，我们访问`../Dockerfile`，拿到flag。

```
idek{task_complete:capture_the_flag_0f25acc159}
```

## RCE

接下来介绍如何利用`pydash.set_`实现任意命令执行，首先，我们只能设置属性值，无法进行其它操作，因此我们只能考虑程序运行期间使用的属性值。

由于每次打开网页只会运行render_template，所以我们重点关注这个函数的调用链，跟到[compile部分](https://github.com/pallets/jinja/blob/36b601f24b30a91fe2fdc857116382bcb7655466/src/jinja2/environment.py#L766)，知道网页渲染时会运行编译函数，我们需要知道source是如何构成的，好想办法修改它。

构造source部分代码用的是[generate函数](https://github.com/pallets/jinja/blob/36b601f/src/jinja2/compiler.py#L101)，然后找到可以利用的点，为[visit_Template函数](https://github.com/pallets/jinja/blob/36b601f/src/jinja2/compiler.py#L826)，关键代码如下：

```python
        from .runtime import exported, async_exported

        if self.environment.is_async:
            exported_names = sorted(exported + async_exported)
        else:
            exported_names = sorted(exported)

        self.writeline("from jinja2.runtime import " + ", ".join(exported_names))
```

其中exported的值是我们可以修改，这部分的内容编译成python代码执行，我们可以将其改成想要执行的命令，示例如下：

```python
set_("get.__globals__.pydash.helpers.inspect.sys.modules.jinja2.runtime.exported[0]", '*;import os;os.system("cp /flag* /tmp/flag") #')
```

