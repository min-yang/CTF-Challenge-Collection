# 题目描述

The elves have stumbled upon an interesting puzzle game. The puzzle is very similar to a Rubik's cube but in 2D, a matrix A of N*N numbers which contains numbers form 1 to N*N.

Initially, the numbers are placed in order, such that A[i][j] = (i - 1) * n + j (i and j are indexed starting from 1). The elves can now circularly permute any of the lines and/or the columns, described as the following operations:

**L x** - All values from the x-th line will permute to the left by one unit

**R x** - All values from the x-th line will permute to the right by one unit

**U y** - All values from the y-th column will permute upwards by one unit

**D y** - All values from the y-th column will permute downwards by one unit

**Task1**: The elves had fun playing with the matrix, but in order for them to be able to easily reach the initial state, they only applied operations to the lines or the columns, not both. What's the minimum number of moves needed to reach the initial state?

**Example**:

```
n = 10
10 1 2 3 4 5 6 7 8 9
11 12 13 14 15 16 17 18 19 20
22 23 24 25 26 27 28 29 30 21
40 31 32 33 34 35 36 37 38 39
42 43 44 45 46 47 48 49 50 41
51 52 53 54 55 56 57 58 59 60
61 62 63 64 65 66 67 68 69 70
80 71 72 73 74 75 76 77 78 79
81 82 83 84 85 86 87 88 89 90
91 92 93 94 95 96 97 98 99 100
```

**Answer**: 5

# 解决方案

连上服务器后，服务器会依次给15个矩阵，我们需要解出每个矩阵需要移动多少次才能复原，移动规则跟魔方类似，每一行只能左右移动，每一列只能上下移动；此外，改题还有时间限制，必须在105秒内解决15个挑战，后面几个矩阵会比较大，最大的矩阵为2000\*2000，因此需要优化解决的算法，不然时间会不够。

该目录下的[solve.py](solution/solve.py)即为解决方案，这里提供的算法只计算对角线上的元素需要移动的次数，然后将其相加即可得到正确答案，因此第一行第一列的元素再怎么左右移动、上下移动，都不会改变第二行第二列的值。

最后成功解决15个挑战后，拿到flag，其为：X-MAS{Elv35_4r3_g00d_4t_puzz73s}
