import pandas as pd
import json

# Data loading and preprocessing
df = pd.read_excel('produtos.xlsx', skiprows=1)
df = df.iloc[:, 0:3]

df.columns = ['codigo', 'descricao', 'familia']
df.dropna(subset=['codigo'], inplace=True)

df['codigo'] = df['codigo'].astype(int).astype(str)
df['codigo'] = df['codigo'].str[:3] + '.' + df['codigo'].str[3:]

df.drop_duplicates(subset=['codigo'], keep='first', inplace=True)

# JSON-like string construction
data_dict = df.set_index('codigo').to_dict(orient='index')

json_str = json.dumps(data_dict, ensure_ascii=False, indent=4)

js_content = json_str

with open('produtos.json', 'w', encoding='utf-8') as f:
    f.write(js_content)

print('Exemplo do JSON \n', js_content[:500])
