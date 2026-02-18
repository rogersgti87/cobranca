# Estrutura Atual do Sistema - CobrançaSegura

## Visão Geral
Sistema Laravel 9 de gestão de cobranças e contas a pagar/receber.

## Tecnologias
- **Framework**: Laravel 9.x
- **PHP**: 8.3
- **Banco de Dados**: MySQL
- **Autenticação**: Laravel Auth + JWT (php-open-source-saver/jwt-auth)
- **Integrações**: 
  - Gateways: PagHiper, Mercado Pago, Banco Inter, Asaas
  - WhatsApp: Evolution API
  - Email: Brevo (SendInBlue)
  - QR Code: chillerlan/php-qrcode, endroid/qr-code

## Arquitetura Atual

### Models Principais

| Model | Tabela | Descrição | Relações |
|-------|--------|-----------|----------|
| User | users | Usuários do sistema | - |
| Customer | customers | Clientes | Pertence a User |
| Service | services | Serviços oferecidos | Pertence a User |
| CustomerService | customer_services | Vincula cliente a serviço | Cliente + Serviço |
| Invoice | invoices | Contas a receber/Faturas | Pertence a User, Customer, CustomerService |
| InvoiceNotification | invoice_notifications | Notificações de faturas | Pertence a Invoice |
| Payable | payables | Contas a pagar | Pertence a User, Supplier, Category |
| PayableCategory | payable_categories | Categorias de despesas | Pertence a User |
| PayableReversal | payable_reversals | Estornos | Pertence a Payable |
| Supplier | suppliers | Fornecedores | Pertence a User |

### Estrutura de Tabelas

#### Tabela: users
- Armazena usuários do sistema
- Contém configurações de integrações (PagHiper, Mercado Pago, Inter, Asaas, WhatsApp)
- Campos principais: id, name, email, password, status, document, company
- **Observação**: Campo `company` armazena nome da empresa (texto livre)

#### Tabela: customers
- Armazena clientes dos usuários
- user_id: Relaciona com usuário
- Tipo: Física ou Jurídica
- Campos de endereço completo
- Preferências de notificação (email, whatsapp)
- Gateways de pagamento configurados

#### Tabela: services
- Serviços oferecidos pelo usuário
- user_id: Relaciona com usuário
- Campos: name, price, status

#### Tabela: customer_services
- Vincula cliente a serviço (assinatura)
- user_id, customer_id, service_id
- Configurações: day_due, price, period, gateway_payment, payment_method
- Data início/fim cobrança
- Taxa adicional (tax)

#### Tabela: invoices (Contas a Receber)
- Faturas geradas para clientes
- user_id, customer_id, customer_service_id
- Status: Pendente, Processamento, Pago, Cancelado, Erro, Gerando, Estabelecimento
- Métodos de pagamento: Pix, Boleto, Cartão, Dinheiro, Depósito
- Campos de integração: transaction_id, image_url_pix, pix_digitable, qrcode_pix_base64, billet_url, billet_digitable

#### Tabela: invoice_notifications
- Histórico de notificações enviadas
- user_id, invoice_id
- Tipo: Email ou WhatsApp
- Status e mensagens

#### Tabela: payables (Contas a Pagar)
- Despesas do usuário
- user_id, supplier_id, category_id
- Tipos: Fixa, Recorrente, Parcelada
- Status: Pendente, Pago, Cancelado
- Recorrência: período, dia, data fim
- Parcelamento: installments, installment_number, parent_id

#### Tabela: payable_categories
- Categorias de despesas
- user_id, name, color

#### Tabela: payable_reversals
- Estornos de contas pagas
- payable_id, user_id
- Motivo e data do estorno

#### Tabela: suppliers
- Fornecedores
- user_id
- Dados cadastrais completos

#### Tabela: view_invoices
- VIEW SQL que agrega dados de invoices, customers e users
- Facilita consultas complexas

### Controllers Principais

#### Admin Controllers
- **InvoiceController**: CRUD de faturas, relatórios, notificações
- **PayableController**: CRUD de contas a pagar, estornos, parcelamento
- **CustomerController**: CRUD de clientes
- **ServiceController**: CRUD de serviços
- **CustomerServiceController**: CRUD de assinaturas
- **SupplierController**: CRUD de fornecedores
- **PayableCategoryController**: CRUD de categorias
- **UserController**: Configurações do usuário, integrações
- **AdminController**: Dashboard, relatórios, gráficos

#### API Controllers
- **AuthController**: Login, Register, Logout (JWT)
- **InvoiceController**: Listagem, notificação, verificação de faturas

### Commands (Cron Jobs)

| Command | Frequência | Descrição |
|---------|-----------|-----------|
| CreateInvoiceCron | 2x/dia (1h, 4h) | Cria faturas recorrentes a partir de customer_services |
| GenerateInvoiceCron | A cada minuto | Gera PIX/Boletos para faturas em status "Gerando" |
| RememberInvoiceCron | 2x/dia (9h, 14h) | Envia lembretes de cobrança (5 dias antes, 2 dias antes, vencimento, vencido) |
| TokenInterCron | A cada hora | Renova token do Banco Inter |
| StatusInterCron | A cada 30 min | Verifica status de pagamentos no Inter |
| GenerateRecurringPayables | A cada hora | Cria contas a pagar recorrentes |

### Rotas

#### Web Routes
- `/`: Home pública
- `/admin/*`: Área administrativa (protegida por auth)
- `/webhook/*`: Webhooks dos gateways

#### API Routes
- `/api/login`, `/api/register`: Autenticação JWT
- `/api/invoices`: Listagem de faturas
- `/api/invoices/notify`: Notificar cliente
- `/api/invoices/check`: Verificar status da fatura

### Fluxo de Cobrança

1. **Criação de Assinatura**: CustomerService vincula cliente a serviço
2. **Geração Automática**: CreateInvoiceCron cria faturas baseadas nas assinaturas
3. **Processamento**: GenerateInvoiceCron gera PIX/Boleto nos gateways
4. **Notificação**: RememberInvoiceCron envia lembretes por email/WhatsApp
5. **Confirmação**: Webhooks ou StatusInterCron confirmam pagamento

### Fluxo de Contas a Pagar

1. **Criação Manual**: Usuário cria conta a pagar
2. **Recorrência**: GenerateRecurringPayables cria automaticamente contas recorrentes
3. **Pagamento**: Usuário marca como paga
4. **Estorno**: Possibilidade de estornar pagamento (PayableReversal)

## Limitações Atuais

### Sem Multiempresa
- Cada usuário gerencia apenas sua empresa
- Campo `company` em users é texto livre (não é relacional)
- Impossibilidade de um usuário gerenciar múltiplas empresas
- Todos os dados (customers, invoices, payables) são filtrados apenas por user_id

### Impactos
- Usuários com múltiplas empresas precisam criar múltiplas contas
- Relatórios consolidados entre empresas são impossíveis
- Configurações de gateway precisam ser duplicadas
- Commands enviam notificações sem distinção de empresa

## Próximos Passos

Para implementar multiempresa, será necessário:

1. Criar tabela `companies`
2. Criar tabela pivot `company_user`
3. Adicionar `company_id` em todas as tabelas relevantes
4. Criar sistema de seleção de empresa ativa
5. Adaptar todos os Controllers e Commands
6. Migrar dados existentes
