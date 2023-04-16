import capstone
import iced_x86
import requests
import secrets
import os
import subprocess

CODE_MAX_LEN = 0x100

CAPSTONE_ALLOWED_JUMPS = { capstone.x86.X86_INS_JNE, capstone.x86.X86_INS_JE }
ICED_ALLOWED_JUMPS = { iced_x86.Code.JNE_REL8_64, iced_x86.Code.JE_REL8_64 }

GHIDRA_URL = os.getenv("GHIDRA_URL", "http://localhost:5000")
BINJA_URL = os.getenv("BINJA_URL", "http://localhost:5001")


# Capstone
def check1(code):
    md = capstone.Cs(capstone.CS_ARCH_X86, capstone.CS_MODE_64)
    md.detail = True

    ok = True
    last_idx = 0
    valid_targets = set()
    jump_targets = set()

    for ins in md.disasm(code, 0):
        if (
            (capstone.CS_GRP_JUMP in ins.groups and ins.id not in CAPSTONE_ALLOWED_JUMPS)
            or not set(ins.groups).isdisjoint({capstone.CS_GRP_CALL, capstone.CS_GRP_RET, capstone.CS_GRP_IRET})
        ):
            ok = False
            break

        last_idx = ins.address + ins.size

        # Track jump targets
        if ins.id in CAPSTONE_ALLOWED_JUMPS:
            target = ins.operands[0].imm
            jump_targets.add(target)
        valid_targets.add(ins.address)

    if last_idx != len(code):
        ok = False

    if not jump_targets.issubset(valid_targets):
        ok = False

    return ok

# Iced
def check2(code):
    decoder = iced_x86.Decoder(64, code, ip=0)

    ok = True
    last_idx = 0
    valid_targets = set()
    jump_targets = set()
    for ins in decoder:
        if not ins:
            ok = False
            break

        if not (
            ins.flow_control == iced_x86.FlowControl.NEXT
            or ins.code == iced_x86.Code.SYSCALL
            or ins.code in ICED_ALLOWED_JUMPS
        ):
            ok = False

        last_idx = ins.ip + ins.len

        # Track jump targets
        if ins.code in ICED_ALLOWED_JUMPS:
            jump_targets.add(ins.near_branch_target)
        valid_targets.add(ins.ip)

    if last_idx != len(code):
        ok = False

    if not jump_targets.issubset(valid_targets):
        ok = False

    return ok


def run():
    code = input("Enter x86 (hex): ")
    try:
        code = bytes.fromhex(code)
    except:
        print("error")
        return

    if len(code) > CODE_MAX_LEN:
        print("too long")
        return

    if not check1(code):
        print("capstone: bad instruction")
        return

    if not check2(code):
        print("iced: bad instruction")
        return

    code += b"\xf4" # hlt

    # Decompile with ghidra
    ghidra_result = requests.get(f"{GHIDRA_URL}/?c={code.hex()}").json()

    # Decompile with binja
    binja_result = requests.get(f"{BINJA_URL}/?c={code.hex()}").json()

    print(f"Ghidra\n=====\n\n{ghidra_result.get('code', 'fail')}\n\n")
    print(f"Binja\n=====\n\n{binja_result.get('code', 'fail')}\n\n")

    if not ghidra_result.get("ok", False):
        print(f"Ghidra fail")
        return

    if "syscall" in ghidra_result["code"]:
        print("Ghidra: syscalls not allowed")
        return

    if not binja_result.get("ok", False):
        print(f"Binja fail {binja_result}")
        return

    if "syscall" in binja_result["code"]:
        print("Binja: syscalls not allowed")
        return

    level = os.getenv('LEVEL', '2')
    print(f"Running (level {level})...")
    subprocess.run([
        "nsjail",
        "--config", "jail.cfg",
        "--user", "ctf",
        "--group", "ctf",
        "-E", f"LEVEL={level}"
    ], check=True, input=code)


if __name__ == "__main__":
    run()

