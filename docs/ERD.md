# ERD — Comercializadora de Metales Preciosos "Orbeg Capital"
# Documento de referencia central e inamovible
# Fecha de creacion: 2026-02-23
# Cualquier cambio debe marcarse con: PROPUESTA DE CAMBIO EN ERD: [razon]

---

## CONVENCIONES GENERALES

- Todos los campos de peso usan `decimal(15,4)` en gramos como unidad base.
- Todos los campos monetarios usan `decimal(15,4)`.
- La conversion a onzas troy es calculada: `peso_gramos / 31.1035`.
- Todas las tablas incluyen `id` (bigint unsigned auto_increment), `created_at`, `updated_at`.
- Soft deletes (`deleted_at`) solo en entidades maestras (clients, suppliers, metals, users).
- Moneda por defecto: USD. Campo `currency` donde aplique para soporte multi-moneda futuro.

---

## 1. users

Usuarios del sistema. Autenticacion via Laravel Breeze + Sanctum.
Roles asignados via Spatie: administrador, operador, auditor.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| name                 | varchar(150)      | NOT NULL                          |
| email                | varchar(255)      | NOT NULL, UNIQUE                  |
| email_verified_at    | timestamp         | NULLABLE                          |
| password             | varchar(255)      | NOT NULL                          |
| phone                | varchar(20)       | NULLABLE                          |
| is_active            | boolean           | NOT NULL, DEFAULT true            |
| remember_token       | varchar(100)      | NULLABLE                          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |
| deleted_at           | timestamp         | NULLABLE (soft delete)            |

**Relaciones:**
- hasMany: purchases, sales, settlements, shipments, audit_logs, metal_prices (como created_by)

---

## 2. clients

Clientes a quienes se les VENDE metal precioso.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| document_type        | enum              | 'RUC','DNI','CE','PASAPORTE'      |
| document_number      | varchar(20)       | NOT NULL, UNIQUE                  |
| business_name        | varchar(255)      | NOT NULL (razon social)           |
| trade_name           | varchar(255)      | NULLABLE (nombre comercial)       |
| email                | varchar(255)      | NULLABLE                          |
| phone                | varchar(20)       | NULLABLE                          |
| address              | varchar(500)      | NULLABLE                          |
| city                 | varchar(100)      | NULLABLE                          |
| state                | varchar(100)      | NULLABLE                          |
| country              | varchar(100)      | NOT NULL, DEFAULT 'Peru'          |
| contact_person       | varchar(150)      | NULLABLE                          |
| contact_phone        | varchar(20)       | NULLABLE                          |
| notes                | text              | NULLABLE                          |
| is_active            | boolean           | NOT NULL, DEFAULT true            |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |
| deleted_at           | timestamp         | NULLABLE (soft delete)            |

**Relaciones:**
- hasMany: sales

---

## 3. suppliers

Proveedores de quienes se COMPRA metal precioso.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| document_type        | enum              | 'RUC','DNI','CE','PASAPORTE'      |
| document_number      | varchar(20)       | NOT NULL, UNIQUE                  |
| business_name        | varchar(255)      | NOT NULL                          |
| trade_name           | varchar(255)      | NULLABLE                          |
| email                | varchar(255)      | NULLABLE                          |
| phone                | varchar(20)       | NULLABLE                          |
| address              | varchar(500)      | NULLABLE                          |
| city                 | varchar(100)      | NULLABLE                          |
| state                | varchar(100)      | NULLABLE                          |
| country              | varchar(100)      | NOT NULL, DEFAULT 'Peru'          |
| contact_person       | varchar(150)      | NULLABLE                          |
| contact_phone        | varchar(20)       | NULLABLE                          |
| bank_name            | varchar(150)      | NULLABLE                          |
| bank_account_number  | varchar(30)       | NULLABLE                          |
| bank_cci             | varchar(30)       | NULLABLE (codigo interbancario)   |
| notes                | text              | NULLABLE                          |
| is_active            | boolean           | NOT NULL, DEFAULT true            |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |
| deleted_at           | timestamp         | NULLABLE (soft delete)            |

**Relaciones:**
- hasMany: purchases

---

## 4. metals

Catalogo de metales preciosos gestionados.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| name                 | varchar(50)       | NOT NULL, UNIQUE (Oro, Plata...) |
| symbol               | varchar(5)        | NOT NULL, UNIQUE (Au, Ag, Pt)    |
| description          | varchar(255)      | NULLABLE                          |
| is_active            | boolean           | NOT NULL, DEFAULT true            |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |
| deleted_at           | timestamp         | NULLABLE (soft delete)            |

**Relaciones:**
- hasMany: metal_prices, purchase_items, sale_items, inventory_movements

---

## 5. metal_prices

Registro historico de precios por metal. Permite consultar precio vigente y auditar variaciones.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| metal_id             | bigint unsigned   | FK -> metals.id, NOT NULL         |
| price_per_gram       | decimal(15,4)     | NOT NULL                          |
| price_per_troy_ounce | decimal(15,4)     | NOT NULL                          |
| currency             | varchar(3)        | NOT NULL, DEFAULT 'USD'           |
| effective_date       | date              | NOT NULL                          |
| source               | varchar(100)      | NULLABLE (referencia/bolsa)       |
| created_by           | bigint unsigned   | FK -> users.id, NOT NULL          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Indices:**
- UNIQUE compuesto: (metal_id, currency, effective_date)

**Relaciones:**
- belongsTo: metal, user (created_by)

---

## 6. purchases (Compras)

Transacciones de compra de metal a proveedores.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| purchase_number      | varchar(20)       | NOT NULL, UNIQUE (auto: CMP-XXXX) |
| supplier_id          | bigint unsigned   | FK -> suppliers.id, NOT NULL      |
| user_id              | bigint unsigned   | FK -> users.id, NOT NULL          |
| purchase_date        | date              | NOT NULL                          |
| status               | enum              | 'borrador','confirmada','liquidada','cancelada' NOT NULL, DEFAULT 'borrador' |
| subtotal             | decimal(15,4)     | NOT NULL, DEFAULT 0               |
| tax_percentage       | decimal(5,2)      | NOT NULL, DEFAULT 0               |
| tax_amount           | decimal(15,4)     | NOT NULL, DEFAULT 0               |
| total                | decimal(15,4)     | NOT NULL, DEFAULT 0               |
| currency             | varchar(3)        | NOT NULL, DEFAULT 'USD'           |
| notes                | text              | NULLABLE                          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Relaciones:**
- belongsTo: supplier, user
- hasMany: purchase_items
- morphMany: settlements, shipments

---

## 7. purchase_items

Lineas de detalle de cada compra.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| purchase_id          | bigint unsigned   | FK -> purchases.id, NOT NULL, CASCADE |
| metal_id             | bigint unsigned   | FK -> metals.id, NOT NULL         |
| description          | varchar(255)      | NULLABLE (descripcion adicional)  |
| weight_grams         | decimal(15,4)     | NOT NULL                          |
| purity               | decimal(5,4)      | NOT NULL (ej: 0.9999 = 99.99%)   |
| price_per_gram       | decimal(15,4)     | NOT NULL                          |
| subtotal             | decimal(15,4)     | NOT NULL                          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Calculo:**
- `subtotal = weight_grams * purity * price_per_gram`
- `weight_troy_ounces` se calcula en runtime: `weight_grams / 31.1035`

**Relaciones:**
- belongsTo: purchase, metal

---

## 8. sales (Ventas)

Transacciones de venta de metal a clientes.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| sale_number          | varchar(20)       | NOT NULL, UNIQUE (auto: VNT-XXXX) |
| client_id            | bigint unsigned   | FK -> clients.id, NOT NULL        |
| user_id              | bigint unsigned   | FK -> users.id, NOT NULL          |
| sale_date            | date              | NOT NULL                          |
| status               | enum              | 'borrador','confirmada','liquidada','cancelada' NOT NULL, DEFAULT 'borrador' |
| subtotal             | decimal(15,4)     | NOT NULL, DEFAULT 0               |
| tax_percentage       | decimal(5,2)      | NOT NULL, DEFAULT 0               |
| tax_amount           | decimal(15,4)     | NOT NULL, DEFAULT 0               |
| total                | decimal(15,4)     | NOT NULL, DEFAULT 0               |
| currency             | varchar(3)        | NOT NULL, DEFAULT 'USD'           |
| notes                | text              | NULLABLE                          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Relaciones:**
- belongsTo: client, user
- hasMany: sale_items
- morphMany: settlements, shipments

---

## 9. sale_items

Lineas de detalle de cada venta.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| sale_id              | bigint unsigned   | FK -> sales.id, NOT NULL, CASCADE |
| metal_id             | bigint unsigned   | FK -> metals.id, NOT NULL         |
| description          | varchar(255)      | NULLABLE                          |
| weight_grams         | decimal(15,4)     | NOT NULL                          |
| purity               | decimal(5,4)      | NOT NULL                          |
| price_per_gram       | decimal(15,4)     | NOT NULL                          |
| subtotal             | decimal(15,4)     | NOT NULL                          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Calculo:**
- `subtotal = weight_grams * purity * price_per_gram`

**Relaciones:**
- belongsTo: sale, metal

---

## 10. settlements (Liquidaciones)

Documento de liquidacion financiera. Polimorficas: aplican a compras o ventas.
Generan PDF via barryvdh/laravel-dompdf.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| settlement_number    | varchar(20)       | NOT NULL, UNIQUE (auto: LIQ-XXXX) |
| settleable_type      | varchar(255)      | NOT NULL (morph: Purchase o Sale)  |
| settleable_id        | bigint unsigned   | NOT NULL                          |
| user_id              | bigint unsigned   | FK -> users.id, NOT NULL          |
| settlement_date      | date              | NOT NULL                          |
| total_amount         | decimal(15,4)     | NOT NULL                          |
| currency             | varchar(3)        | NOT NULL, DEFAULT 'USD'           |
| status               | enum              | 'pendiente','parcial','completada','anulada' NOT NULL, DEFAULT 'pendiente' |
| pdf_path             | varchar(500)      | NULLABLE                          |
| notes                | text              | NULLABLE                          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Indices:**
- INDEX compuesto: (settleable_type, settleable_id)

**Relaciones:**
- belongsTo: user
- morphTo: settleable (Purchase o Sale)
- hasMany: settlement_payments

---

## 11. settlement_payments (Pagos de Liquidacion)

Registro de pagos parciales o totales contra una liquidacion.
Permite multiples formas de pago por liquidacion.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| settlement_id        | bigint unsigned   | FK -> settlements.id, NOT NULL, CASCADE |
| payment_method       | enum              | 'efectivo','transferencia','cheque','otro' NOT NULL |
| amount               | decimal(15,4)     | NOT NULL                          |
| currency             | varchar(3)        | NOT NULL, DEFAULT 'USD'           |
| reference_number     | varchar(100)      | NULLABLE (nro transferencia/cheque) |
| payment_date         | date              | NOT NULL                          |
| notes                | text              | NULLABLE                          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Relaciones:**
- belongsTo: settlement

---

## 12. inventory_movements (Movimientos de Inventario)

Registro de entradas y salidas de metal del inventario.
Cada compra confirmada genera entrada(s), cada venta confirmada genera salida(s).

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| metal_id             | bigint unsigned   | FK -> metals.id, NOT NULL         |
| movementable_type    | varchar(255)      | NOT NULL (morph: PurchaseItem o SaleItem) |
| movementable_id      | bigint unsigned   | NOT NULL                          |
| type                 | enum              | 'entrada','salida','ajuste' NOT NULL |
| weight_grams         | decimal(15,4)     | NOT NULL                          |
| purity               | decimal(5,4)      | NOT NULL                          |
| reference            | varchar(100)      | NULLABLE (nro de compra/venta)    |
| notes                | text              | NULLABLE                          |
| user_id              | bigint unsigned   | FK -> users.id, NOT NULL          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Indices:**
- INDEX compuesto: (movementable_type, movementable_id)
- INDEX: (metal_id, type)

**Relaciones:**
- belongsTo: metal, user
- morphTo: movementable (PurchaseItem o SaleItem)

---

## 13. shipments (Envios)

Gestion logistica de envios. Polimorficas: aplican a compras o ventas.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| shippable_type       | varchar(255)      | NOT NULL (morph: Purchase o Sale)  |
| shippable_id         | bigint unsigned   | NOT NULL                          |
| user_id              | bigint unsigned   | FK -> users.id, NOT NULL          |
| tracking_number      | varchar(100)      | NULLABLE                          |
| carrier              | varchar(150)      | NULLABLE (empresa transportista)  |
| origin_address       | varchar(500)      | NOT NULL                          |
| destination_address  | varchar(500)      | NOT NULL                          |
| shipped_date         | date              | NULLABLE                          |
| estimated_delivery   | date              | NULLABLE                          |
| actual_delivery      | date              | NULLABLE                          |
| status               | enum              | 'preparando','en_transito','entregado','cancelado' NOT NULL, DEFAULT 'preparando' |
| total_weight_grams   | decimal(15,4)     | NULLABLE                          |
| notes                | text              | NULLABLE                          |
| created_at           | timestamp         | NULLABLE                          |
| updated_at           | timestamp         | NULLABLE                          |

**Indices:**
- INDEX compuesto: (shippable_type, shippable_id)

**Relaciones:**
- belongsTo: user
- morphTo: shippable (Purchase o Sale)

---

## 14. audit_logs

Registro de auditoria completo: quien, que, cuando, valor antes/despues.

| Columna              | Tipo              | Restricciones                     |
|----------------------|-------------------|-----------------------------------|
| id                   | bigint unsigned   | PK, auto_increment                |
| user_id              | bigint unsigned   | FK -> users.id, NULLABLE          |
| auditable_type       | varchar(255)      | NOT NULL                          |
| auditable_id         | bigint unsigned   | NOT NULL                          |
| action               | enum              | 'created','updated','deleted','restored' NOT NULL |
| old_values           | json              | NULLABLE                          |
| new_values           | json              | NULLABLE                          |
| ip_address           | varchar(45)       | NULLABLE                          |
| user_agent           | varchar(500)      | NULLABLE                          |
| created_at           | timestamp         | NULLABLE                          |

**Indices:**
- INDEX compuesto: (auditable_type, auditable_id)
- INDEX: (user_id)
- INDEX: (action)
- INDEX: (created_at)

**Relaciones:**
- belongsTo: user (nullable, para acciones del sistema)
- morphTo: auditable

---

## 15. Tablas de Spatie Permission (auto-generadas)

Estas tablas son creadas automaticamente por `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`.

- `roles` (id, name, guard_name, created_at, updated_at)
- `permissions` (id, name, guard_name, created_at, updated_at)
- `model_has_roles` (role_id, model_type, model_id)
- `model_has_permissions` (permission_id, model_type, model_id)
- `role_has_permissions` (permission_id, role_id)

**Roles iniciales del sistema:**
- `administrador` — Acceso total. Gestiona usuarios, configura precios, ve reportes.
- `operador` — Registra compras, ventas, liquidaciones y envios.
- `auditor` — Solo lectura. Acceso a reportes y audit_logs.

---

## MAPA DE RELACIONES POLIMORFICAS

| Relacion Morph     | Modelos posibles            | Usado en             |
|--------------------|-----------------------------|----------------------|
| settleable         | Purchase, Sale              | settlements          |
| shippable          | Purchase, Sale              | shipments            |
| movementable       | PurchaseItem, SaleItem      | inventory_movements  |
| auditable          | Todos los modelos           | audit_logs           |

---

## ENUMS DEL SISTEMA (para PHP 8.3 Backed Enums)

| Enum                | Valores                                              |
|---------------------|------------------------------------------------------|
| DocumentType        | RUC, DNI, CE, PASAPORTE                              |
| PurchaseStatus      | borrador, confirmada, liquidada, cancelada            |
| SaleStatus          | borrador, confirmada, liquidada, cancelada            |
| SettlementStatus    | pendiente, parcial, completada, anulada               |
| PaymentMethod       | efectivo, transferencia, cheque, otro                 |
| MovementType        | entrada, salida, ajuste                               |
| ShipmentStatus      | preparando, en_transito, entregado, cancelado         |
| AuditAction         | created, updated, deleted, restored                   |

---

## REGLAS DE NEGOCIO CRITICAS

1. **Precio historico inmutable:** Cuando se confirma una compra/venta, el `price_per_gram` se congela en los items. Cambios futuros en `metal_prices` no afectan transacciones existentes.

2. **Calculo de subtotal:** `subtotal = weight_grams * purity * price_per_gram` — siempre calculado en Backend.

3. **Conversion de unidades:** `1 onza troy = 31.1035 gramos` — constante definida en Backend, nunca hardcodeada en Frontend.

4. **Flujo de estados (compras/ventas):**
   - `borrador` -> `confirmada` -> `liquidada`
   - `borrador` -> `cancelada`
   - `confirmada` -> `cancelada` (solo si no tiene liquidacion)
   - Una vez `liquidada`, no se puede cancelar ni modificar.

5. **Inventario automatico:**
   - Compra `confirmada` -> genera `inventory_movements` tipo `entrada`.
   - Venta `confirmada` -> genera `inventory_movements` tipo `salida`.
   - No se puede confirmar venta si no hay suficiente inventario.

6. **Liquidacion unica:** Una compra/venta solo puede tener UNA liquidacion activa (no anulada).

7. **Auditoria automatica:** Toda operacion CUD (Create/Update/Delete) en modelos auditables genera registro en `audit_logs`.

---

## DIAGRAMA DE FLUJO DE ESTADOS

```
COMPRA/VENTA:
  borrador ──> confirmada ──> liquidada (estado final)
     │              │
     └──> cancelada <┘ (solo si no tiene liquidacion activa)

LIQUIDACION:
  pendiente ──> parcial ──> completada (estado final)
     │            │
     └──> anulada <┘

ENVIO:
  preparando ──> en_transito ──> entregado (estado final)
     │               │
     └──> cancelado  <┘
```
