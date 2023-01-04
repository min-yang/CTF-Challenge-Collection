import pprint
import time

from web3 import Web3
from solcx import install_solc, compile_source
from eth_account import Account

uuid = 'xxx'
rpc = 'xxx'
privateKey = 'xxx'
setupContract = 'xxx'

web3 = Web3(Web3.HTTPProvider(rpc))
account = Account.from_key(privateKey)
print('Account address:', account.address)
print('Account Balance', web3.eth.get_balance(account.address))

for account in web3.eth.accounts:
    print('web3.eth.accounts: ', web3.eth.get_balance(account))

print('GetCode', web3.eth.get_code(setupContract).hex())

print('The address is')
print(web3.eth.get_storage_at(setupContract, 0))
contractAddress = web3.eth.get_storage_at(setupContract, 0).hex()
print(contractAddress)
checksumAddress = Web3.toChecksumAddress(contractAddress)

# Compile SOL
compiled_sol = compile_source(open('../file/Blocker.sol', 'r').read(), output_values=['abi', 'bin'])
contract_id, contract_interface = compiled_sol.popitem()
bytecode = contract_interface['bin']
abi = contract_interface['abi']

# Remote Contract
Blocker = web3.eth.contract(address=checksumAddress, abi=abi)
print('This contract has transaction_count:', web3.eth.get_transaction_count(checksumAddress))

# Get Timestamp
blockNumber = web3.eth.block_number
lastBlock = web3.eth.get_block(blockNumber)
print('The timestamp of the last block is', lastBlock.timestamp)

# Make transaction
for offset in range(-10, 10):
    tx_hash = Blocker.functions.solve(int(time.time()) + offset).transact({'from': web.eth.accounts[1], 'gas': 100000})
    receipt = web3.eth.wait_for_transaction_receipt(tx_hash)
    print('Transaction receipt mined:')
    pprint.pprint(dict(receipt))
    print('\nWas transaction successful?')
    pprint.pprint(receipt['status'] == 1)
    if receipt['status'] == 1:
        input('WOAH')

pprint.pprint(dict(web3.eth.get_transaction(tx_hash)))

print('Current Timestamp', Blocker.functions.current_timestamp().call())
print('Current isSolved', Blocker.functions.solved().call())

web3.eth.wait_for_transaction_receipt(Blocker.functions.current_timestamp().transact())