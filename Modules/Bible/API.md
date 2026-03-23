# API Bíblia – Documentação

API central do módulo Bible. Todas as rotas estão sob o prefixo **`/api/v1/bible`**. Respostas seguem o formato `{ "data": ... }`. Throttle: 60 requisições por minuto por IP.

## Endpoints

### GET `/api/v1/bible/versions`

Lista versões ativas da Bíblia (ordenadas: padrão primeiro, depois nome).

**Query:** nenhuma.

**Resposta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Nova Versão Internacional",
      "abbreviation": "NVI",
      "is_default": true
    }
  ]
}
```

---

### GET `/api/v1/bible/books`

Livros de uma versão.

**Query:**

| Parâmetro    | Tipo   | Obrigatório | Descrição                          |
|-------------|--------|-------------|------------------------------------|
| `version_id`| integer| Não         | ID da versão. Se omitido, usa a padrão. |

**Resposta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Gênesis",
      "abbreviation": "Gn",
      "book_number": 1,
      "testament": "old"
    }
  ]
}
```

---

### GET `/api/v1/bible/chapters`

Capítulos de um livro.

**Query:**

| Parâmetro   | Tipo   | Obrigatório | Descrição |
|------------|--------|-------------|-----------|
| `book_id`  | integer| Sim*        | ID do livro. |
| `book_name`| string | Sim*        | Nome do livro (usado com `version_id`). |
| `version_id` | integer | Não      | ID da versão (obrigatório se usar `book_name`). |

\* É obrigatório enviar `book_id` **ou** (`book_name` e `version_id`).

**Resposta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "chapter_number": 1,
      "total_verses": 31
    }
  ]
}
```

---

### GET `/api/v1/bible/verses`

Versículos de um capítulo.

**Query:**

| Parâmetro        | Tipo   | Obrigatório | Descrição |
|-----------------|--------|-------------|-----------|
| `chapter_id`    | integer| Sim*        | ID do capítulo. |
| `book_id`       | integer| Sim*        | ID do livro (usado com `chapter_number`). |
| `chapter_number`| integer| Sim*        | Número do capítulo. |
| `verse_range`   | string | Não         | Ex.: `1-5`, `1,3,5-10` para filtrar versículos. |

\* É obrigatório enviar `chapter_id` **ou** (`book_id` e `chapter_number`).

**Resposta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "verse_number": 1,
      "text": "No princípio Deus criou os céus e a terra."
    }
  ]
}
```

---

### GET `/api/v1/bible/find`

Busca por referência (ex.: "João 3:16", "Salmos 23:1-3").

**Query:**

| Parâmetro | Tipo  | Obrigatório | Descrição |
|-----------|-------|-------------|-----------|
| `ref`     | string| Sim         | Referência (livro + capítulo + versículo ou intervalo). |

**Resposta 200:**
```json
{
  "data": {
    "reference": "João 3:16",
    "book": "João",
    "chapter": 3,
    "verses": [
      {
        "id": 123,
        "verse_number": 16,
        "text": "Porque Deus amou o mundo..."
      }
    ],
    "full_chapter_url": "https://..."
  }
}
```

**Resposta 404:** referência não encontrada ou formato inválido.

---

### GET `/api/v1/bible/search`

Busca por texto ou referência. Tenta referência exata primeiro; se não achar, faz busca por texto no conteúdo.

**Query:**

| Parâmetro | Tipo  | Obrigatório | Descrição |
|-----------|-------|-------------|-----------|
| `q`       | string| Sim         | Texto ou referência. |

**Resposta 200 (referência exata):**
```json
{
  "data": {
    "type": "exact",
    "reference": "João 3:16",
    "verses": [...],
    "full_chapter_url": "https://..."
  }
}
```

**Resposta 200 (busca por texto):**
```json
{
  "data": [
    {
      "id": 123,
      "reference": "João 3:16",
      "text": "...",
      "type": "search"
    }
  ]
}
```

---

### GET `/api/v1/bible/random`

Retorna um versículo aleatório.

**Query:**

| Parâmetro    | Tipo   | Obrigatório | Descrição |
|-------------|--------|-------------|-----------|
| `version_id`| integer| Não         | Restringe à versão. |

**Resposta 200:**
```json
{
  "data": {
    "id": 123,
    "verse_number": 16,
    "text": "...",
    "chapter": { "id": 1, "chapter_number": 3 },
    "book": { "id": 43, "name": "João", "abbreviation": "Jo", "book_number": 43 }
  }
}
```

---

### GET `/api/v1/bible/compare`

Compara o mesmo trecho em duas versões.

**Query:**

| Parâmetro     | Tipo   | Obrigatório | Descrição |
|--------------|--------|-------------|-----------|
| `v1`         | mixed  | Sim         | ID ou abreviatura da versão 1. |
| `v2`         | mixed  | Sim         | ID ou abreviatura da versão 2. |
| `book_number`| integer| Sim         | Número do livro. |
| `chapter`    | integer| Sim         | Número do capítulo. |
| `verse`      | integer| Não         | Versículo específico; se omitido, retorna o capítulo inteiro. |

**Resposta 200:**
```json
{
  "data": {
    "v1": {
      "abbreviation": "NVI",
      "name": "Nova Versão Internacional",
      "verses": [...]
    },
    "v2": {
      "abbreviation": "ARA",
      "name": "Almeida Revista e Atualizada",
      "verses": [...]
    }
  }
}
```

---

## Uso interno (backend)

Para leitura no backend (ex.: HomePage, EBD, Sermons), use o **`BibleApiService`**:

```php
use Modules\Bible\App\Services\BibleApiService;

$bibleApi = app(BibleApiService::class);

$versions = $bibleApi->getVersions();
$books = $bibleApi->getBooks($versionId);
$chapters = $bibleApi->getChapters($bookId);
$verses = $bibleApi->getVerses($chapterId, null, null, '1-5');
$result = $bibleApi->findByReference('João 3:16');
$verse = $bibleApi->getRandomVerse($versionId);
```

Após import/atualização de dados, limpe o cache:

```php
$bibleApi->clearCache();
```
