# Sistema-DSS-de-Ventas-de-Repuestos-Automotrices-y-Facturacion-Electronica


## 👥 Squad

| Integrante |
|---|
| Josue Adhemar Mamani Huanacu |
| Josue Matias Arroyo Reynoso |
| Luis Fernando Arias Segovia |

---

## 📋 Índice

- [Contexto Estratégico](#-contexto-estratégico)
- [Árbol de Problemas](#-árbol-de-problemas)
- [Árbol de Soluciones](#-árbol-de-soluciones)
- [Objetivo SMART](#-objetivo-smart)
- [Definición del MVP](#-definición-del-mvp)
- [Arquitectura Técnica](#-arquitectura-técnica)
- [Product Backlog](#-product-backlog)
- [Historias de Usuario](#-historias-de-usuario)
- [Definition of Ready General](#-definition-of-ready-general)

---

## 📊 Contexto Estratégico

### Diagnóstico del Problema

Los negocios minoristas de repuestos automotrices gestionan sus ventas y facturación en sistemas independientes sin integración, lo que impide convertir los datos transaccionales en información analítica para la toma de decisiones.

---

## 🌳 Árbol de Problemas

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          🌿 EFECTOS (Ramas y Hojas)                         │
├──────────────────┬──────────────────┬──────────────────┬────────────────────┤
│  Sobre-stock de  │ Desabastecimiento│  Reducción de    │ Incapacidad de     │
│  Productos Hueso │ de Productos     │  rentabilidad    │ reaccionar ante    │
│  → capital       │ Estrella →       │  neta por        │ cambios en demanda │
│  inmovilizado    │ pérdida ventas   │  malas decisiones│ o márgenes         │
├──────────────────┴──────────────────┴──────────────────┴────────────────────┤
│                 + Riesgo de incumplimiento fiscal por procesos manuales      │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                        🌳 PROBLEMA CENTRAL (Tronco)                         │
│                                                                             │
│  Los negocios minoristas de repuestos automotrices no cuentan con           │
│  visibilidad financiera en tiempo real sobre la rentabilidad de su          │
│  inventario, dado que los datos de venta y facturación se generan y         │
│  almacenan de forma aislada en sistemas independientes, sin un mecanismo    │
│  que los integre y transforme en información analítica accionable para      │
│  la toma de decisiones de compra, precio y reposición.                      │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         🌱 CAUSAS (Raíces)                                  │
├──────────────────────────────────────────────────────────────────────────────┤
│  • Ventas y comprobantes fiscales gestionados en sistemas independientes     │
│    sin integración de datos entre módulos.                                  │
│  • Ausencia de un mecanismo automatizado que cruce tasa de rotación con     │
│    margen de ganancia neta por repuesto.                                    │
│  • Decisiones de compra y precio basadas en intuición, sin sustento en      │
│    datos históricos de ventas.                                              │
│  • Información de ventas no consolidada en tiempo real; los reportes        │
│    llegan con retraso cuando ya no son accionables.                         │
│  • La facturación electrónica no está conectada al inventario; solo         │
│    aporta cumplimiento fiscal, sin valor analítico para el negocio.         │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 🌱 Árbol de Soluciones

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          🏆 FINES (Ramas y Hojas)                           │
├──────────────────┬──────────────────┬──────────────────┬────────────────────┤
│  Optimización    │ Disponibilidad   │  Incremento de   │ Mayor capacidad    │
│  del capital de  │ oportuna de      │  rentabilidad    │ de respuesta ante  │
│  trabajo         │ Productos        │  neta basada en  │ cambios del        │
│  (↓ Hueso)       │ Estrella         │  datos reales    │ mercado            │
├──────────────────┴──────────────────┴──────────────────┴────────────────────┤
│         + Eliminación del riesgo fiscal mediante automatización              │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                       🎯 OBJETIVO CENTRAL (Tronco)                          │
│                                                                             │
│  Los negocios minoristas cuentan con visibilidad financiera en tiempo real  │
│  gracias a un DSS que integra ventas, facturación electrónica e inventario  │
│  en una única BD relacional, clasificando el catálogo en cuatro cuadrantes  │
│  (Estrella, Interrogante, Vaca, Hueso) y presentando los resultados en un   │
│  dashboard interactivo que sustenta decisiones de compra, precio y          │
│  reposición.                                                                │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                          🔧 MEDIOS (Raíces)                                 │
├──────────────────────────────────────────────────────────────────────────────┤
│  • Integrar facturación electrónica, ventas e inventario en una única BD    │
│    relacional MySQL con esquema híbrido transaccional-analítico (HTAP).     │
│  • Implementar el servicio DSSAnalyzer en Laravel que calcula tasa de       │
│    rotación y margen, y clasifica productos en la matriz de cuadrantes.     │
│  • Proveer un dashboard React con KPICards, StarHusoMatrix e                │
│    InvoiceStatusChart que visualizan resultados analíticos en tiempo real.  │
│  • Diseñar el modelo de datos con índices sobre created_at, product_id e    │
│    invoice_status para consultas agregadas en menos de 2 segundos.         │
│  • Aprovechar la facturación electrónica (SIN Bolivia) como fuente de datos │
│    estandarizados para alimentar el motor analítico DSS.                    │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 Objetivo SMART

Implementar dentro del plazo establecido en el cronograma de la asignatura, un **Sistema de Soporte de Decisiones (DSS)** integrado a un módulo de facturación electrónica para negocios minoristas de repuestos automotrices, capaz de clasificar automáticamente el catálogo de productos en categorías de rentabilidad (**Estrella, Interrogante, Vaca, Hueso**) mediante el cruce de la tasa de rotación y el margen de ganancia neta, con el fin de incrementar la visibilidad financiera del negocio y apoyar la toma de decisiones de compra, precio y reposición de inventario.

**Medición:** Entrega de un dashboard funcional que procese la totalidad de las transacciones registradas y clasifique el **100% del catálogo de productos activos** antes de la fecha límite de entrega del proyecto.

---

## 📦 Definición del MVP

### Cuadro Es / No Es / Hace / No Hace

| ✅ ES | ❌ NO ES |
|---|---|
| Un DSS que clasifica el catálogo en cuatro cuadrantes (Estrella, Interrogante, Vaca, Hueso) cruzando tasa de rotación y margen de ganancia neta | Un sistema contable o ERP completo que gestione la operación financiera del negocio |
| Un módulo de registro de ventas con detalles por línea que congela `unit_price` y `unit_cost` para garantizar inmutabilidad histórica | Un sistema de facturación electrónica certificado autónomamente ante el SIN |
| Un módulo de facturación electrónica integrado como fuente de datos (hash SHA-256, número SIN, timestamp) que alimenta el motor DSS | Un sistema de Machine Learning predictivo de demanda |
| Un dashboard interactivo con KPIs financieros, scatter plot matricial ApexCharts y gráfico de dona de estado de facturación | Un sistema de gestión de proveedores, logística o rutas de entrega |
| Un sistema de control de acceso RBAC con JWT via Laravel Sanctum (rol `admin` accede al DSS, rol `vendedor` solo a ventas) | Un reemplazo del proceso contable o declaración de impuestos |

| ✅ HACE | ❌ NO HACE |
|---|---|
| Registra usuarios con rol `admin`/`vendedor`, autentica con JWT y restringe el Dashboard DSS por middleware `CheckRole` | Modifica `unit_price` ni `unit_cost` de `sale_details` una vez insertados — inmutabilidad histórica garantizada por triggers |
| Gestiona el catálogo de repuestos (SKU, marca, categoría, `price`, `cost`, `stock`) y calcula margen de contribución (`price − cost`) | Envía ni valida comprobantes directamente ante el SIN — se delega a `InvoiceServiceInterface` + mock |
| Registra ventas con múltiples líneas, congela precios al momento de la transacción, decrementa stock vía trigger y calcula `total_amount` | Gestiona contabilidad general, libro diario, balance ni declaración tributaria |
| Emite factura electrónica llamando al servicio SIN, persiste `invoice_number`, `invoice_xml_hash` (SHA-256) e `invoice_issued_at` | Realiza predicciones de demanda con modelos estadísticos o ML en esta fase |
| Calcula `rotation_rate` y `margin_rate` por producto y clasifica cada repuesto en uno de los cuatro cuadrantes de la matriz | Controla logística de despacho, proveedores ni cuentas por pagar |
| Construye el payload del Dashboard (`kpis + matrix + top_star + critical_huso + invoice_summary`) en `GET /api/dashboard` en < 2 segundos | Opera sin conectividad — requiere internet para el servicio SIN y tracking en tiempo real |

---

## ⚙️ Arquitectura Técnica

### Stack Tecnológico

| Capa | Tecnología |
|---|---|
| Backend API | Laravel 11 + PHP 8.3 |
| Autenticación | Laravel Sanctum (JWT) |
| Base de Datos | MySQL 8 (esquema HTAP) |
| Frontend | React + Vite |
| Gráficos | ApexCharts + react-apexcharts |
| HTTP Client | Axios |

### Modelo Relacional

```
users
  id PK · name · email UQ · password (bcrypt) · role ENUM(admin,vendedor)
  IDX(email) · IDX(role)
  │
  │ 1:N
  ▼
sales
  id PK · user_id FK→users · total_amount · invoice_status ENUM(pending,issued,error)
  invoice_number · invoice_xml_hash · invoice_issued_at
  IDX(created_at) · IDX(invoice_status) · IDX(user_id)
  │
  │ 1:N
  ▼
sale_details
  id PK · sale_id FK→sales · product_id FK→products
  quantity · unit_price (CONGELADO) · unit_cost (CONGELADO) · subtotal
  IDX(sale_id) · IDX(product_id) · IDX(product_id, created_at)

products
  id PK · sku UQ · name · brand · category · compatibility (JSON)
  price · cost · stock · is_active
  IDX(sku) · IDX(brand) · IDX(category)
```

### Triggers de Base de Datos

| Trigger | Evento | Función |
|---|---|---|
| `trg_sale_details_before_insert` | BEFORE INSERT en `sale_details` | Valida que `subtotal = quantity × unit_price` |
| `trg_sale_details_after_insert_stock` | AFTER INSERT en `sale_details` | Decrementa `stock` en `products`; rechaza si queda negativo |
| `trg_sale_details_after_insert_total` | AFTER INSERT en `sale_details` | Recalcula `total_amount` en `sales` |
| `trg_sale_details_after_delete_stock` | AFTER DELETE en `sale_details` | Restaura `stock` en cancelaciones |

### Vistas Analíticas

| Vista | Propósito |
|---|---|
| `v_product_dss_metrics` | Métricas de rotación y margen por producto para el motor DSS |
| `v_sales_invoice_summary` | Consolidado de ventas agrupado por `invoice_status` |
| `v_dss_kpis_general` | KPIs generales: ingresos, ventas, productos activos, margen global |

### Flujo MVC + DSS

```
Vista React  →  AxiosClient  →  Ruta API  →  Controller  →  Singleton  →  DSSAnalyzer  →  MySQL
     ↑                                                                                       │
     └───────────────────── JSON Response ←──────────────────────────────────────────────────┘
```

---

## 📋 Product Backlog

### Resumen MoSCoW

| ID | Historia de Usuario | Épica | SP | MoSCoW |
|:---:|---|---|:---:|:---:|
| HU-01 | Registro y Autenticación de Usuarios | 🔐 Seguridad y Acceso | 3 | 🔴 Must have |
| HU-02 | Inicio de Sesión con Token JWT | 🔐 Seguridad y Acceso | 2 | 🔴 Must have |
| HU-03 | Restricción de Acceso al Módulo DSS por Rol | 🔐 Seguridad y Acceso | 3 | 🔴 Must have |
| HU-04 | Registro de Productos con Costo y Precio | 🗂️ Catálogo de Repuestos | 3 | 🔴 Must have |
| HU-05 | Edición y Baja Lógica de Productos | 🗂️ Catálogo de Repuestos | 2 | 🟡 Should have |
| HU-06 | Búsqueda y Filtrado de Productos | 🗂️ Catálogo de Repuestos | 2 | 🟢 Could have |
| HU-07 | Registro de Venta con Múltiples Detalles | 🧾 Ventas y Facturación | 5 | 🔴 Must have |
| HU-08 | Emisión de Factura Electrónica (SIN) | 🧾 Ventas y Facturación | 5 | 🔴 Must have |
| HU-09 | Consolidado de Estado de Facturación | 🧾 Ventas y Facturación | 3 | 🔴 Must have |
| HU-10 | Cálculo de Tasa de Rotación y Margen por Producto | 📊 Motor DSS | 5 | 🔴 Must have |
| HU-11 | Clasificación Matricial Estrella / Hueso | 📊 Motor DSS | 8 | 🔴 Must have |
| HU-12 | Dashboard Interactivo con KPIs Generales | 📊 Motor DSS | 5 | 🔴 Must have |

**Total: 12 Historias de Usuario — 4 Épicas — 46 Story Points**

---

## 📖 Historias de Usuario

---

### 🔐 Épica 1 — Seguridad y Acceso (RBAC)

---

#### HU-01 · Registro y Autenticación de Usuarios
> **Épica:** Seguridad y Acceso | **SP:** 3 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **administrador del sistema**, quiero registrar usuarios del negocio con un rol asignado (`admin` o `vendedor`) y autenticarlos mediante correo y contraseña, para garantizar que solo el personal autorizado acceda al sistema y que cada acción quede vinculada a un usuario identificado.

**Criterios de Aceptación**
- [ ] El sistema acepta únicamente los roles `admin` y `vendedor`; cualquier otro valor retorna **HTTP 422** con el mensaje `'El rol indicado no es válido; valores permitidos: admin, vendedor'`.
- [ ] Si el correo ya existe en la tabla `users`, la API retorna **HTTP 409** con el mensaje `'El correo ya está registrado'`.
- [ ] La contraseña se almacena como hash bcrypt con factor de costo ≥ 12; nunca se persiste ni retorna en texto plano.
- [ ] Al registrar exitosamente, la API retorna **HTTP 201** con los campos `id`, `name`, `email` y `role` del nuevo usuario; el campo `password` está excluido del JSON.
- [ ] El campo `role` por defecto es `'vendedor'` si no se especifica en la petición.
- [ ] Los campos `name`, `email` y `password` son obligatorios; la omisión de cualquiera retorna **HTTP 422** con el nombre del campo faltante.

**Definition of Ready (DoR)**
- [ ] La migración de `users` está ejecutada con los campos: `id (PK, AI)`, `name (VARCHAR 100, NN)`, `email (VARCHAR 150, UQ, NN)`, `password (VARCHAR 255, NN)`, `role (ENUM admin/vendedor, DEFAULT vendedor)`, `created_at`, `updated_at`.
- [ ] El constraint `UNIQUE` sobre `email` está declarado y verificado en el entorno local.
- [ ] El Modelo Eloquent `User` incluye los campos en `$fillable` y `password` en `$casts` como `'hashed'`.
- [ ] La ruta `POST /api/auth/register` está registrada en `routes/api.php` sin middleware (ruta pública).
- [ ] El `StoreUserRequest` con reglas `required|string`, `email|unique:users`, `min:8`, `in:admin,vendedor` está creado y enlazado al `AuthController`.
- [ ] El squad acordó que la contraseña mínima es 8 caracteres con hash bcrypt factor 12.
- [ ] El `DatabaseSeeder` incluye al menos 1 usuario `admin` y 1 `vendedor` de prueba.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Administrador del sistema |
| **Precondición** | El administrador tiene acceso al endpoint `POST /api/auth/register` (ruta pública). |
| **Flujo Principal** | 1. El admin envía `POST /api/auth/register` con `name`, `email`, `password` y `role`. 2. `StoreUserRequest` valida que el email sea único, el rol sea válido y la contraseña cumpla la longitud mínima. 3. El sistema genera el hash bcrypt (factor 12). 4. El sistema persiste el nuevo usuario en `users` con el `role` especificado o `'vendedor'` por defecto. 5. La API retorna **HTTP 201** con `id`, `name`, `email` y `role`. |
| **Flujo Alt. A** | Email duplicado → HTTP 409 `'El correo ya está registrado'`; no se crea el registro. |
| **Flujo Alt. B** | Campo obligatorio ausente → HTTP 422 con detalle del campo que falla. |
| **Flujo Alt. C** | Role inválido → HTTP 422 `'El rol indicado no es válido; valores permitidos: admin, vendedor'`. |
| **Postcondición** | El usuario queda persistido en `users` con contraseña hasheada y puede iniciar sesión inmediatamente. |

---

#### HU-02 · Inicio de Sesión con Token JWT
> **Épica:** Seguridad y Acceso | **SP:** 2 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **usuario registrado** (`admin` o `vendedor`), quiero iniciar sesión con mi correo y contraseña y obtener un token de acceso JWT, para que el sistema me identifique en cada solicitud a la API sin necesidad de reautenticarme en cada petición.

**Criterios de Aceptación**
- [ ] Credenciales correctas retornan **HTTP 200** con `token`, `expires_in` (segundos), `id`, `name` y `role`; el campo `password` nunca aparece en la respuesta.
- [ ] Credenciales incorrectas retornan **HTTP 401** con el mensaje genérico `'Credenciales inválidas'`, sin indicar si el error es el correo o la contraseña.
- [ ] El token expira según la configuración de Laravel Sanctum definida en `.env` (`TOKEN_EXPIRATION_MINUTES`); la expiración se incluye como `expires_in` en segundos.
- [ ] El frontend almacena el token en memoria o `LocalStorage` y lo envía en cada petición protegida como `Authorization: Bearer <token>`.
- [ ] Un token expirado genera **HTTP 401** `'Token expirado o inválido'` en cualquier endpoint protegido; `AxiosClient` redirige automáticamente a `/login`.
- [ ] El endpoint `POST /api/auth/login` no requiere autenticación previa (ruta pública).

**Definition of Ready (DoR)**
- [ ] Laravel Sanctum está instalado y configurado: `config/sanctum.php` publicado, `SANCTUM_STATELESS_DOMAINS` definido en `.env`.
- [ ] La ruta `POST /api/auth/login` está registrada en `routes/api.php` sin middleware.
- [ ] El método `login()` del `AuthController` usa `Hash::check()` para validar la contraseña (no `Auth::attempt()` directamente).
- [ ] La duración del token (`TOKEN_EXPIRATION_MINUTES=60`) está documentada en `.env.example` y acordada por el squad.
- [ ] El squad acordó que el token viaja exclusivamente en el header `Authorization: Bearer` (no en cookie ni query param).
- [ ] El componente `AxiosClient` en React tiene interceptor de respuesta que detecta HTTP 401 y redirige a `/login`.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Usuario registrado (admin o vendedor) |
| **Precondición** | El usuario tiene cuenta activa en `users` con contraseña hasheada. |
| **Flujo Principal** | 1. El usuario envía `POST /api/auth/login` con `email` y `password`. 2. El sistema busca el usuario por email en `users`. 3. El sistema verifica la contraseña con `Hash::check()`. 4. El sistema genera un token personal via Sanctum: `$user->createToken('auth-token')`. 5. La API retorna **HTTP 200** con `token`, `expires_in`, `id`, `name` y `role`. |
| **Flujo Alt. A** | Credenciales inválidas → **HTTP 401** `'Credenciales inválidas'`; misma respuesta para ambos casos. |
| **Flujo Alt. B** | Token expirado en petición posterior → **HTTP 401**; el interceptor de `AxiosClient` redirige a `/login`. |
| **Postcondición** | El usuario dispone de un token JWT válido para consumir los endpoints protegidos por `auth:sanctum`. |

---

#### HU-03 · Restricción de Acceso al Módulo DSS por Rol
> **Épica:** Seguridad y Acceso | **SP:** 3 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **administrador del negocio**, quiero que solo los usuarios con rol `'admin'` puedan acceder al Dashboard analítico DSS, para proteger la información financiera sensible del negocio ante el personal de ventas.

**Criterios de Aceptación**
- [ ] `GET /api/dashboard` con token de usuario `'admin'` retorna **HTTP 200** con el payload completo del Dashboard (`kpis`, `matrix`, `top_star`, `critical_huso`, `invoice_summary`).
- [ ] `GET /api/dashboard` con token de usuario `'vendedor'` retorna **HTTP 403** con el mensaje `'Acceso restringido al módulo DSS'`.
- [ ] `GET /api/dashboard` sin token o con token expirado retorna **HTTP 401** `'Token expirado o inválido'`; el frontend redirige automáticamente a `/login`.
- [ ] El middleware `auth:sanctum` y `CheckRole` están aplicados en el grupo de rutas `/api/dashboard` antes de delegar al `DashboardController`.
- [ ] El método `hasAccessToDSS()` en el modelo `User` retorna `true` únicamente si `$this->role === 'admin'`.

**Definition of Ready (DoR)**
- [ ] HU-02 completada y verificada: el sistema emite tokens JWT válidos.
- [ ] El middleware `CheckRole` está creado en `app/Http/Middleware/CheckRole.php` y registrado en `bootstrap/app.php` con el alias `'role'`.
- [ ] El método `hasAccessToDSS()` está implementado en el modelo `User` y cubierto con test unitario.
- [ ] El grupo de rutas `/api/dashboard` tiene aplicados los middlewares `['auth:sanctum', 'role:admin']` en ese orden.
- [ ] El squad acordó los mensajes exactos: **HTTP 401** `'Token expirado o inválido'` y **HTTP 403** `'Acceso restringido al módulo DSS'`.
- [ ] El componente `DashboardPage` en React maneja el estado 401 (redirect `/login`) y 403 (pantalla de acceso denegado) de forma diferenciada.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Cualquier usuario autenticado (admin o vendedor) |
| **Precondición** | El usuario tiene un token JWT válido obtenido en HU-02. |
| **Flujo Principal** | 1. El usuario envía `GET /api/dashboard` con `Authorization: Bearer <token>`. 2. `auth:sanctum` valida la firma y vigencia del token. 3. `CheckRole` extrae el `role` del usuario y llama `hasAccessToDSS()`. 4. Si `role === 'admin'`, el request pasa al `DashboardController`. 5. `DashboardController` delega a `DSSAnalyzer::buildDashboardData()` y retorna **HTTP 200** con el payload completo. |
| **Flujo Alt. A** | Token inválido o expirado → `auth:sanctum` retorna **HTTP 401**; el frontend redirige a `/login`. |
| **Flujo Alt. B** | Token válido pero `role === 'vendedor'` → `CheckRole` retorna **HTTP 403** `'Acceso restringido al módulo DSS'`. |
| **Postcondición** | Solo usuarios con `role='admin'` acceden al Dashboard DSS; los demás reciben error controlado y diferenciado. |

---

### 🗂️ Épica 2 — Catálogo de Repuestos

---

#### HU-04 · Registro de Productos con Costo y Precio
> **Épica:** Catálogo de Repuestos | **SP:** 3 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **vendedor o administrador**, quiero registrar un repuesto con su SKU, marca, categoría, precio de venta y costo de adquisición, para que el sistema disponga de la información financiera base necesaria para calcular el margen de contribución en el motor DSS.

**Criterios de Aceptación**
- [ ] El campo `sku` es obligatorio, único (`UNIQUE`) y de hasta 50 caracteres; un SKU duplicado retorna **HTTP 409** `'El SKU indicado ya existe en el catálogo'`.
- [ ] Los campos `price` y `cost` son obligatorios y deben ser decimales positivos (`DECIMAL 10,2 > 0`); si `price < cost` la API retorna **HTTP 422** `'El precio de venta no puede ser menor al costo de adquisición'`.
- [ ] El campo `stock` no admite valores negativos; el tipo es `INT UNSIGNED` en la BD con mínimo 0.
- [ ] El método `getContributionMargin()` retorna `round(price - cost, 2)` con exactamente dos decimales.
- [ ] `brand` y `category` son campos VARCHAR obligatorios; un valor vacío retorna **HTTP 422**.
- [ ] Al crear exitosamente, la API retorna **HTTP 201** con todos los campos del producto creado incluyendo `id` y `sku`; `is_active` se inicializa en `1` automáticamente.

**Definition of Ready (DoR)**
- [ ] La migración de `products` está ejecutada con: `id (PK, AI)`, `sku (VARCHAR 50, UQ, NN)`, `name (VARCHAR 200, NN)`, `brand (VARCHAR 100, NN)`, `category (VARCHAR 100, NULL)`, `compatibility (TEXT, NULL)`, `price (DECIMAL 10,2, NN)`, `cost (DECIMAL 10,2, NN)`, `stock (INT UNSIGNED, DEFAULT 0)`, `is_active (TINYINT, DEFAULT 1)`, `created_at`, `updated_at`.
- [ ] Los índices `IDX(sku)`, `IDX(brand)` e `IDX(category)` están declarados en la migración y verificados.
- [ ] El Modelo Eloquent `Product` tiene `price` y `cost` en `$casts` como `decimal:10,2` y el método `getContributionMargin()` implementado.
- [ ] El `StoreProductRequest` tiene las reglas: `sku required|string|max:50|unique:products`, `price required|numeric|min:0.01`, `cost required|numeric|min:0.01` y la regla personalizada `price >= cost`.
- [ ] La ruta `POST /api/products` está registrada con middleware `auth:sanctum`.
- [ ] El seeder incluye al menos 8 repuestos con distintas combinaciones de rotación y margen para clasificar al menos 2 productos en cada cuadrante.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Vendedor o Administrador autenticado |
| **Precondición** | El usuario tiene token JWT válido. La tabla `products` está migrada con sus constraints e índices. |
| **Flujo Principal** | 1. El usuario envía `POST /api/products` con `sku`, `name`, `brand`, `category`, `price`, `cost`, `stock` y `compatibility`. 2. `StoreProductRequest` valida que `sku` sea único, `price ≥ cost` y todos los campos obligatorios presentes. 3. El sistema persiste el producto en `products` con `is_active=1`. 4. La API retorna **HTTP 201** con el objeto producto completo. |
| **Flujo Alt. A** | SKU duplicado → **HTTP 409** `'El SKU indicado ya existe en el catálogo'`. |
| **Flujo Alt. B** | `price < cost` → **HTTP 422** `'El precio de venta no puede ser menor al costo de adquisición'`. |
| **Flujo Alt. C** | Campo obligatorio ausente → **HTTP 422** con detalle del campo que falla. |
| **Postcondición** | El producto queda activo (`is_active=1`) en el catálogo, disponible para ventas y para el análisis DSS. |

---

#### HU-05 · Edición y Baja Lógica de Productos
> **Épica:** Catálogo de Repuestos | **SP:** 2 | **MoSCoW:** Should have

**Historia de Usuario**
> Como **administrador del negocio**, quiero actualizar el precio, costo o stock de un repuesto, o darlo de baja lógica del catálogo, para mantener el catálogo alineado con la realidad del inventario sin perder el histórico de ventas asociado.

**Criterios de Aceptación**
- [ ] La edición de `price` o `cost` persiste únicamente en `products`; los campos `unit_price` y `unit_cost` de `sale_details` vinculados nunca se modifican (inmutabilidad histórica).
- [ ] Al dar de baja (`is_active=0`), el producto desaparece del selector de ventas pero sus `sale_details` históricos permanecen intactos.
- [ ] Si se intenta editar el `sku` para que coincida con uno existente en otro producto, la API retorna **HTTP 409** `'El SKU indicado ya pertenece a otro producto'`.
- [ ] La actualización exitosa retorna **HTTP 200** con el producto actualizado; si el `id` no existe retorna **HTTP 404** `'Producto no encontrado'`.
- [ ] No se puede dar de baja un producto con `stock > 0` sin enviar el campo `force: true` en el body.

**Definition of Ready (DoR)**
- [ ] HU-04 completada: existen productos en la BD con `is_active=1`.
- [ ] Las rutas `PUT /api/products/{id}` y `DELETE /api/products/{id}` están registradas con middleware `auth:sanctum`.
- [ ] El `UpdateProductRequest` está creado con la regla `unique:products,sku,{id}` para ignorar el propio registro.
- [ ] El squad acordó que `DELETE` es baja lógica (`is_active=0`) y no eliminación física.
- [ ] La lógica de `force:true` para baja con `stock > 0` está documentada en el contrato de la API.
- [ ] Los triggers de la BD no interfieren con la actualización de `price`/`cost` en `products` — solo actúan sobre INSERT en `sale_details`.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Administrador autenticado con `role='admin'` |
| **Precondición** | El producto existe en la BD con `is_active=1`. |
| **Flujo Principal** | 1. El admin envía `PUT /api/products/{id}` con los campos a modificar. 2. `UpdateProductRequest` valida las reglas incluyendo unicidad de `sku` excluyendo el propio `id`. 3. El sistema actualiza únicamente `products`; `sale_details` no se toca. 4. La API retorna **HTTP 200** con el producto actualizado. |
| **Flujo Alt. A** | Baja lógica sin `force:true` y `stock > 0` → **HTTP 422** solicitando confirmación explícita. |
| **Flujo Alt. B** | `id` inexistente → **HTTP 404** `'Producto no encontrado'`. |
| **Flujo Alt. C** | SKU editado coincide con otro producto → **HTTP 409**. |
| **Postcondición** | El catálogo refleja la realidad actual; el histórico de ventas y los cálculos DSS anteriores permanecen intactos. |

---

#### HU-06 · Búsqueda y Filtrado de Productos
> **Épica:** Catálogo de Repuestos | **SP:** 2 | **MoSCoW:** Could have

**Historia de Usuario**
> Como **vendedor**, quiero buscar un repuesto por SKU, nombre, marca o categoría, para encontrarlo rápidamente al registrar una venta sin recorrer manualmente el catálogo completo.

**Criterios de Aceptación**
- [ ] La búsqueda por `sku` exacto retorna resultados en menos de 300 ms usando el índice `idx_products_sku`.
- [ ] La búsqueda por `name` admite coincidencia parcial (`LIKE %valor%`) y retorna los primeros 20 resultados ordenados por `name ASC`.
- [ ] El filtrado por `brand` y `category` puede combinarse con la búsqueda de texto libre.
- [ ] La respuesta incluye por producto: `id`, `sku`, `name`, `price`, `stock` y el campo calculado `low_stock_alert` (`true` si `stock = 0`).
- [ ] Solo productos con `is_active=1` aparecen en los resultados.
- [ ] Un request con todos los parámetros vacíos retorna **HTTP 422** `'Ingrese al menos un criterio de búsqueda (sku, name, brand o category)'`.

**Definition of Ready (DoR)**
- [ ] Los índices `idx_products_sku (UNIQUE)`, `idx_products_brand (INDEX)` e `idx_products_category (INDEX)` están creados y verificados con `SHOW INDEX FROM products`.
- [ ] La ruta `GET /api/products/search` está registrada con middleware `auth:sanctum` y no colisiona con `GET /api/products/{id}`.
- [ ] El squad acordó el límite de 20 resultados por página y el criterio de ordenamiento `name ASC`.
- [ ] El `ProductSearchRequest` valida que al menos uno de los parámetros (`sku`, `name`, `brand`, `category`) esté presente.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Vendedor o Administrador autenticado |
| **Precondición** | El catálogo tiene al menos un producto activo (`is_active=1`). |
| **Flujo Principal** | 1. El vendedor envía `GET /api/products/search` con al menos un parámetro. 2. El sistema ejecuta la consulta usando los índices correspondientes filtrando `is_active=1`. 3. El sistema agrega `low_stock_alert=true` a productos con `stock=0`. 4. La API retorna **HTTP 200** con la lista de hasta 20 productos coincidentes. |
| **Flujo Alt. A** | Sin resultados → **HTTP 200** con array vacío `[]` y mensaje `'Sin productos encontrados'`. |
| **Flujo Alt. B** | Todos los parámetros vacíos → **HTTP 422**. |
| **Postcondición** | El vendedor identifica el repuesto con su precio y disponibilidad de stock visible. |

---

### 🧾 Épica 3 — Ventas y Facturación Electrónica

---

#### HU-07 · Registro de Venta con Múltiples Detalles
> **Épica:** Ventas y Facturación | **SP:** 5 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **vendedor**, quiero registrar una venta compuesta de uno o más repuestos con su cantidad y precio, para que el sistema calcule el monto total automáticamente, conserve el precio y costo histórico de cada línea, y deje la venta lista para la emisión de factura electrónica.

**Criterios de Aceptación**
- [ ] Cada `SaleDetail` persiste `quantity`, `unit_price` (copiado de `products.price`) y `unit_cost` (copiado de `products.cost`) en el momento exacto de la transacción; estos valores son **inmutables** después del INSERT.
- [ ] El campo `subtotal` se calcula y persiste como `quantity × unit_price`; el trigger `trg_sale_details_before_insert` valida la consistencia antes de la inserción.
- [ ] `Sale.total_amount` equivale a la suma de todos los `SaleDetail.subtotal` de la misma venta; el trigger `trg_sale_details_after_insert_total` lo actualiza automáticamente.
- [ ] Una venta con el array `details` vacío o ausente es rechazada con **HTTP 422** `'La venta debe contener al menos un producto'`.
- [ ] El stock de cada producto se decrementa vía trigger; si el stock resultante es negativo la operación es rechazada con **HTTP 422** `'Stock insuficiente para el producto {sku}'`.
- [ ] La venta se crea con `invoice_status='pending'` por defecto; la API retorna **HTTP 201** con `id`, `total_amount` e `invoice_status`.

**Definition of Ready (DoR)**
- [ ] HU-04 completada: existen productos con `price`, `cost` y `stock > 0` en la BD.
- [ ] Los triggers `trg_sale_details_before_insert`, `trg_sale_details_after_insert_stock` y `trg_sale_details_after_insert_total` están creados y probados en el entorno local.
- [ ] Las rutas `POST /api/sales` y `GET /api/sales/{id}` están registradas con middleware `auth:sanctum`.
- [ ] El `StoreSaleRequest` valida que `details` sea un array con mínimo 1 elemento y que cada `product_id` exista en `products` con `is_active=1`.
- [ ] El squad acordó que el vendedor no puede modificar `unit_price` manualmente; siempre se copia de `products.price`.
- [ ] El `SaleService` envuelve la creación en `DB::transaction()` para garantizar que falle todo o nada.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Vendedor autenticado |
| **Precondición** | Existen productos activos con `stock > 0` en la BD. |
| **Flujo Principal** | 1. El vendedor envía `POST /api/sales` con `user_id` y el array `details [{product_id, quantity}]`. 2. `StoreSaleRequest` valida que `details` tenga al menos 1 elemento y todos los `product_id` existan. 3. Dentro de `DB::transaction()`: se crea el registro en `sales` con `invoice_status='pending'`; se insertan los `SaleDetail` con `unit_price` y `unit_cost` copiados de `products`. 4. Los triggers actualizan `subtotal`, decrementan `stock` y recalculan `total_amount`. 5. La API retorna **HTTP 201** con `id`, `total_amount` e `invoice_status='pending'`. |
| **Flujo Alt. A** | Stock insuficiente → trigger lanza excepción → `DB::transaction()` hace rollback → **HTTP 422**. |
| **Flujo Alt. B** | `product_id` inexistente → **HTTP 422**. |
| **Flujo Alt. C** | Array `details` vacío → **HTTP 422** `'La venta debe contener al menos un producto'`. |
| **Postcondición** | La venta queda registrada con `invoice_status='pending'`, stock decrementado y `total_amount` calculado. |

---

#### HU-08 · Emisión de Factura Electrónica (SIN)
> **Épica:** Ventas y Facturación | **SP:** 5 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **vendedor**, quiero emitir la factura electrónica asociada a una venta registrada, para cumplir la normativa fiscal vigente del SIN Bolivia y dejar trazabilidad verificable de cada transacción mediante el hash del XML enviado.

**Criterios de Aceptación**
- [ ] Solo ventas con `invoice_status='pending'` pueden procesarse; intentar emitir con status `'issued'` retorna **HTTP 409** `'La factura ya fue emitida para esta venta'`.
- [ ] Una emisión exitosa actualiza `invoice_status` a `'issued'`, persiste `invoice_number` (asignado por el SIN), `invoice_xml_hash` (SHA-256 del payload XML) e `invoice_issued_at`.
- [ ] Si el servicio SIN falla (timeout > 10 s o respuesta 4xx/5xx), `invoice_status` pasa a `'error'`, se persiste el detalle del fallo en `invoice_error_detail` y la API retorna **HTTP 502**.
- [ ] El método `verifyInvoiceHash()` retorna `true` si `SHA-256(xml_payload_recalculado) === invoice_xml_hash` almacenado; `false` en cualquier otro caso.
- [ ] El campo `invoice_issued_at` se persiste con la fecha y hora exacta de confirmación del SIN, no la de registro de la venta.
- [ ] La API retorna **HTTP 200** con `invoice_status`, `invoice_number` e `invoice_issued_at` actualizados.

**Definition of Ready (DoR)**
- [ ] HU-07 completada: existen ventas con `invoice_status='pending'` en la BD.
- [ ] La interfaz `InvoiceServiceInterface` está definida en `app/Contracts/InvoiceServiceInterface.php` con los métodos y tipos de retorno documentados.
- [ ] La implementación mock `MockInvoiceService` está disponible en `app/Services/MockInvoiceService.php` y enlazada en `AppServiceProvider` solo para entornos local y testing.
- [ ] El squad acordó el criterio para `'error'`: timeout > 10 s o respuesta con código HTTP 4xx/5xx del SIN.
- [ ] La ruta `POST /api/sales/{id}/invoice` está registrada con middleware `auth:sanctum`.
- [ ] El campo `invoice_error_detail (TEXT, NULL)` está agregado en la migración de `sales`.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Vendedor o Administrador autenticado |
| **Precondición** | Existe una venta con `invoice_status='pending'` en la BD. |
| **Flujo Principal** | 1. El usuario envía `POST /api/sales/{id}/invoice`. 2. El sistema verifica que `invoice_status === 'pending'`. 3. El sistema genera el payload XML con los datos de la venta. 4. El sistema llama a `InvoiceServiceInterface::emit($sale)`. 5. El SIN retorna el número de factura confirmado. 6. El sistema calcula `invoice_xml_hash = SHA-256(payload XML)`. 7. El sistema actualiza la venta: `invoice_status='issued'`, `invoice_number`, `invoice_xml_hash`, `invoice_issued_at = now()`. 8. La API retorna **HTTP 200** con los campos actualizados. |
| **Flujo Alt. A** | Factura ya emitida → **HTTP 409** `'La factura ya fue emitida para esta venta'`. |
| **Flujo Alt. B** | Fallo del SIN → `invoice_status='error'`, se guarda `invoice_error_detail`, **HTTP 502**. |
| **Postcondición** | La factura queda emitida con trazabilidad completa: número SIN, hash SHA-256 del XML y timestamp de confirmación. |

---

#### HU-09 · Consolidado de Estado de Facturación
> **Épica:** Ventas y Facturación | **SP:** 3 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **administrador del negocio**, quiero ver cuántas ventas están en estado `'issued'`, `'pending'` o `'error'` en un rango de fechas, para detectar a tiempo fallas en el proceso de facturación electrónica y tomar acción antes de una auditoría fiscal.

**Criterios de Aceptación**
- [ ] `GET /api/dashboard/invoice-summary` acepta `date_from` y `date_to` (formato `YYYY-MM-DD`); el rango por defecto son los últimos 30 días si no se proporcionan.
- [ ] La respuesta incluye por cada estado: `count` (INTEGER) con el número de ventas y `total_amount` (DECIMAL) con el monto acumulado.
- [ ] La consulta usa el índice compuesto `idx_sales_date_status (created_at, invoice_status)` y se ejecuta en menos de 500 ms.
- [ ] El componente `<InvoiceStatusChart>` renderiza los datos como gráfico de dona: verde `#16A34A` (issued), amarillo `#D97706` (pending), rojo `#DC2626` (error).
- [ ] Un rango con `date_from > date_to` retorna **HTTP 422** `'El rango de fechas es inválido: date_from debe ser anterior o igual a date_to'`.

**Definition of Ready (DoR)**
- [ ] HU-08 completada: existen ventas en los tres estados en la BD de prueba.
- [ ] El índice compuesto `idx_sales_date_status (created_at, invoice_status)` está creado y verificado con `EXPLAIN`.
- [ ] La ruta `GET /api/dashboard/invoice-summary` está registrada con middlewares `['auth:sanctum','role:admin']`.
- [ ] El squad acordó los colores exactos del gráfico: verde `#16A34A`, amarillo `#D97706`, rojo `#DC2626`.
- [ ] El seeder incluye ventas en los tres estados distribuidas en los últimos 60 días.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Administrador autenticado con `role='admin'` |
| **Precondición** | Existen ventas registradas con al menos un estado distinto de `'pending'`. |
| **Flujo Principal** | 1. `DashboardPage` envía `GET /api/dashboard/invoice-summary` con `date_from` y `date_to`. 2. El `DashboardController` valida el rango y delega a `DSSAnalyzer`. 3. `DSSAnalyzer` ejecuta `SELECT invoice_status, COUNT(*), SUM(total_amount) FROM sales WHERE created_at BETWEEN ... GROUP BY invoice_status`. 4. La API retorna **HTTP 200** con el consolidado agrupado. 5. `<InvoiceStatusChart>` renderiza el gráfico de dona. |
| **Flujo Alt. A** | Rango inválido (`date_from > date_to`) → **HTTP 422** con mensaje descriptivo. |
| **Flujo Alt. B** | Sin ventas en el rango → **HTTP 200** con los tres estados en `count=0` y `total_amount=0.00`. |
| **Postcondición** | El administrador tiene visibilidad fiscal del período y puede detectar facturas en error antes de una auditoría. |

---

### 📊 Épica 4 — Motor DSS y Dashboard Analítico

---

#### HU-10 · Cálculo de Tasa de Rotación y Margen por Producto
> **Épica:** Motor DSS / Dashboard | **SP:** 5 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **administrador del negocio**, quiero que el sistema calcule automáticamente la tasa de rotación y el margen de ganancia neta de cada repuesto del catálogo para el período analizado, para disponer de los datos cuantitativos que sustentan las decisiones de compra y precio.

**Criterios de Aceptación**
- [ ] `rotation_rate = SUM(sale_details.quantity) / período_días`; el período por defecto es 30 días. Si `período_días = 0` el sistema usa `1` como denominador.
- [ ] `margin_rate = (SUM(subtotal) − SUM(quantity × unit_cost)) / SUM(subtotal)`; si `SUM(subtotal) = 0` entonces `margin_rate = 0` (protección ante división por cero).
- [ ] Los umbrales `rotationThreshold (default 0.5)` y `marginThreshold (default 0.20)` son propiedades públicas configurables de `DSSAnalyzer`, no constantes hardcodeadas.
- [ ] El cálculo usa `LEFT JOIN` entre `products`, `sale_details` y `sales` filtrado por rango de fechas usando el índice `idx_sale_details_product_date`.
- [ ] Productos sin ventas en el período tienen `rotation_rate = 0` y `margin_rate = 0` (LEFT JOIN garantizado).
- [ ] La respuesta incluye para cada producto: `product_id`, `sku`, `name`, `rotation_rate` (4 decimales), `margin_rate` (4 decimales), `total_sold`, `total_revenue` y `total_contribution_margin`.

**Definition of Ready (DoR)**
- [ ] HU-07 completada: existen `sale_details` en la BD con datos suficientes.
- [ ] El índice `idx_sale_details_product_date (product_id, created_at)` está creado y verificado con `EXPLAIN`.
- [ ] La clase `DSSAnalyzer` está creada en `app/Services/DSSAnalyzer.php` con `rotationThreshold=0.5` y `marginThreshold=0.20` como propiedades públicas con setter.
- [ ] Los métodos `computeRotationRate(array $productMetrics, int $days): float` y `computeMarginRate(array $productMetrics): float` están definidos con PHPDoc.
- [ ] El seeder garantiza que al menos 2 productos caigan en cada uno de los cuatro cuadrantes con los umbrales default.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Motor `DSSAnalyzer` (invocado por `DashboardController`) |
| **Precondición** | Existen `sale_details` en la BD para el rango de fechas solicitado. |
| **Flujo Principal** | 1. `DashboardController` invoca `DSSAnalyzer::buildDashboardData($startDate, $endDate)`. 2. `DSSAnalyzer` calcula `período_días = diff($startDate, $endDate)` (mínimo 1). 3. `DSSAnalyzer` ejecuta LEFT JOIN: `products → sale_details → sales` filtrado por rango, agrupado por `product_id`. 4. Por cada producto calcula `rotation_rate` y `margin_rate` con protección ante división por cero. 5. Para productos sin ventas (LEFT JOIN nulo): asigna `rotation_rate=0` y `margin_rate=0`. 6. Retorna array de métricas al `DashboardController`. |
| **Flujo Alt. A** | Rango sin ventas → todos los productos retornan `rotation_rate=0` y `margin_rate=0`. |
| **Flujo Alt. B** | `período_días = 0` → el sistema usa `1` como denominador; se registra un warning en el log de Laravel. |
| **Postcondición** | `DSSAnalyzer` dispone del array de métricas por producto listo para la clasificación matricial de HU-11. |

---

#### HU-11 · Clasificación Matricial Estrella / Hueso
> **Épica:** Motor DSS / Dashboard | **SP:** 8 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **administrador del negocio**, quiero que el sistema clasifique automáticamente cada producto del catálogo en uno de los cuatro cuadrantes de la matriz (Estrella, Interrogante, Vaca, Hueso), para identificar visualmente qué repuestos inmovilizan capital de trabajo y cuáles debo priorizar para reposición.

**Criterios de Aceptación**
- [ ] `classifyProduct()` asigna **ESTRELLA** cuando `rotation_rate ≥ θ_rot AND margin_rate ≥ θ_mrgn` (alta rotación, alto margen).
- [ ] `classifyProduct()` asigna **INTERROGANTE** cuando `rotation_rate < θ_rot AND margin_rate ≥ θ_mrgn` (baja rotación, alto margen).
- [ ] `classifyProduct()` asigna **VACA** cuando `rotation_rate ≥ θ_rot AND margin_rate < θ_mrgn` (alta rotación, bajo margen).
- [ ] `classifyProduct()` asigna **HUESO** cuando `rotation_rate < θ_rot AND margin_rate < θ_mrgn` (baja rotación, bajo margen).
- [ ] El 100% de los productos activos queda clasificado en exactamente uno de los cuatro cuadrantes.
- [ ] `buildStarHusoMatrix()` retorna un objeto con cuatro arrays (`estrella`, `interrogante`, `vaca`, `hueso`), `top_star[5]` (mayor `rotation_rate × margin_rate`) y `critical_huso[5]` (mayor `total_cost_sold` sin retorno).
- [ ] El componente `<StarHusoMatrix>` renderiza un scatter plot en ApexCharts con eje X = `rotation_rate`, eje Y = `margin_rate`, color por cuadrante y líneas de referencia en los umbrales.

**Definition of Ready (DoR)**
- [ ] HU-10 completada: `DSSAnalyzer` calcula `rotation_rate` y `margin_rate` para todos los productos activos.
- [ ] El enum `ProductCategory` con los valores `ESTRELLA`, `INTERROGANTE`, `VACA`, `HUESO` está definido en `app/Enums/ProductCategory.php`.
- [ ] El método `classifyProduct(float $rotationRate, float $marginRate): ProductCategory` está implementado con los cuatro casos mutuamente excluyentes.
- [ ] El seeder garantiza al menos 2 productos en cada cuadrante para validar el renderizado completo del scatter plot.
- [ ] ApexCharts está instalado: `npm install apexcharts react-apexcharts` y listado en `package.json` con versión exacta.
- [ ] El squad acordó los colores: Estrella `#16A34A` (verde), Interrogante `#D97706` (amarillo), Vaca `#1D4ED8` (azul), Hueso `#DC2626` (rojo).

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Motor `DSSAnalyzer` + Administrador (visualización final) |
| **Precondición** | HU-10 ejecutada: el array de métricas por producto está disponible en memoria del `DSSAnalyzer`. |
| **Flujo Principal** | 1. `DSSAnalyzer` itera sobre el array de métricas por producto. 2. Para cada producto llama `classifyProduct(rotation_rate, margin_rate)` que retorna un valor del enum `ProductCategory`. 3. El producto se agrega al array del cuadrante correspondiente en la `matrixDTO`. 4. `DSSAnalyzer` extrae `top_star[5]` y `critical_huso[5]`. 5. `DashboardController` retorna la `matrixDTO` en el payload JSON. 6. `<StarHusoMatrix>` renderiza el scatter plot con los cuatro cuadrantes coloreados y las líneas de umbral. |
| **Flujo Alt.** | Ningún producto en algún cuadrante → el cuadrante se renderiza vacío con mensaje `'Sin productos en este cuadrante'` sin lanzar error. |
| **Postcondición** | El 100% de los productos activos queda clasificado en exactamente un cuadrante; el administrador visualiza la distribución completa en el scatter plot. |

---

#### HU-12 · Dashboard Interactivo con KPIs Generales
> **Épica:** Motor DSS / Dashboard | **SP:** 5 | **MoSCoW:** Must have

**Historia de Usuario**
> Como **administrador del negocio**, quiero ver en un solo panel los ingresos totales, número de ventas, productos activos y el margen global del período analizado, para tener visibilidad financiera inmediata sin esperar reportes manuales tardíos.

**Criterios de Aceptación**
- [ ] `GET /api/dashboard` retorna un único payload JSON con `kpis`, `matrix`, `top_star`, `critical_huso` e `invoice_summary` en menos de **2 segundos** sobre el dataset del seeder.
- [ ] `kpis` incluye: `total_revenue (DECIMAL, 2 decimales)`, `total_sales (INTEGER)`, `active_products (INTEGER)` y `overall_margin_rate_pct (DECIMAL, 2 decimales como porcentaje)`.
- [ ] El filtro de fecha por defecto son los últimos 30 días; el administrador puede ajustarlo enviando `date_from` y `date_to` como query params.
- [ ] El componente `<KPICards>` renderiza cuatro tarjetas: Total Ingresos, Total Ventas, Productos Activos y Margen Global; cada tarjeta muestra la variación porcentual (↑ verde / ↓ rojo) respecto al período anterior.
- [ ] Si no hay ventas en el período, todos los KPIs son `0` y el Dashboard muestra el mensaje `'Sin datos para el período seleccionado'` sin lanzar error HTTP.

**Definition of Ready (DoR)**
- [ ] HU-11 completada: `DSSAnalyzer` clasifica la matriz Estrella/Hueso y todas sus dependencias están resueltas.
- [ ] La ruta `GET /api/dashboard` está registrada con middlewares `['auth:sanctum','role:admin']`.
- [ ] El squad acordó el formato de cada tarjeta KPI: valor principal + símbolo de variación porcentual respecto al período anterior de igual duración.
- [ ] El seeder incluye ventas en el período por defecto (últimos 30 días) y en el período anterior (días 31–60) para que la variación sea calculable.
- [ ] El SLA de 2 segundos está documentado en el README y se verificará con un test básico usando Laravel HTTP Test.
- [ ] `DashboardPage` implementa un estado de carga (spinner) y un estado de error si la respuesta tarda más de 10 segundos.

**Modelo de Caso de Uso**

| Campo | Descripción |
|---|---|
| **Actor Principal** | Administrador autenticado con `role='admin'` |
| **Precondición** | El usuario está autenticado con `role='admin'`. Existen ventas en la BD para el período analizado. |
| **Flujo Principal** | 1. El administrador navega a `/dashboard` en el frontend. 2. `DashboardPage` ejecuta `GET /api/dashboard` (últimos 30 días por defecto) vía `AxiosClient`. 3. Los middlewares `auth:sanctum` y `CheckRole` validan el token y el rol. 4. `DashboardController` invoca `DSSAnalyzer::buildDashboardData(startDate, endDate)`. 5. `DSSAnalyzer` ejecuta las 4 queries analíticas y clasifica la matriz. 6. La API retorna **HTTP 200** con el payload completo en < 2 segundos. 7. React actualiza el estado y re-renderiza: `<KPICards>`, `<StarHusoMatrix>`, `<TopStarTable>`, `<CriticalHusoAlert>` e `<InvoiceStatusChart>`. |
| **Flujo Alt. A** | Sin ventas en el período → **HTTP 200** con todos los valores en 0; el Dashboard muestra estado vacío informativo. |
| **Flujo Alt. B** | Tiempo de respuesta > 2 s → el frontend muestra spinner; si supera 10 s muestra mensaje de error con opción de reintentar. |
| **Postcondición** | El administrador tiene visibilidad financiera completa del período en un único panel interactivo con KPIs, matriz, alertas y estado fiscal. |

---

## 📌 Definition of Ready General

### Entorno y Repositorio
- [ ] El repositorio existe en GitHub con una instalación limpia de **Laravel 11**.
- [ ] La estructura de carpetas `/app`, `/database`, `/docs`, `/resources` y `/routes` está creada conforme al documento de arquitectura.
- [ ] El archivo `.gitignore` excluye `.env`, `/vendor` y `/node_modules`.
- [ ] El `README.md` incluye los miembros del Squad y el enlace al PDF consolidado del entregable.
- [ ] Cada integrante del Squad tiene acceso de escritura al repositorio y a **GitHub Projects**.

### Base de Datos
- [ ] Las migraciones de `users`, `products`, `sales` y `sale_details` están versionadas en `/database/migrations`.
- [ ] Los índices analíticos (`created_at`, `invoice_status`, `sku`, `brand`, `category`, `product_id`) están declarados en las migraciones.
- [ ] Los cuatro triggers (`trg_sale_details_before_insert`, `after_insert_stock`, `after_delete_stock`, `after_insert_total`) están en un archivo de migración o seeder ejecutable.
- [ ] Las tres vistas analíticas (`v_product_dss_metrics`, `v_sales_invoice_summary`, `v_dss_kpis_general`) están creadas.
- [ ] Existen seeders que poblan datos suficientes para que el motor DSS clasifique al menos **2 productos en cada cuadrante**.

### Backlog y Tablero Ágil
- [ ] Las 12 Historias de Usuario están registradas como **Issues** independientes en GitHub Projects con plantilla completa.
- [ ] Cada Issue está vinculado a la columna `'Backlog'` del tablero Kanban.
- [ ] Las dependencias entre Historias están señaladas: `HU-07 → HU-08`, `HU-04 → HU-10`, `HU-10 → HU-11`.

### Componentes Técnicos

| Componente | Condición de Listo |
|---|---|
| **Modelos Eloquent** | `User`, `Product`, `Sale` y `SaleDetail` creados con relaciones `hasMany`/`belongsTo`. `price`, `cost`, `unit_price` y `unit_cost` tipados como `decimal:10,2` en `$casts`. |
| **Migraciones** | Toda migración tiene `down()` implementado. FK explícitas en todas las tablas. |
| **DSSAnalyzer** | Clase en `app/Services` con `rotationThreshold` y `marginThreshold` configurables. Firmas de métodos coinciden con el diagrama de clases. |
| **DashboardController** | Rutas `/api/dashboard` registradas. Middleware `auth:sanctum` + `CheckRole` aplicados. |
| **Frontend React** | `DashboardPage` y `AxiosClient` scaffoldeados. ApexCharts instalado y en `package.json`. |
| **Facturación** | `InvoiceServiceInterface` definida. Implementación mock disponible para pruebas locales. |

### Criterio de Cierre de la DoR

El proyecto cumple la Definition of Ready cuando:

- El **100%** de los ítems anteriores está marcado y verificado por al menos un integrante del Squad distinto al que lo implementó.
- El tablero Kanban es **visible públicamente** para el equipo docente.
- Al menos **HU-01** puede moverse de `'Ready'` a `'In Progress'` sin requerir información adicional de ningún tipo.

---

*Universidad Privada Domingo Savio — Sistemas de Información II — 2025*
