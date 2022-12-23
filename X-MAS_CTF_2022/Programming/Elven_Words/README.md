# 题目描述

The old elves decided to play a game of words.

Each of the N elves writes a single word in a dictionary.

Then, they would like to create a new dictionary by combining any of the words from the old dictionary.

For instance, if the old dictionary contains the words "a", "b" and "c", then the new dictionary contains the words: "a", "b", "c", "ab", "ba", "ac", "ca", "bc", "cb", "abc", "acb", "bac", "bca", "cab", "cba"

The elves' favorite words are "original" words. An original word is a word such that it contains a letter which appears at least floor(len / 2) + 1 times in the word. For instance, 'aaabc' is a valid word (a appears 3 times), while 'ab' is not.

The elves would like to know:

What's the maximum number of words (from the old dictionary) that they can combine in order to produce an "original" word?

What's the maximum length of an "original" word?

**Limits**:

The sum of the lengths of all words <= 10.000

# 示例

```sh
nc challs.htsp.ro 14003
```

```
You will have to solve 30 tests in at most 90 seconds. Good luck!
The input will have the following format:
n
s1
s2
..
sn
The output should have the following format: ans_task1 ans_task2
Loading challenge...
Step: #1 out of 30!
54
xvyxvvyzuxovuy
pussouxuouwy
usvvyyvzopyvy
oouxytosv
sqyotuzr
qy
uqszwrtw
pto
oxyquyuywpzr
upsvpqprrrsvqr
yptqzppwtxo
xzuprqrss
xutt
xuwuxoytxtuutur
tyy
tyx
towuoszxuoos
spspxwqzwrz
ox
prtszvqo
ttst
qoqxz
p
tzy
s
yqp
ryx
vvsuyx
vxqvwtstosz
tvurrruzop
zo
xsorqxtsp
qvxxrsxwvptzz
oqox
urssrprsyqrstz
w
rvyvvu
vxxrzyotzp
vtvvuwvv
pxwtoq
ryrszvrsquoxzpo
oros
zsxutswwvxv
vzpovrvx
uwzyqxwqqowy
uourzvvw
woyxqvrqp
pwsq
sro
wtqxxxxzx
urw
vxrsssvqtyty
t
x
Ans =
```
