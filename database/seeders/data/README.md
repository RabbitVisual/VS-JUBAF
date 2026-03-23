# Dados de CEP

## Como obter o arquivo CSV

1. Acesse: https://gist.github.com/tamnil/792a6a66f6df9fc028041587cfca0c3d
2. Clique em "Raw" para ver o conteúdo bruto
3. Salve o conteúdo como `ceps.csv` neste diretório

Ou use o comando curl (Linux/Mac):

```bash
curl -o ceps.csv https://gist.githubusercontent.com/tamnil/792a6a66f6df9fc028041587cfca0c3d/raw/ceps.csv
```

## Estrutura do CSV

O arquivo deve ter o seguinte formato:

```csv
UF,CIDADE,CEP DE,CEP ATÉ
SP,São Paulo,01310000,01310999
RJ,Rio de Janeiro,20000000,20099999
```

## Importar dados

Após salvar o arquivo `ceps.csv` neste diretório, execute:

```bash
php artisan db:seed --class=CepRangeSeeder
```
