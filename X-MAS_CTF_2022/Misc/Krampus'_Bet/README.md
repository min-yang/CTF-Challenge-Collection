# Description

Krampus has trapped two new bots, Botbot and Tobbot into his lair for another challenge.

This year's challenge stands as follows:

- Botbot will be the first to enter the lair. Krampus will have laid down in a straight line on a table a deck of 52 cards (numbered from 0 to 51), facing up and will allow Botbot to choose two of the cards on the table and swap their positions.
- Tobbot will enter the lair afterwards and will see the table with the cards in the same positions (after the swap) as before, but facing down. Krampus will indicate some card from 0 to 51 to find, in at most 26 tries. For each try, Tobbot will indicate one card from the table and turn it over, revealing its value.

Once again, you will have to program Botbot's and Tobbot's behavioural logic chips so that they can solve the challenge and escape Krampus' damnation.

To program Botbot you will have to implement the following C++ function:

std::pair <int, int> bot(int board[52]), which will take as parameter the intial state of the board, each element board[i] of the array will represent the card that is on position i on the board. The function must return the positions of the cards that Botbot wants to swap.

To program Tobbot you will have to implement the following C++ function:

int tob(int to_find, int revealed_cards[52]), which will take as arguments to_find, the card that Tobbot has to find and revealed_cards the state of his board so far, with -1 in a position indicating a card is still facing down and another value representing the card that is on that position. The function will have to return the guess that tob should take (a position from 0 to 51) at this point in the game.

For communicating with the server, you will have to provide the implementation of these functions in order, terminated with a completely blank line (so each implementation will end in two newline characters).

P.S: Krampus hates warnings ('-Wall', '-Wextra', '-Werror', '-Wpedantic') is used

P.P.S: You have vector and utility at your disposal
