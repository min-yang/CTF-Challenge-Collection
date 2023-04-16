#!/usr/bin/python3

from binaryninja import core_set_license, core_product_type, open_view
import sys, os, json

# Flask app decompiles sequences of assembly
from flask import Flask, request, jsonify

core_set_license(os.getenv("BN_LICENSE"))

app = Flask(__name__)

@app.route("/")
def hello_world():
    try:
        c = request.args.get('c')
        c = bytes.fromhex(c)[:0x200].ljust(0x200, b"\x90")

        with open_view("/chall/empty_elf") as bv:
            fn = bv.get_functions_at(0x13371337000)[0]
            base = fn.lowest_address
            bv.write(base, c)
            bv.update_analysis_and_wait()
            source = '\n'.join(map(str, fn.hlil.root.lines))
            if "undefined\n" in source or 'unimplemented' in source:
                return jsonify({"ok": False, "code": source})

        return jsonify({"ok": True, "code": source})
    except Exception as e:
        print(e)
        return jsonify({"ok": False, "exc": str(e)})

app.run(host="0.0.0.0", port=5001)

