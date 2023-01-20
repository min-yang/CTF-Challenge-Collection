import sys
import random


def main():

    byte_data = []
    file_name = sys.argv[1]
    num = random.randint(1, 1024)

    with open(file_name, "rb") as f:
        byte_data = f.read()        
        bdh = byte_data.hex()
        out = []

        for each in list(bdh):
            out.append(ord(each) ^ num)

        concat = "".join(chr(i) for i in out)

        with open("output.enc", "w") as f:
            f.write(concat)



if __name__ == "__main__":
    main()