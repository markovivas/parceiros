# Sistema de Parceiros

Plugin WordPress para gerenciamento e exibição de parceiros/parcerias com filtros, shortcode e design moderno.

## Funcionalidades

- **CRUD completo** — Adicionar, editar, excluir e buscar parceiros pelo admin
- **Shortcode `[parceiros]`** — Exiba os parceiros em qualquer página/post
- **Filtros em tempo real** — Filtrar por setor, categoria e nome no frontend
- **Cards modernos** — Layout responsivo em grid com animações e ícones SVG
- **WhatsApp integrado** — Botão de contato abre o WhatsApp com o número do parceiro
- **Logo padronizada** — Container fixo com `object-fit: contain` sem deformar
- **Admin estilizado** — Tabela com paginação, ordenação e busca
- **Upload de logo** — Suporte a JPG, PNG, GIF e WebP (máx. 2MB)

## Instalação

1. Faça upload da pasta `parceiros` para `/wp-content/plugins/`
2. Ative o plugin em **Plugins** no WordPress
3. Acesse o menu **Parceiros** no admin

## Uso

### Shortcode

```
[parceiros]
[parceiros categoria="Desenvolvimento"]
[parceiros setor="Tecnologia" limite="5"]
```

### Atributos

| Atributo   | Descrição                     | Padrão |
|------------|-------------------------------|--------|
| `categoria`| Filtra por categoria          | vazio  |
| `setor`    | Filtra por setor              | vazio  |
| `limite`   | Número máximo de parceiros    | -1     |

### Template

Use `obter_parceiros($filtros)` em qualquer template PHP:

```php
$parceiros = obter_parceiros(array('setor' => 'Tecnologia'));
foreach ($parceiros as $p) {
    echo $p->nome;
}
```

## Requisitos

- WordPress 5.0+
- PHP 7.0+
- MySQL / MariaDB
