# 参考资料

比赛主办方提供的参考资料如下：

### Web3 Challenges

This CTF includes the addition of new blockchain and web3 focused challenges, developed and provided by [HALBORN](https://halborn.com/).

These tasks are hosted with a special challenge type, allowing you as the player to spin up and start a private chain for you to hack on. All of the challenges are written for the [Ethereum](https://ethereum.org/) blockchain in mind using [Solidity](https://soliditylang.org/).

You can start challenges like these by pressing the Start button in the top-right corner. If you ever encounter an state were you cannot continue the challenge or you need a fresh chain instance you can always press the Reset button. The following information will be displayed under the challenge pop-up:

### Description

You will get details or clues on what the challenge is all about.

### Files

It will always be a zip file containing all the smart contracts source code needed to solve the challenge. The deployed addresses for each smart contract can be found under the Deploy details section. The folder structure was generated using a testing framework for Ethereum written in Python called [Brownie](https://eth-brownie.readthedocs.io/en/stable/). However, you can use whatever testing framework/client you are comfortable with, we recommend using [Remix IDE](https://remix.ethereum.org/) for starters/easy interaction if you are not comfortable with the command line. More under resources. Notice that some files will be useful for you to solve the challenge:

**scripts/challenge.py**

It usually contains several functions, the most important ones being deployed and solved. The former will describe how the challenge was deployed on your private chain and the latter all conditions required to solve the challenge. Some accounts will also be restricted from you to use under the restricted_accounts function.

**brownie-config.yaml**

It gives you information on what version of solc (the Solidity compiler) is being used for that specific challenge and the dependencies and mappings being for the smart contract import statements.

### Deploy details

This section does contain the pre-deployed addresses for all the smart contracts under your private chain

For example, under Brownie, you can use the following code to access the contract at that specific address:

```
contract = ContractName.at('0x95222290dd7278aa3ddd389cc1e1d165cc4bafe5')
contract.method(some_arguments, {'from': caller})
```

### Check Your Solution

There is no flag for you to manually submit. Instead, you should click the Submit/Solve button and the challenge will automatically run the solve function of the scripts/challenge.py file to verify if all the requirements are meet. If they do, the platform will count your challenge as solved, otherwise the corresponding message from the solve file will be returned.

### Resources

If you aren't familiar with web3, blockchain technology or Ethereum/Solidity, take a quick at these resources to get you up to speed!

- [Solidity by Example](https://solidity-by-example.org/)
- [Top Smart Contract vulnerabilities (similar to OWASP)](https://swcregistry.io/)
- [Remix IDE tutorial](https://betterprogramming.pub/developing-a-smart-contract-by-using-remix-ide-81ff6f44ba2f)
- [Brownie tutorial](https://chainstack.com/the-brownie-tutorial-series-part-1/)
- [Foundry tutorial](https://www.notamonadtutorial.com/ethereum-development-made-easy-with-foundry/)
- [Hardhat tutorial](https://betterprogramming.pub/the-complete-hands-on-hardhat-tutorial-9e23728fc8a4)

### Tools and Utilities

To help solve these challenges, you might find these tools helpful:

- [Remix IDE](https://remix.ethereum.org/): This is highly recommended for easy interaction if you are not comfortable on using command line, Python or Javascript. You can easily upload the zip content and directly test there, you will need some tweaks:
	- You will have to change the compiler version under Solidity Compiler > Compiler depending on the challenge and match the optimizations under Advanced Configurations
	- You will have to input your private chain RPC under Deploy & Run > Environment > External HTTP Provider (or Injected Provider) and replace `http://127.0.0.1:8545` with your custom endpoint
	- You will have to input each contract address you are interacting with under Deploy & Run > At Address.
	- When testing, the default Chain ID should be 1337 as documented here: https://eth-brownie.readthedocs.io/en/v1.13.3/network-management.html#development-networks
- [Brownie](https://eth-brownie.readthedocs.io/en/stable/): Testing framework written in Python. Tests and interaction in Python and with a console
- [Foundry](https://github.com/foundry-rs/foundry): Testing framework written in Rust. Tests and interaction in Solidity
- [Hardhat](https://github.com/NomicFoundation/hardhat): Testing framework written in Javascript. Tests and interaction in Javascript. It also has a console
- [Ganache](https://github.com/trufflesuite/ganache): It spins up a local Ethereum node with the required functionality for testing/development
- [Anvil](https://github.com/foundry-rs/foundry/tree/master/anvil): Same as Ganache but written in Rust. Really fast startup
