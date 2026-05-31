# MenuStock — Sistema de Gerenciamento de Restaurante

O **MenuStock** é um sistema web completo para gerenciamento de restaurantes, projetado com arquitetura **MVC (Model-View-Controller)** e **DAO (Data Access Object)**. O sistema possui duas interfaces principais:
1. **Vitrine Pública**: Onde os clientes visualizam o cardápio, filtram pratos por categoria, buscam pratos por ingrediente/nome, montam carrinhos de compras persistentes, fazem pedidos e solicitam reservas de mesas.
2. **Painel Administrativo**: Destinado a administradores e garçons para gestão operacional, incluindo atualização de pedidos em tempo real, controle de reservas, gerenciamento de pratos, categorias, ingredientes comuns, promoções ativas e usuários.

---

## 🚀 Tecnologias Utilizadas

* **Back-end:** PHP 8+ (Programação Orientada a Objetos, PDO)
* **Banco de Dados:** MySQL
* **Front-end:** HTML5, CSS3, JavaScript puro (Vanilla)
* **Servidor Local:** XAMPP / Apache

---

## 📁 Estrutura do Projeto

```text
menustock/
├── index.php                        # Vitrine pública do cardápio
├── login.php                        # Tela de login e cadastro de usuários
├── ver_prato.php                    # Detalhes de um prato e seus ingredientes
├── carrinho.php                     # Carrinho de compras do cliente
├── meus_pedidos.php                 # Acompanhamento de pedidos e reservas do cliente
├── reserva.php                      # Formulário de reserva de mesa
│
├── database/                        # Migrações e scripts do banco de dados
│   └── migrations/
│
└── admin/                           # Painel Administrativo
    ├── config/                      # Conexão com banco e controle de sessão/autenticação
    ├── controllers/                 # Controladores para deleções, status e logout
    ├── models/                      # Classes do Modelo (Prato, Reserva, Pedido, etc.)
    ├── views/                       # Telas administrativas (dashboard, gerenciamento)
    └── public/                      # Recursos públicos (CSS, JS, uploads de imagens)
```

---

## ⚙️ Regras de Negócio e Funcionalidades

### 1. Nível de Acesso por Domínio de E-mail
O sistema define a função do usuário automaticamente no cadastro:
* **Administrador** (`@menustock.com.br`): Acesso total a todas as configurações, relatórios financeiros e CRUDs.
* **Garçom** (`@garcom.menustock.com.br`): Acesso operacional (atualizar status de pedidos, confirmar reservas, alternar disponibilidade de pratos e usar o *Menu Garçom* para realizar pedidos em nome de clientes).
* **Cliente** (qualquer outro domínio): Acesso à vitrine, carrinho, pedidos pessoais e reservas pessoais.

### 2. Matriz de Permissões
| Ação | Cliente | Garçom | Admin |
| :--- | :---: | :---: | :---: |
| Ver cardápio e detalhes do prato | ✔ | ✔ | ✔ |
| Adicionar ao carrinho e confirmar pedido | ✔ | ✗ | ✗ |
| Criar pedido em nome de cliente (*Menu Garçom*) | ✗ | ✔ | ✗ |
| Acompanhar próprios pedidos e fazer reservas | ✔ | ✗ | ✔ |
| Confirmar reserva / atualizar status de pedido | ✗ | ✔ | ✔ |
| Cancelar reservas administrativamente | ✗ | ✗ | ✔ |
| Cadastrar, editar e deletar pratos/categorias | ✗ | ✗ | ✔ |
| Gerenciar usuários e promoções | ✗ | ✗ | ✔ |
| Ver receita financeira no Dashboard | ✗ | ✗ | ✔ |

### 3. Proteção de Dados Históricos
* **Preço Congelado**: O preço do prato é gravado no carrinho e no pedido no momento da ação. Mudanças de preço posteriores pelo administrador não afetam pedidos ou carrinhos em andamento.
* **Restrição de Exclusão**: Pratos com pedidos vinculados ou categorias com pratos cadastrados não podem ser excluídos (proteção por chave estrangeira `ON DELETE RESTRICT`). Podem ser ocultados da vitrine se o status for alterado para indisponível (`disponivel = 0`).

### 4. Controle de Reservas e Capacidade
O sistema verifica a capacidade de pessoas por faixa de horário antes de aceitar uma nova reserva. Se o limite máximo configurado for atingido no dia e horário escolhidos, o cliente é bloqueado de reservar.

### 5. Promoções Ativas
As promoções cadastradas têm validade automática no banco de dados. O desconto é calculado em tempo real através da view `vw_promocoes_ativas`, e promoções vencidas expiram e deixam de ser aplicadas automaticamente.

---

## 🛠️ Como Executar o Projeto Localmente

1. **Instale o XAMPP** (ou ambiente similar com Apache, PHP 8+ e MySQL).
2. Clone ou mova este repositório para o diretório raiz do servidor:
   ```bash
   C:\xampp\htdocs\MenuStock
   ```
3. **Configure o Banco de Dados**:
   * Crie um banco de dados MySQL chamado `menustock`.
   * Importe a estrutura e dados iniciais localizados no diretório `/database` (como `schema.sql` ou arquivo similar de dump).
   * Verifique as credenciais de conexão em `admin/config/conexao.php`.
4. **Permissões de Escrita**:
   * Garanta que a pasta `admin/public/imagens/` possui permissão de escrita para que os uploads de fotos de novos pratos funcionem corretamente.
5. Inicie os módulos **Apache** e **MySQL** no painel do XAMPP.
6. Acesse o sistema através do navegador:
   ```url
   http://localhost/MenuStock
   ```

---

## 👨‍💻 Autores e Contexto
Projeto acadêmico desenvolvido para a disciplina de **Linguagens de Programação 1** do curso de **Engenharia da Computação** — UNIFIPMoc / Afya.
* **Professor**: Evandro Júnior
