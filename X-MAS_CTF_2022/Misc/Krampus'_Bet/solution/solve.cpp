#include <iostream>
#include <utility>
#include <vector>

std::pair<int, int> bot(int board[52]) {
    for (int i = 0; i < 52; i++){
        std::vector<int> path;
        path.push_back(board[i]);
        while (true) {
            if (i == board[path.back()]) {
                if (path.size() >= 26) {
                    return std::make_pair(i, path.at(path.size() / 2));
                } else {
                    break;
                }
            } else {
                path.push_back(board[path.back()]);
            }
        }
    }
    return std::make_pair(0, 0);
}

int tob(int to_find, int revealed_cards[52]) {
    for (int i = 0; i < 52; i++) {
        if (revealed_cards[i] == to_find) {
            return i;
        }
    }
    int to_find_prev;
    int curr = to_find;
    while (curr != -1) {
        to_find_prev = curr;
        curr = revealed_cards[curr];
    }
    return to_find_prev;
}

//X-MAS{Kr4mpu5_l1k35_Cycl3_d3c0mp051710n}