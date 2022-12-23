# 题目描述

Santa's elves managed to find during their break a forgotten cellar. Upon entering it, they found that the cellar is full of the best drinks the world has to offer, stored in a barrel. There are N elves in total, and fortunately for them, there are also N barrels in total.

However, due to the cellar being long forgotten, bugs have made their way into the cellar, now living nearby the barrels. We know that near the i-th barrel there are v[i] bugs.

The elves want to start a party there once they get rid of all the bugs, so they will use bug-killing sprays to clean up. Each elf has his own spray which he can use to kill some of the bugs near a single barrel.

Out of the total of N sprays amongst the elves, there are K sprays which kill P bugs, and N-K sprays which kill Q bugs. Each spray can only be used once a day, since it takes a full day to be effective. Combining these sprays is very toxic and should be avoided by all means (so using any 2 sprays on the same barrel is forbidden).

Therefore, the elves' plan is simple: each day, during their break, use the sprays to remove some of the bugs, and repeat the process until the cellar is bug-free. They would like to know the minimum number of days this process will take them, so they can start preparing invites for the party.

**Example**

```
N = 5 K = 2
P = 3 Q = 1
V = [5, 3, 4, 8, 7]
=> 4 days
```

**Note**: The V array (the number of bugs located near each barrel) is generated with the following formula:

```
v[1] = v1
v[2] = v2
v[i] = (v[i - 2] * a + v[i - 1] * b + c ) % d,
```

where v1, v2, a, b, c and d are given variables

**Limits**:

```
K < N <= 10^6
P, Q <= 10^9
v1, v2, a, b, c, max(V) <= d <= 10^9
```

# 示例

```sh
nc challs.htsp.ro 14000
```

```
You will have to solve 20 tests in at most 40 seconds. Good luck!
Loading challenge...
In order to reduce output size, we generate the array of bugs with the following formula
v[i] = (a * v[i - 2] + b * v[i - 1] + c) % d, for all 2 <= i < N

Step: #1 out of 20!
N = 5; K = 3; P = 2; Q = 4
v[0] = 23; v[1] = 20
a = 26; b = 19; c = 23; d = 30
Ans = ^C
```
