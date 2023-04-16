from ghidra.program.model.address import Address, AddressSet
from ghidra.program.model.symbol import SourceType
from ghidra.app.decompiler import DecompInterface
from ghidra.util.task import ConsoleTaskMonitor
from ghidra.program.flatapi import FlatProgramAPI
import sys

program = getState().getCurrentProgram()
flat = FlatProgramAPI(program)
st = program.getSymbolTable()
main = list(st.getSymbols("main"))[0].getAddress()
print(f"main at {hex(main.getUnsignedOffset())}")

flat.clearListing(main, main.add(0x200))
flat.removeFunctionAt(main)

program.getMemory().setBytes(main, bytes([0xc3]))

main_range = AddressSet(main, main.add(0x200))
program.getFunctionManager().createFunction("main", main, main_range, SourceType.USER_DEFINED)

ifc = DecompInterface()
ifc.openProgram(program)

function = getGlobalFunctions('main')[0]

# Flask app quickly decompiles sequences of assembly
from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route("/")
def hello_world():
    try:
        c = request.args.get('c')
        c = bytes.fromhex(c)[:0x200].ljust(0x200, b"\x90")
        program.getMemory().setBytes(main, c)

        results = ifc.decompileFunction(function, 0, ConsoleTaskMonitor())
        result = results.getDecompiledFunction().getC()
        if "Bad instruction - Truncating control flow" in result:
            return jsonify({"ok": False, "code": result})
        return jsonify({"ok": True, "code": result})
    except Exception as e:
        print(e)
        return jsonify({"ok": False})

app.run(host="0.0.0.0", port=5000, threaded=False)

