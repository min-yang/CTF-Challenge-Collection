from architecture import *

model = BigramLanguageModel(len(vocab))
model.load_state_dict(torch.load('bigram_model.pt'))


print('H', end='')
x = torch.tensor([[vocab.index('H')]])

while True:
    y = model(x).argmax()
    print(vocab[y], end='')
    x = torch.tensor([[int(y)]])
