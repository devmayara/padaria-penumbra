# 🥖 Padaria Penumbra - Sistema de Gestão

Sistema completo de gestão para padaria com controle de usuários, produtos, estoque, pedidos e fichas (tickets).

## 🚀 Setup do Projeto

### Pré-requisitos
- PHP 8.2+
- Composer 2.0+
- Node.js 18+ (para assets)
- SQLite (configurado por padrão)

### Instalação

1. **Clone o repositório**
```bash
git clone <repository-url>
cd padaria-penumbra
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Instale as dependências Node.js**
```bash
npm install
```

4. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados**
```bash
touch database/database.sqlitetouch database/database.sqlite
```

6. **Execute as migrations e seeders**
```bash
php artisan migrate:fresh --seed
```

7. **Crie o link simbólico para storage**
```bash
php artisan storage:link
```

8. **Compile os assets**
```bash
npm run build
```

9. **Inicie o servidor**
```bash
php artisan serve
```

O sistema estará disponível em `http://localhost:8000`

## 🔐 Credenciais Seed

### Usuário Administrador
- **Email:** `admin@padaria.test`
- **Senha:** `password`
- **Perfil:** `admin`

## 📊 Fluxos de Negócio

### Sistema de Pedidos

1. **Cliente (Member)**
   - Acessa o marketplace (`/member/marketplace`)
   - Visualiza produtos disponíveis
   - Adiciona produtos ao carrinho
   - Cria pedido (`/member/my-orders/create`)
   - Acompanha status do pedido (`/member/my-orders`)
   - Pode cancelar pedidos pendentes

2. **Administrador**
   - Gerencia todos os pedidos (`/admin/orders`)
   - Altera status dos pedidos
   - Visualiza histórico completo
   - Gera relatórios de vendas

### Gestão de Estoque

#### Movimentações de Estoque

O sistema registra automaticamente todas as movimentações de estoque:

1. **Entrada de Estoque**
   - Ao criar produto com quantidade inicial
   - Ao registrar entrada manual via admin
   - Tipo: `entrada`
   - Registra: produto, quantidade, preço unitário, usuário responsável

2. **Saída de Estoque**
   - Quando pedido é marcado como "pago"
   - Sistema decrementa automaticamente a quantidade
   - Tipo: `saída`
   - Registra: produto, quantidade, motivo "Venda"

3. **Estorno de Estoque**
   - Quando pedido é cancelado
   - Sistema incrementa automaticamente a quantidade
   - Tipo: `entrada`
   - Registra: produto, quantidade, motivo "Estorno por cancelamento"

### Sistema de Fichas (Tickets)

1. **Geração Automática**
   - Ficha é criada automaticamente ao criar pedido
   - Código único gerado para cada ficha
   - QR Code incluído para rastreamento

2. **Funcionalidades**
   - Download em PDF
   - Impressão
   - Regeneração (apenas admin)

## 🏗️ Estrutura do Projeto

### Rotas Organizadas

- **`/admin/*`** - Rotas administrativas (requer perfil admin)
- **`/member/*`** - Rotas para clientes (requer perfil member)
- **`/`** - Rotas públicas e dashboard

### Middlewares de Segurança

- `auth` - Verifica autenticação
- `verified` - Verifica email verificado
- `user.active` - Verifica se usuário está ativo
- `admin` - Verifica perfil administrativo
- `member` - Verifica perfil de cliente

### Controllers Principais

- `UserController` - Gestão de usuários
- `ProductController` - Gestão de produtos
- `CategoryController` - Gestão de categorias
- `OrderController` - Gestão de pedidos
- `StockMovementController` - Controle de estoque
- `TicketController` - Geração de fichas
- `ReportController` - Relatórios

## 🔧 Comandos Artisan Úteis

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Listar rotas
php artisan route:list

# Verificar status das migrations
php artisan migrate:status

# Acessar Tinker para debug
php artisan tinker
```

## 📁 Estrutura de Arquivos Importantes

```
padaria-penumbra/
├── app/
│   ├── Http/Controllers/     # Controllers da aplicação
│   ├── Http/Middleware/      # Middlewares customizados
│   ├── Models/               # Modelos Eloquent
│   ├── Providers/           # Service Providers
├── database/
│   ├── migrations/           # Migrations do banco
│   └── seeders/              # Seeders iniciais
├── resources/
│   └── views/                # Views Blade
├── routes/                   # Definição de rotas
└── storage/                  # Arquivos de upload e cache
```

## 🚨 Tratamento de Erros

### Páginas de Erro Customizadas

- **403** - Acesso negado (`resources/views/errors/403.blade.php`)
- **404** - Página não encontrada (`resources/views/errors/404.blade.php`)
- **500** - Erro interno (`resources/views/errors/500.blade.php`)

### Tratamento de Exceções

- Usuários não autenticados são redirecionados para login
- Usuários sem permissão veem página 403
- Rotas inexistentes retornam 404

## 🔒 Segurança

- **CSRF Protection** habilitado em todos os formulários
- **Rate Limiting** no login (6 tentativas por minuto)
- **Policies** para controle de acesso
- **Soft Deletes** para dados importantes
- **Validação** em todos os inputs via FormRequests

## 📱 Responsividade

O sistema é totalmente responsivo e funciona em:
- Desktop
- Tablet
- Mobile



## 📞 Suporte

Para dúvidas ou problemas:
- Verifique os logs em `storage/logs/laravel.log`
- Consulte a documentação das rotas com `php artisan route:list`
- Use o Tinker para debug: `php artisan tinker`

## 📄 Licença

Este projeto é privado e de uso exclusivo da Padaria Penumbra.

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
