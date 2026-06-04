# MenuStock — Documentação Técnica do Projeto

> Sistema de gerenciamento de restaurante com cardápio digital, pedidos, reservas e controle de estoque de ingredientes.

---

## 1. Visão Geral do Sistema

O **MenuStock** é uma aplicação web desenvolvida em **PHP 8** com banco de dados **MySQL/MariaDB**, seguindo o padrão arquitetural **MVC (Model-View-Controller)**. O sistema atende três tipos de usuário: **administrador**, **garçom** e **cliente**, cada um com permissões e telas específicas.

### Tecnologias Utilizadas

| Camada         | Tecnologia                        |
|----------------|-----------------------------------|
| Back-end       | PHP 8+ com `declare(strict_types=1)` |
| Banco de Dados | MySQL / MariaDB (PDO)             |
| Front-end      | HTML5 + CSS3 (Vanilla)            |
| Servidor       | Apache (XAMPP)                    |
| Conexão DB     | PDO com prepared statements       |

---

## 2. Estrutura de Pastas

```
MenuStock/
├── index.php                  # Cardápio público
├── login.php                  # Tela de login e cadastro
├── carrinho.php               # Carrinho de compras do cliente
├── reserva.php                # Formulário de reserva de mesa
├── ver_prato.php              # Detalhe de um prato
├── meus_pedidos.php           # Histórico de pedidos do cliente
├── banco de dados.sql         # Script SQL completo do banco
├── composer.json              # Dependências PHP
└── admin/
    ├── config/
    │   ├── conexao.php        # Conexão PDO (Singleton)
    │   ├── auth.php           # Funções de autenticação e utilitários
    │   ├── header_admin.php   # Cabeçalho do painel admin
    │   ├── footer_admin.php   # Rodapé do painel admin
    │   ├── public_header.php  # Cabeçalho das páginas públicas
    │   └── public_footer.php  # Rodapé das páginas públicas
    ├── models/
    │   ├── Usuario.php        # Classe de usuários
    │   ├── Prato.php          # Classe de pratos
    │   ├── Categoria.php      # Classe de categorias
    │   ├── Ingrediente.php    # Classe de ingredientes
    │   ├── Carrinho.php       # Classe do carrinho
    │   ├── Pedido.php         # Classe de pedidos
    │   ├── Promocao.php       # Classe de promoções
    │   └── Reserva.php        # Classe de reservas de mesa
    ├── views/
    │   ├── dashboard.php
    │   ├── gerenciar_pratos.php
    │   ├── gerenciar_pedidos.php
    │   ├── gerenciar_reservas.php
    │   ├── gerenciar_categorias.php
    │   ├── gerenciar_ingredientes.php
    │   ├── gerenciar_promocoes.php
    │   ├── ver_usuarios.php
    │   ├── menu_garcom.php
    │   ├── cadastrar_prato.php
    │   ├── editar_prato.php
    │   ├── cadastrar_usuario.php
    │   ├── editar_usuario.php
    │   ├── alterar_senha_usuario.php
    │   └── partials/          # Componentes reutilizáveis de HTML
    ├── controllers/
    │   ├── atualizar_status_pedido.php
    │   ├── atualizar_status_reserva.php
    │   ├── deletar_categoria.php
    │   ├── deletar_ingrediente.php
    │   ├── deletar_prato.php
    │   ├── deletar_promocao.php
    │   ├── deletar_usuario.php
    │   └── logout.php
    ├── public/
    │   └── imagens/           # Imagens dos pratos (upload)
    └── storage/               # Armazenamento adicional
```

---

## 3. Banco de Dados

### 3.1 Tabelas Principais

#### `usuarios`
Armazena todos os usuários do sistema.

| Coluna           | Tipo                         | Descrição                                        |
|------------------|------------------------------|--------------------------------------------------|
| `id`             | INT AUTO_INCREMENT PK        | Identificador único                              |
| `nome`           | VARCHAR(100)                 | Nome completo                                    |
| `email`          | VARCHAR(150) UNIQUE          | E-mail (também serve como login)                 |
| `senha`          | VARCHAR(255)                 | Hash bcrypt da senha                             |
| `telefone`       | VARCHAR(20)                  | Telefone opcional                                |
| `tipo`           | ENUM('admin','garcom','user')| Perfil de acesso                                 |
| `login_attempts` | TINYINT UNSIGNED             | Contador de tentativas falhas de login           |
| `locked_until`   | DATETIME                     | Data/hora até quando a conta fica bloqueada      |
| `created_at`     | TIMESTAMP                    | Data de cadastro                                 |

#### `categorias`
Organiza os pratos em grupos.

| Coluna           | Tipo         | Descrição                              |
|------------------|--------------|----------------------------------------|
| `id`             | INT PK       | Identificador                          |
| `nome`           | VARCHAR(100) | Nome da categoria (ex.: Entradas)      |
| `descricao`      | TEXT         | Descrição opcional                     |
| `ordem_exibicao` | INT          | Ordem de aparição no cardápio          |

#### `pratos`
Registro de cada prato do cardápio.

| Coluna         | Tipo           | Descrição                                  |
|----------------|----------------|--------------------------------------------|
| `id`           | INT PK         | Identificador                              |
| `categoria_id` | INT FK         | Referência a `categorias`                  |
| `nome`         | VARCHAR(100)   | Nome do prato                              |
| `descricao`    | TEXT           | Descrição detalhada                        |
| `preco`        | DECIMAL(10,2)  | Preço base                                 |
| `tempo_preparo`| INT            | Tempo de preparo em minutos                |
| `disponivel`   | TINYINT(1)     | 1 = disponível, 0 = indisponível           |
| `imagem`       | VARCHAR(255)   | Nome do arquivo de imagem (upload)         |

#### `ingredientes`
Cadastro de ingredientes disponíveis.

| Coluna    | Tipo         | Descrição                         |
|-----------|--------------|-----------------------------------|
| `id`      | INT PK       | Identificador                     |
| `nome`    | VARCHAR(100) | Nome do ingrediente               |
| `unidade` | VARCHAR(20)  | Unidade de medida (g, ml, uni...) |

#### `prato_ingrediente` (tabela pivô)
Relaciona pratos com seus ingredientes (N:N).

| Coluna           | Tipo          | Descrição                     |
|------------------|---------------|-------------------------------|
| `prato_id`       | INT FK PK     | Referência a `pratos`         |
| `ingrediente_id` | INT FK PK     | Referência a `ingredientes`   |
| `quantidade`     | DECIMAL(8,2)  | Quantidade usada no prato     |

#### `carrinho`
Itens adicionados ao carrinho pelo cliente (temporário).

| Coluna       | Tipo           | Descrição                          |
|--------------|----------------|------------------------------------|
| `id`         | INT PK         | Identificador                      |
| `usuario_id` | INT FK         | Dono do carrinho                   |
| `prato_id`   | INT FK         | Prato adicionado                   |
| `quantidade` | INT            | Quantidade desejada                |
| `obs_item`   | TEXT           | Observação específica (sem cebola) |
| `preco_unit` | DECIMAL(10,2)  | Preço no momento da adição         |

#### `pedidos`
Pedidos finalizados a partir do carrinho.

| Coluna       | Tipo                                                       | Descrição                      |
|--------------|------------------------------------------------------------|--------------------------------|
| `id`         | INT PK                                                     | Identificador                  |
| `usuario_id` | INT FK (nullable)                                          | Cliente (null = mesa via garçom)|
| `status`     | ENUM('recebido','preparo','pronto','entregue','cancelado') | Status atual do pedido         |
| `obs_geral`  | TEXT                                                       | Observação geral do pedido     |
| `total`      | DECIMAL(10,2)                                              | Valor total                    |
| `mesa`       | TINYINT                                                    | Número da mesa (opcional)      |

#### `itens_pedido`
Itens individuais de cada pedido (snapshot do preço).

| Coluna       | Tipo           | Descrição                                  |
|--------------|----------------|--------------------------------------------|
| `id`         | INT PK         | Identificador                              |
| `pedido_id`  | INT FK         | Referência ao pedido                       |
| `prato_id`   | INT FK         | Referência ao prato                        |
| `quantidade` | INT            | Quantidade pedida                          |
| `preco_unit` | DECIMAL(10,2)  | **Preço congelado** no momento do pedido   |
| `obs_item`   | TEXT           | Observação individual do item              |

> **Regra importante:** O preço é salvo no item do pedido no momento da compra para garantir histórico correto, mesmo que o preço do prato mude futuramente.

#### `promocoes`
Promoções aplicadas a pratos específicos.

| Coluna        | Tipo                                              | Descrição                                 |
|---------------|---------------------------------------------------|-------------------------------------------|
| `id`          | INT PK                                            | Identificador                             |
| `prato_id`    | INT FK                                            | Prato ao qual a promoção se aplica        |
| `nome`        | VARCHAR(100)                                      | Nome da promoção                          |
| `tipo`        | ENUM('desconto_percentual','desconto_fixo','combo')| Tipo de desconto                         |
| `valor`       | DECIMAL(10,2)                                     | Percentual (%) ou valor fixo (R$)         |
| `data_inicio` | DATE                                              | Início da vigência                        |
| `data_fim`    | DATE                                              | Fim da vigência                           |
| `ativa`       | TINYINT(1)                                        | 1 = ativa, 0 = inativa                   |

#### `reservas`
Reservas de mesa feitas pelos clientes.

| Coluna       | Tipo                                    | Descrição                       |
|--------------|-----------------------------------------|---------------------------------|
| `id`         | INT PK                                  | Identificador                   |
| `usuario_id` | INT FK                                  | Cliente que fez a reserva       |
| `data`       | DATE                                    | Data da reserva                 |
| `horario`    | TIME                                    | Horário da reserva              |
| `num_pessoas`| INT                                     | Número de pessoas               |
| `status`     | ENUM('pendente','confirmada','cancelada')| Status da reserva              |
| `observacao` | TEXT                                    | Observações adicionais          |

---

### 3.2 Views (Visões SQL)

#### `vw_promocoes_ativas`
Calcula o **preço promocional** de cada prato com promoção vigente no dia atual.

```sql
-- Lógica de cálculo do preço:
CASE
  WHEN tipo = 'desconto_percentual' THEN preco - (preco * valor / 100)
  WHEN tipo = 'desconto_fixo'       THEN preco - valor
  ELSE preco  -- tipo 'combo': sem alteração de preço
END AS preco_promocional
```
Filtra apenas promoções com `ativa = 1` e onde `CURDATE()` está entre `data_inicio` e `data_fim`.

#### `vw_pedidos_resumo`
Une pedidos com clientes e conta itens. Quando não há usuário vinculado (pedido de mesa via garçom), exibe `"Mesa {numero}"` como nome do cliente usando `COALESCE`.

#### `vw_reservas_painel`
Une reservas com dados do cliente para exibição no painel administrativo, ordenadas por data e horário.

---

## 4. Classes (Models)

Todas as classes ficam em `admin/models/` e usam injeção de dependência da conexão PDO via a função singleton `getConnection()`.

---

### 4.1 Classe `Usuario`
**Arquivo:** `admin/models/Usuario.php`

Responsável por todo o gerenciamento de usuários do sistema.

#### Métodos

| Método | Visibilidade | Descrição |
|--------|-------------|-----------|
| `autenticar(string $email, string $senha): ?array` | public | Autentica o usuário com controle de tentativas e bloqueio |
| `cadastrar(array $dados): int` | public | Cadastra novo usuário, retorna o ID inserido |
| `editar(int $id, array $dados): bool` | public | Atualiza dados do usuário |
| `alterarSenha(int $id, string $senha): bool` | public | Atualiza a senha com novo hash |
| `deletar(int $id, ?int $usuarioLogadoId): bool` | public | Remove usuário (não permite auto-deleção) |
| `listar(?string $busca): array` | public | Lista todos os usuários com filtro opcional por nome/e-mail |
| `listarClientes(): array` | public | Lista apenas usuários do tipo `user` |
| `buscarPorId(int $id): ?array` | public | Busca usuário pelo ID |
| `tipoPorEmail(string $email): string` | public static | Determina o perfil do usuário pelo domínio do e-mail |

#### Regras e Lógicas Importantes

**Determinação automática do tipo de usuário por e-mail (`tipoPorEmail`):**
```
@garcom.menustock.com.br  → tipo: 'garcom'
@menustock.com.br         → tipo: 'admin'
qualquer outro e-mail     → tipo: 'user'
```

**Proteção contra força bruta no login (`autenticar`):**
- A cada senha errada, incrementa `login_attempts`
- Ao atingir **5 tentativas erradas**, a conta é bloqueada por **15 minutos** (`locked_until`)
- Ao fazer login com sucesso, zera o contador e remove o bloqueio
- A senha é verificada com `password_verify()` contra o hash `bcrypt` salvo no banco

**Validações no cadastro:**
- Nome não pode ser vazio
- E-mail deve ser válido (verificado com `filter_var`)
- Senha deve ter no mínimo **6 caracteres**
- A senha é armazenada como hash com `password_hash($senha, PASSWORD_DEFAULT)`

---

### 4.2 Classe `Prato`
**Arquivo:** `admin/models/Prato.php`

Gerencia o cadastro, edição, listagem e exclusão de pratos do cardápio.

#### Métodos

| Método | Visibilidade | Descrição |
|--------|-------------|-----------|
| `listar(?string $busca, ?int $categoriaId): array` | public | Lista todos os pratos (admin) |
| `listarDisponiveis(?string $busca, ?int $categoriaId): array` | public | Lista apenas pratos disponíveis (público) |
| `buscarPorId(int $id, bool $somenteDisponivel): ?array` | public | Busca prato pelo ID |
| `precoAtual(int $id): ?float` | public | Retorna o preço com promoção ou preço base |
| `cadastrar(array $dados, ?array $arquivo): int` | public | Cadastra novo prato com imagem e ingredientes |
| `editar(int $id, array $dados, ?array $arquivo): bool` | public | Edita prato existente |
| `deletar(int $id): bool` | public | Remove prato (bloqueado se tiver pedidos) |
| `alterarDisponibilidade(int $id, bool $disponivel): bool` | public | Alterna disponibilidade do prato |
| `consultarPratos(...)` | private | Método interno que monta a query dinâmica |
| `salvarIngredientes(...)` | private | Sincroniza ingredientes do prato (apaga e reinsere) |
| `salvarImagem(...)` | private | Valida e salva a imagem do prato |

#### Regras e Lógicas Importantes

**Preço com promoção (`precoAtual`):**
- Consulta a view `vw_promocoes_ativas` para verificar se há promoção vigente
- Retorna `preco_promocional` se existir, caso contrário retorna `preco` base

**Upload de imagem (`salvarImagem`):**
- Tamanho máximo: **3 MB**
- Extensões permitidas: `jpg`, `jpeg`, `png`, `webp`, `gif`
- Validação dupla: extensão do arquivo **e** MIME type real via `mime_content_type()`
- Nome gerado aleatoriamente com `bin2hex(random_bytes(12))` para evitar conflitos
- Salvo em `admin/public/imagens/`

**Exclusão de prato (`deletar`):**
- Um prato **não pode ser excluído** se houver registros em `itens_pedido` referenciando-o

**Sincronização de ingredientes (`salvarIngredientes`):**
- Remove todos os ingredientes anteriores e reinsere os novos (operação "apaga e recria")
- Garante que a lista de ingredientes esteja sempre atualizada

---

### 4.3 Classe `Categoria`
**Arquivo:** `admin/models/Categoria.php`

Gerencia as categorias do cardápio.

#### Métodos

| Método | Visibilidade | Descrição |
|--------|-------------|-----------|
| `listar(): array` | public | Lista todas as categorias ordenadas por `ordem_exibicao` |
| `cadastrar(array $dados): int` | public | Cria nova categoria |
| `editar(int $id, array $dados): bool` | public | Atualiza categoria |
| `deletar(int $id): bool` | public | Remove categoria |

#### Regra: Exclusão Bloqueada
Uma categoria **não pode ser excluída** enquanto houver pratos vinculados a ela (verificação via `COUNT(*) FROM pratos WHERE categoria_id = ?`).

---

### 4.4 Classe `Ingrediente`
**Arquivo:** `admin/models/Ingrediente.php`

Gerencia o cadastro de ingredientes.

#### Métodos

| Método | Visibilidade | Descrição |
|--------|-------------|-----------|
| `listar(): array` | public | Lista todos os ingredientes em ordem alfabética |
| `listarPorPrato(int $pratoId): array` | public | Lista ingredientes de um prato específico com quantidade |
| `cadastrar(array $dados): int` | public | Cria novo ingrediente |
| `editar(int $id, array $dados): bool` | public | Atualiza ingrediente |
| `deletar(int $id): bool` | public | Remove ingrediente |

#### Regra: Exclusão Bloqueada
Um ingrediente **não pode ser excluído** se estiver vinculado a algum prato em `prato_ingrediente`.

---

### 4.5 Classe `Carrinho`
**Arquivo:** `admin/models/Carrinho.php`

Gerencia o carrinho de compras persistido no banco de dados (não usa sessão).

#### Métodos

| Método | Visibilidade | Descrição |
|--------|-------------|-----------|
| `adicionar(int $usuarioId, int $pratoId, int $quantidade, string $obsItem): bool` | public | Adiciona item ou soma quantidade se já existir |
| `remover(int $usuarioId, int $itemId): bool` | public | Remove item do carrinho |
| `atualizarQuantidade(int $usuarioId, int $itemId, int $quantidade): bool` | public | Altera quantidade (remove se <= 0) |
| `listarPorUsuario(int $usuarioId): array` | public | Lista itens do carrinho com detalhes do prato |
| `calcularTotal(int $usuarioId): float` | public | Soma `quantidade * preco_unit` de todos os itens |
| `limpar(int $usuarioId): bool` | public | Remove todos os itens do carrinho |
| `contarItens(int $usuarioId): int` | public | Retorna a soma de todas as quantidades |
| `obterEstadoCarrinho(int $usuarioId): array` | public | Retorna itens, total e contagem em um array |

#### Regras e Lógicas Importantes

**Adição inteligente de item (`adicionar`):**
- Busca se já existe um item com o mesmo `prato_id` **e** mesma `obs_item` (observação)
- Se existe: **soma a quantidade**
- Se não existe: **insere novo registro**
- O preço é obtido via `Prato::precoAtual()` (já considera promoção vigente)
- Se o prato não estiver disponível (preço `null`), a adição é bloqueada

**Atualização de quantidade:**
- Se a nova quantidade for `<= 0`, o item é **removido** automaticamente

---

### 4.6 Classe `Pedido`
**Arquivo:** `admin/models/Pedido.php`

Gerencia a criação e atualização de pedidos.

#### Constante

```php
public const STATUS = ['recebido', 'preparo', 'pronto', 'entregue', 'cancelado'];
```

#### Métodos

| Método | Visibilidade | Descrição |
|--------|-------------|-----------|
| `criarDoCarrinho(int $usuarioId, ?int $mesa, string $obsGeral): ?int` | public | Cria pedido do próprio carrinho do usuário |
| `criarDoCarrinhoParaUsuario(int $carrinhoUsuarioId, ?int $pedidoUsuarioId, ?int $mesa, string $obsGeral): ?int` | public | Cria pedido do carrinho de outro usuário (garçom) |
| `atualizarStatus(int $id, string $status): bool` | public | Atualiza status do pedido |
| `listar(?string $status, bool $somenteHoje): array` | public | Lista pedidos com filtros |
| `listarPorUsuario(int $usuarioId): array` | public | Pedidos de um usuário específico |
| `buscarPorId(int $id): ?array` | public | Busca pedido pelo ID |
| `itens(int $pedidoId): array` | public | Lista itens de um pedido |
| `contar(?string $status, bool $somenteHoje): int` | public | Conta pedidos por status/período |

#### Regras e Lógicas Importantes

**Criação de pedido com transação (`criarDoCarrinhoParaUsuario`):**
1. Verifica se o carrinho não está vazio — retorna `null` se estiver
2. Calcula o total do carrinho
3. Inicia uma **transação de banco de dados** (`beginTransaction`)
4. Insere o pedido com status inicial `"recebido"`
5. Insere cada item do carrinho em `itens_pedido` com o preço congelado
6. Limpa o carrinho do usuário
7. Faz `commit` — ou `rollBack` em caso de erro (`Throwable`)

**Atualização de status (`atualizarStatus`):**
- Status deve ser um dos valores válidos em `self::STATUS`
- Pedidos com status `"cancelado"` ou `"entregue"` **não podem ser alterados** (estado final)

**Dois métodos de criação:**
- `criarDoCarrinho()` — cliente finaliza o próprio pedido
- `criarDoCarrinhoParaUsuario()` — garçom finaliza o pedido de uma mesa (pode vincular a um cliente diferente ou null)

---

### 4.7 Classe `Promocao`
**Arquivo:** `admin/models/Promocao.php`

Gerencia promoções vinculadas a pratos.

#### Métodos

| Método | Visibilidade | Descrição |
|--------|-------------|-----------|
| `listar(): array` | public | Lista todas as promoções com nome do prato |
| `buscarPorId(int $id): ?array` | public | Busca promoção pelo ID |
| `cadastrar(array $dados): int` | public | Cria nova promoção |
| `editar(int $id, array $dados): bool` | public | Atualiza promoção |
| `deletar(int $id): bool` | public | Remove promoção |
| `normalizarDados(array $dados): array` | private | Valida e prepara os dados antes de salvar |

#### Regras e Lógicas

**Validação de datas (`normalizarDados`):**
- `data_inicio` e `data_fim` são obrigatórias
- `data_fim` deve ser **maior ou igual** a `data_inicio`
- Lança `InvalidArgumentException` se inválido

**Tipos de desconto:**
- `desconto_percentual`: desconta `valor`% do preço
- `desconto_fixo`: desconta `valor` em R$ do preço
- `combo`: sem alteração de preço (usado para promoções do tipo pacote)

---

### 4.8 Classe `Reserva`
**Arquivo:** `admin/models/Reserva.php`

Gerencia as reservas de mesa.

#### Constante

```php
public const STATUS = ['pendente', 'confirmada', 'cancelada'];
```

#### Propriedade

```php
private int $capacidadePorHorario; // padrão: 60 pessoas
```

#### Métodos

| Método | Visibilidade | Descrição |
|--------|-------------|-----------|
| `__construct(int $capacidadePorHorario = 60)` | public | Inicializa e já cancela reservas expiradas |
| `cadastrar(int $usuarioId, array $dados): int` | public | Cria nova reserva após verificar disponibilidade |
| `confirmar(int $id): bool` | public | Confirma uma reserva pendente |
| `cancelar(int $id, ?int $usuarioId): bool` | public | Cancela ou exclui reserva |
| `atualizarStatus(int $id, string $status): bool` | public | Atualiza status (valida enum) |
| `listar(?string $data): array` | public | Lista reservas futuras (admin) |
| `listarPorUsuario(int $usuarioId): array` | public | Reservas de um usuário |
| `buscarPorId(int $id): ?array` | public | Busca reserva pelo ID |
| `verificarDisponibilidade(...)` | private | Verifica se há vagas no horário |
| `atualizarReservasExpiradas()` | private | Cancela automaticamente reservas passadas |

#### Regras e Lógicas Importantes

**Controle de capacidade (`verificarDisponibilidade`):**
- Soma o `num_pessoas` de todas as reservas **confirmadas** no mesmo `data` e `horario`
- A nova reserva só é aceita se `(soma_atual + num_pessoas_nova) <= capacidadePorHorario` (padrão 60)
- Suporta ignorar a própria reserva ao reconfirmar (parâmetro `$ignorarReservaId`)

**Cancelamento automático (`atualizarReservasExpiradas`):**
- Executado sempre que a classe é instanciada (no construtor)
- Cancela automaticamente reservas com `status = "pendente"` cuja `data < CURDATE()`

**Cancelamento pelo cliente vs. admin:**
- Com `$usuarioId` informado: **deleta** o registro (cliente cancela a própria reserva)
- Sem `$usuarioId`: apenas **muda o status** para `"cancelada"` (ação do admin/garçom)

---

## 5. Camada de Configuração (`admin/config/`)

### 5.1 `conexao.php` — Conexão PDO (Singleton)

```php
function getConnection(): PDO
```

- Usa padrão **Singleton** com variável `static $pdo`
- Configurada via **variáveis de ambiente** (`getenv()`), com fallback para valores padrão
- Parâmetros PDO configurados:
  - `ATTR_ERRMODE` → `ERRMODE_EXCEPTION` (lança exceções em erros)
  - `ATTR_DEFAULT_FETCH_MODE` → `FETCH_ASSOC` (retorna arrays associativos)
  - `ATTR_EMULATE_PREPARES` → `false` (usa prepared statements reais)

### 5.2 `auth.php` — Autenticação e Utilitários

Contém todas as funções globais de autenticação, segurança e helpers da aplicação.

#### Funções de Autenticação

| Função | Descrição |
|--------|-----------|
| `isLoggedIn(): bool` | Verifica se há sessão ativa |
| `currentUserId(): ?int` | Retorna o ID do usuário logado |
| `currentUserTipo(): ?string` | Retorna o tipo ('admin', 'garcom', 'user') |
| `currentUser(): ?array` | Busca dados completos do usuário no banco |
| `hasRole(array\|string $roles): bool` | Verifica se o usuário tem um dos perfis informados |
| `loginUser(array $user): void` | Inicia a sessão do usuário (regenera session ID) |
| `logoutUser(): void` | Destrói a sessão e invalida o cookie |
| `requireLogin(string $redirectTo): void` | Redireciona se não estiver logado |
| `requireRole(array\|string $roles): void` | Redireciona se não tiver a permissão necessária |
| `redirectByRole(): never` | Redireciona para a tela correta conforme o perfil |

#### Funções de Segurança

| Função | Descrição |
|--------|-----------|
| `csrfToken(): string` | Gera ou retorna o token CSRF da sessão |
| `csrfField(): string` | Retorna o campo HTML hidden com o token CSRF |
| `verifyCsrfOrFail(): void` | Valida o token CSRF — encerra com HTTP 403 se inválido |
| `e(mixed $value): string` | Escapa output para HTML (`htmlspecialchars`) |

#### Funções de Controle de Acesso por Função

| Função | Descrição |
|--------|-----------|
| `canManageReservaStatus(string $status): bool` | Admin pode tudo; garçom não pode cancelar |
| `canManagePratoDisponibilidade(): bool` | Apenas admin pode alterar disponibilidade |
| `getActiveWaiterTable(): int` | Retorna a mesa ativa do garçom |
| `setActiveWaiterTable(int $table): void` | Define a mesa ativa do garçom na sessão |
| `clearActiveWaiterTable(): void` | Limpa a mesa ativa do garçom |

#### Funções Utilitárias

| Função | Descrição |
|--------|-----------|
| `flash(string $type, string $message): void` | Armazena mensagem flash na sessão |
| `consumeFlash(): array` | Lê e remove as mensagens flash |
| `redirect(string $path): never` | Redireciona e encerra a execução |
| `formatMoney(float $value): string` | Formata valor como `R$ 1.234,56` |
| `statusLabel(string $status): string` | Converte status em texto legível em PT-BR |
| `publicImagePath(?string $image): string` | Retorna caminho da imagem ou placeholder |

#### Funções de CRUD Genéricas

| Função | Descrição |
|--------|-----------|
| `handleGenericDeletion(...)` | Trata exclusão genérica com verificação de role e CSRF |
| `handleGenericPostCRUD(...)` | Trata POST de cadastro/edição genérico com mensagens flash |

#### Configuração da Sessão

A sessão é iniciada no topo do arquivo com configurações de segurança:
```php
session_set_cookie_params([
    'lifetime' => 0,         // Cookie de sessão (expira ao fechar o browser)
    'path'     => '/',
    'secure'   => $isHttps,  // HTTPS only quando em produção
    'httponly' => true,       // Inacessível via JavaScript
    'samesite' => 'Lax',     // Proteção contra CSRF cross-site
]);
```

---

## 6. Controle de Acesso por Perfil

| Recurso / Ação                       | Admin | Garçom | Cliente |
|--------------------------------------|:-----:|:------:|:-------:|
| Ver cardápio público                 | ✅    | ✅     | ✅      |
| Adicionar ao carrinho                | ✅    | ✅     | ✅      |
| Finalizar pedido                     | ✅    | ✅     | ✅      |
| Reservar mesa                        | ✅    | ❌     | ✅      |
| Dashboard administrativo             | ✅    | ✅     | ❌      |
| Gerenciar pratos                     | ✅    | ❌     | ❌      |
| Gerenciar categorias                 | ✅    | ❌     | ❌      |
| Gerenciar ingredientes               | ✅    | ❌     | ❌      |
| Gerenciar promoções                  | ✅    | ❌     | ❌      |
| Gerenciar pedidos (todos)            | ✅    | ✅     | ❌      |
| Gerenciar reservas                   | ✅    | ✅*    | ❌      |
| Cancelar reserva (admin)             | ✅    | ❌     | ❌      |
| Ver/gerenciar usuários               | ✅    | ❌     | ❌      |
| Alterar disponibilidade de prato     | ✅    | ❌     | ❌      |
| Menu especial de garçom (por mesa)   | ❌    | ✅     | ❌      |

> \* Garçom pode confirmar reservas, mas não pode cancelá-las.

---

## 7. Fluxo Principal de Pedido

```
1. Cliente acessa index.php (cardápio público)
2. Filtra por categoria ou busca por nome
3. Clica em um prato → ver_prato.php
4. Adiciona ao carrinho (Carrinho::adicionar)
   └─ Verifica disponibilidade e preço atual (com promoção se houver)
   └─ Agrupa itens iguais com mesma observação
5. Acessa carrinho.php → revisa itens e totais
6. Confirma pedido → Pedido::criarDoCarrinho()
   └─ Inicia transação no banco
   └─ Insere pedido com status "recebido"
   └─ Insere cada item com preço congelado
   └─ Limpa o carrinho
   └─ Commit
7. Admin/Garçom vê o pedido no dashboard
8. Atualiza status: recebido → preparo → pronto → entregue
```

---

## 8. Segurança Implementada

| Mecanismo | Implementação |
|-----------|---------------|
| **Senha hasheada** | `password_hash()` com `PASSWORD_DEFAULT` (bcrypt) |
| **Proteção CSRF** | Token único por sessão, verificado em todo POST |
| **Output Escaping** | Função `e()` com `htmlspecialchars` em todas as saídas HTML |
| **Prepared Statements** | Todos os queries usam `PDO::prepare()` + `execute()` |
| **Bloqueio de conta** | 5 tentativas erradas → bloqueio de 15 minutos |
| **Sessão segura** | `httponly`, `samesite=Lax`, `secure` em HTTPS |
| **Regeneração de sessão** | `session_regenerate_id(true)` ao fazer login |
| **Validação de MIME** | Upload de imagem valida extensão E conteúdo real |
| **Tipagem estrita** | `declare(strict_types=1)` em todos os arquivos PHP |
| **Autorização por rota** | `requireRole()` no topo de cada view protegida |

---

## 9. Padrões de Código Adotados

- **MVC (Model-View-Controller):** Models em `admin/models/`, Views em `admin/views/`, Controllers (ações HTTP) em `admin/controllers/`
- **Singleton para conexão:** A conexão PDO é criada uma única vez por requisição
- **Strict Types:** Todos os arquivos usam `declare(strict_types=1)` para tipagem rigorosa
- **Flash Messages:** Sistema de mensagens temporárias via sessão para feedback ao usuário após redirecionamentos
- **Transações de banco:** Operações compostas (criar pedido) usam `beginTransaction` / `commit` / `rollBack`
- **Soft-delete implícito:** Registros não são excluídos se houver dependências (ex.: prato com pedidos)
- **Queries dinâmicas:** Filtros de busca são construídos dinamicamente com `WHERE 1=1` e concatenação segura

---

*Documentação gerada em: junho de 2026*
