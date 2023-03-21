from web3 import Web3
from solcx import compile_source

compiled_sol = compile_source(open('Unknown.sol').read(), output_values=['abi', 'bin'])
contract_id, contract_interface = compiled_sol.popitem()
abi = contract_interface['abi']
bytecode = contract_interface['bin']

# 2. Add the Web3 provider logic here:
web3 = Web3(Web3.HTTPProvider('http://159.65.94.38:31929'))
print(web3.eth.get_block('latest'))

# 3. Create variables
account_from = {
    'private_key': '0xb5b4d1dafd3992c53920e604b24c9ceb974815a1cf07cbaf0a1d7a85a12db145',
    'address': '0xA7051AABeda70fdA976E74a05a63Eb3F0DBb0B70',
}
contract_address = '0x280c1a01d9bd23dDdeb6a07949Ad9c77EE904636'

# 4. Create contract instance
Incrementer = web3.eth.contract(address=contract_address, abi=abi)

# 5. Build increment tx
increment_tx = Incrementer.functions.updateSensors(10).build_transaction(
    {
        'from': account_from['address'],
        'nonce': web3.eth.get_transaction_count(account_from['address']),
    }
)

# 6. Sign tx with PK
tx_create = web3.eth.account.sign_transaction(increment_tx, account_from['private_key'])

# 7. Send tx and wait for receipt
tx_hash = web3.eth.send_raw_transaction(tx_create.rawTransaction)
tx_receipt = web3.eth.wait_for_transaction_receipt(tx_hash)
print(f'Tx successful with hash: { tx_receipt.transactionHash.hex() }')