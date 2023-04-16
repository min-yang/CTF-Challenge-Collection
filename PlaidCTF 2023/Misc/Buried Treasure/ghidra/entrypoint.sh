#!/bin/bash

rm -rf workspace/
mkdir workspace/

# Send the ghidra decompiler server to the background...
/ghidra/support/analyzeHeadless workspace/ headless -import empty_elf -scriptPath scripts/ -postScript decompile_ghidra.py
