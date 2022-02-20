# Amazon Affiliate Link Generator
**Criador**: [D1360-64RC14](https://github.com/D1360-64RC14)  
**Tags**: amazon, affiliate, rest, api, product  
**Testado em**: 5.9  
**Licença**: [Apache-2.0](https://www.apache.org/licenses/LICENSE-2.0.txt)

Este plugin cria um endpoint REST que transforma links de produtos da Amazon em links de afiliado reproduzindo o comportamento do SiteStripe

## Descrição

É criado uma rota `wp-json/api/v1/affiliate-link` que receberá requisições GET para criação do link de afiliado.

O endpoint `/affiliate-link` recebe 3 parâmetros de URL:
- **url**: (obrigatório) URL de produto da Amazon [encodada](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent);
- **tag**: (opcional) Tag de afiliado a ser utilizada (ver seção [Instalação](#instalação));
- **simple**: (opcional) Transforma as mensagens de erro em textos simples, ao invés do formato JSON.

### Exemplos

URL de produto a ser transformada: https://www.amazon.com.br/gp/product/B08C1K6LB2
```
site.com/wp-json/api/v1/affiliate-link?url=https%3A%2F%2Fwww.amazon.com.br%2Fgp%2Fproduct%2FB08C1K6LB2
site.com/wp-json/api/v1/affiliate-link?url=https%3A%2F%2Fwww.amazon.com.br%2Fgp%2Fproduct%2FB08C1K6LB2&simple
site.com/wp-json/api/v1/affiliate-link?url=https%3A%2F%2Fwww.amazon.com.br%2Fgp%2Fproduct%2FB08C1K6LB2&tag=newsinside0d-20
site.com/wp-json/api/v1/affiliate-link?url=https%3A%2F%2Fwww.amazon.com.br%2Fgp%2Fproduct%2FB08C1K6LB2&tag=newsinside0d-20&simple
```
O resultado de todas as 4 requisições será um link de afiliado em texto simples.

#### Parâmetro `simple`

Normalmente, em caso de erros, será retornado [status code 400](https://httpstatuses.com/400)
com um corpo em JSON contendo a mensagem de erro. Exemplo:
```json
{
  "error_type": "invalid_product_url",
  "error_message": "Path de produto inválido. Deve ser um produto da Amazon"
}
```
Adicionando o parâmetro `simple` à URL, a mensagem de erro será formatada como texto simples e conterá apenas o campo `"error_message"`.
Exemplo:
```
Path de produto inválido. Deve ser um produto da Amazon
```

Essa configuração é útil caso vá integrar a API com outras ferramentas, como, por exemplo, bots de chat.

### URLs de produtos válidas

Os seguintes exemplos de URL de produto da Amazon são válidas:
- https://www.amazon.com.br/gp/product/B08C1K6LB2
- https://www.amazon.com.br/Echo-Dot-3ª-Geração-Cor-Preta/dp/B07PDHSJ1H
- https://www.amazon.com.br/Echo-Dot-3ª-Geração-Cor-Preta/dp/B07PDHSJ1H/ref=p13n_ds_purchase_sim_1p_dp_desktop_5/130-1374962-7819459
- https://www.amazon.com.br/dp/B09FTLKBGX

Com destaque no protocolo `https://` e hostname `amazon.com` que são obirgatórios.

Qualquer outro parâmetro que vier junto do link do produto será passado para frente no link de afiliado.

## Instalação

1. Copie o atual diretório `wp-amz-affiliate-link` para `wp-content/plugins/` do seu WordPress;
2. Edite o arquivo `affiliate_link.php` e insira sua tag de afiliado na constante `AFFILIATE_TAG` seguindo o exemplo comentado nas linhas anteriores;
3. Ative o plugin no painel *Plugins* de seu WordPress;
4. Verifique se em *Configurações > Links permanentes > Configurações comuns*, a opção *Padrão* **NÃO** está selecionada.
Se a mesma estiver, não será possível acessar a rota `/wp-json/`.

#### Exemplo do passo 2.
```php
/**
 * Tag de afiliado.
 * Caso esteja vazia, o parâmetro de URL 'tag' será obrigatório.
 * Exemplo:
 * const AFFILIATE_TAG = 'example-tag-20';
 */
const AFFILIATE_TAG = 'someone-20';
```

#### Importante
No item 2, caso você deixe o campo da tag de afiliado em branco, a mesma será obrigatória na requisição.
