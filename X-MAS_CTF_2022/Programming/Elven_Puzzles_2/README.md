# Descrption

This is a continuation of the task Elven Puzzles 1.

**Task2**:The elves noted down a sequence of m moves they've applied to the initial matrix. Now, they are wondering, how times they would have to repeat that exact sequence of moves to an initial matrix an integer amout of times to reach the initial state? The answer should be computed modulo 109+7

**Note**: The matrix is 0-indexed.

**Example 1**:

```
n = 3, m = 5
U 1
R 1
U 2
R 1
L 2
Answer:** 6
```

**Example 2**:

```
n = 8, m = 10
R 5
L 7
R 3
U 2
L 2
L 0
R 2
U 4
U 5
U 2
Answer:4284
```

**Limits**:

```
n <= 1.000 & m <= 25.000
```

# Example

```sh
nc challs.htsp.ro 14002
```

```
You will have to solve 15 tests in at most 60 seconds. Good luck!

The input will have the following format:
n m
mv_1 idx_1
mv_2 idx_2
 ...
mv_m idx_n

Loading challenge...
Step: #1 out of 15!
10 5
L 5
U 0
U 8
U 7
D 9
Ans =
```