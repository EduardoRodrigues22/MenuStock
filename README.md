# MenuStock — Sistema de Gerenciamento de Restaurante

O **MenuStock** é um sistema web completo para gerenciamento de restaurantes, projetado com arquitetura **MVC (Model-View-Controller)** e **DAO (Data Access Object)**. O sistema possui duas interfaces principais:
1. **Vitrine Pública (Cardápio)**: Onde os clientes visualizam o cardápio, filtram pratos por categoria, buscam pratos, montam carrinhos de compras persistentes, fazem pedidos e solicitam reservas de mesas.
2. **Painel Administrativo**: Destinado a administradores e garçons para gestão operacional, incluindo atualização de pedidos em tempo real, controle de reservas, gerenciamento de pratos, categorias, ingredientes, promoções ativas e usuários.

---

## 🚀 Tecnologias Utilizadas

* **Back-end:** PHP 8+ (Programação Orientada a Objetos, PDO)
* **Banco de Dados:** MySQL / MariaDB
* **Front-end:** HTML5, CSS3 (com layout responsivo e moderno), JavaScript puro (Vanilla)
* **Servidor Local:** XAMPP / Apache

---

## 📁 Estrutura do Projeto

```text
menustock/
├── index.php                        # Cardápio (vitrine pública) do restaurante
├── login.php                        # Tela de login e cadastro de usuários
├── ver_prato.php                    # Detalhes de um prato e seus ingredientes
├── carrinho.php                     # Carrinho de compras do cliente
├── meus_pedidos.php                 # Acompanhamento de pedidos e reservas do cliente
├── reserva.php                      # Formulário de reserva de mesa
├── banco de dados.sql               # Script SQL de estrutura e dados iniciais do banco
│
└── admin/                           # Painel Administrativo
    ├── config/                      # Conexão com banco, controle de sessão e regras de acesso
    ├── controllers/                 # Controladores das operações do sistema (CRUD, status)
    ├── models/                      # Classes do Modelo (Prato, Reserva, Pedido, Categoria, etc.)
    ├── views/                       # Telas administrativas (dashboard, listas de gestão)
    └── public/                      # Recursos estáticos (CSS, JS, uploads de imagens)
```

---

## ⚙️ Regras de Negócio e Funcionalidades

### 1. Nível de Acesso por Domínio de E-mail
O sistema define a função do usuário automaticamente com base no e-mail cadastrado:
* **Administrador** (`@menustock.com.br`): Acesso total a todas as configurações, relatórios financeiros e CRUDs completas.
* **Garçom** (`@garcom.menustock.com.br`): Acesso simplificado e operacional (atualizar status de pedidos, confirmar reservas, usar o *Menu* para realizar pedidos em nome de clientes e gerenciar a lista simplificada de pratos).
* **Cliente** (qualquer outro domínio): Acesso à vitrine, carrinho de compras e acompanhamento de pedidos/reservas pessoais.

### 2. Matriz de Permissões
| Ação | Cliente | Garçom | Admin |
| :--- | :---: | :---: | :---: |
| Ver cardápio e detalhes do prato | ✔ | ✔ | ✔ |
| Adicionar ao carrinho e confirmar pedido | ✔ | ✗ | ✗ |
| Criar pedido em nome de cliente (*Menu*) | ✗ | ✔ | ✗ |
| Acompanhar próprios pedidos e fazer reservas | ✔ | ✗ | ✔ |
| Confirmar reserva / atualizar status de pedido | ✗ | ✔ | ✔ |
| Cancelar reservas administrativamente | ✗ | ✗ | ✔ |
| Cadastrar, editar e deletar pratos/categorias | ✗ | ✗ | ✔ |
| Gerenciar usuários e promoções | ✗ | ✗ | ✔ |
| Ver receita financeira no Dashboard | ✗ | ✗ | ✔ |

### 3. Ajustes Específicos para Perfil de Garçom
Para melhorar o foco operacional e a produtividade, o painel do garçom possui as seguintes restrições:
* **Navegação Exclusiva**: Quando logado como garçom, o link na navbar da vitrine pública redireciona diretamente para o **Menu** administrativo (`menu_garcom.php`) em vez do `index.php`. O botão de "Reservar mesa" também fica oculto na home.
* **Atribuição Automática de Mesa**: No painel do garçom, a mesa ativa é atribuída automaticamente ao pedido no momento da seleção, sem necessidade de confirmação manual.
* **Listagem de Pratos Simplificada**: O garçom não visualiza as colunas de *Categoria* e *Ações (Operacional)*, nem as descrições dos pratos na listagem.

### 4. Gestão de Reservas em Cartões (Responsivo)
* **Visualização Moderna**: A listagem de reservas foi reformulada de tabelas lineares para um sistema de cartões responsivos (`order-card`), idêntico ao painel de pedidos.
* **Compactação Inteligente**: As datas são exibidas de forma simplificada (`dia/mês`), a coluna de Observação foi encurtada para `Obs.`, e os dados curtos são centralizados na tabela interna de detalhes.
* **Quebra de Linha Automática**: As tabelas de detalhes internas possuem quebra de linha inteligente para evitar barras de rolagem horizontal em telas móveis.
* **Filtro de Datas Futuras**: O painel exibe apenas reservas da data atual em diante (`r.data >= CURDATE()`), e o seletor de calendário do filtro impede a escolha de datas passadas.

### 5. Proteção de Dados Históricos
* **Preço Congelado**: O preço do prato é gravado no carrinho e no pedido no momento da ação. Mudanças de preço posteriores pelo administrador não afetam pedidos ou carrinhos em andamento.
* **Restrição de Exclusão**: Pratos com pedidos vinculados ou categorias com pratos cadastrados não podem ser excluídos (proteção por chave estrangeira `ON DELETE RESTRICT`). Podem ser ocultados da vitrine se o status for alterado para indisponível (`disponivel = 0`).

---

## 🛠️ Como Executar o Projeto Localmente

1. **Instale o XAMPP** (ou ambiente similar contendo Apache, PHP 8+ e MySQL/MariaDB).
2. Mova ou clone esta pasta para o diretório de hospedagem do Apache:
   ```bash
   C:\xampp\htdocs\MenuStock
   ```
3. **Configure o Banco de Dados**:
   * Acesse o phpMyAdmin (`http://localhost/phpmyadmin`).
   * Crie um banco de dados chamado `menustock`.
   * Importe a estrutura e dados iniciais utilizando o arquivo **`banco de dados.sql`** localizado na raiz do projeto.
   * Caso necessário, ajuste as credenciais de acesso em `admin/config/conexao.php`.
4. **Permissões de Escrita**:
   * Garanta que a pasta `admin/public/imagens/` possui permissão de leitura e escrita para o envio de imagens de novos pratos.
5. Inicie o **Apache** e o **MySQL** no Painel de Controle do XAMPP.
6. Abra seu navegador e acesse o sistema:
   ```url
   http://localhost/MenuStock
   ```

---

## 👨‍💻 Autores e Contexto
Projeto acadêmico desenvolvido para a disciplina de **Linguagens de Programação 1** do curso de **Engenharia da Computação** — UNIFIPMoc / Afya.
* **Professor**: Evandro Júnior.
* **Acadêmico**: Carlos Eduardo Rodrigues Brito.