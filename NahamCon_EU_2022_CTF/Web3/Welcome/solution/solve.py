from brownie import *

def deploy(state, deployer, player):
    Welcome.deploy({"from": deployer[0]})

def solved(welcome_address):
    if Welcome.at(welcome_address).balance() > 0:
        return "Solved!"
    else:
        return "Need more coins!"

def main(welcome_address=None):
    if welcome_address:
        # print("Yo")
        CONFIG = {
            "RPC": "https://ctf.nahamcon.com/challenge/39/4b1c3f26-f849-4ead-b563-6ddc5f5d477b",
            # "BLOCK_NUMBER": '',
            # 'FLAGS': '',
            "MNEMONIC": "test test test test test test test test test test test junk",
            # 'RUNNABLES': [],
            "ALLOWED_RPC_METHODS": [],
        }
        # welcome_address = "0x0cB8C2Fe5f94B3b9a569Df43a9155AC008c9884b"
        attacker = accounts.from_mnemonic(CONFIG["MNEMONIC"])
        tx = attacker.transfer(to=welcome_address, amount="0.01 ether")
        tx.wait(1)
        print(f"{solved(welcome_address)}")