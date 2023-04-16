from flask import Flask, url_for, render_template, request
from ast import literal_eval
import sympy

app = Flask(__name__)

@app.route("/")
def index():
    return render_template('index.html')


regular_operators = {
    'add': lambda x, y: x + y,
    'sub': lambda x, y: x - y,
    'mul': lambda x, y: x * y,
    'div': lambda x, y: x / y,
    'pow': lambda x, y: x ** y,
}

trig_operators = {
    'sin': sympy.sin,
    'cos': sympy.cos,
    'tan': sympy.tan,
    'cot': sympy.cot,
    'sec': sympy.sec,
    'csc': sympy.csc,
    'asin': sympy.asin,
    'acos': sympy.acos,
    'atan': sympy.atan,
    'acot': sympy.acot,
    'asec': sympy.asec,
    'acsc': sympy.acsc,
}


def postfix_calculator(inp):
    stack = []
    for (ty, val) in inp:
        if ty == 'num':
            stack.append(literal_eval(val))
        elif ty == 'var':
            stack.append(sympy.Symbol(val))
        elif ty == 'op':
            if val in regular_operators:
                a = stack.pop()
                b = stack.pop()
                stack.append(regular_operators[val](b, a))
            elif val in trig_operators:
                a = stack.pop()
                stack.append(trig_operators[val](a))
            else:
                raise ValueError("Invalid operator")
    return stack


@app.post("/compute")
def compute():
    try:
        expr = postfix_calculator(request.get_json())
        if len(expr) == 1:
            return sympy.latex(expr[0]) + r'\\=\\' + sympy.latex(sympy.simplify(expr[0]))
        else:
            return r'\quad{}'.join(map(sympy.latex, expr)) + r'\\=\\\cdots'
    except Exception as e:
        return "invalid expression"
