# Event-Driven E-Commerce Synchronization API

![Hub and Spoke Architecture](https://mermaid.ink/img/pako:eNp1ksGOqkAMhl_FhHPe4A1kcDBLzI0ZcTKTMVA6FAbatLRQGeO7r2B09-bSh_b_-_8mQ3MqM4I0j_Ozz2e_IChfL2hA19c36P18hV59Q6-Pj9CrD_TqA_33-gG9ev8FvX7_C3r1_jd0_fs_0P_179DvH3-hnz_f0M-f39DPn9_Qz7__hv7__h_0__f_Qf__fxb2QJ_NqYQCaR5XyF5BII1jCSvYwAb2sIcjHOEMF7jCFW5wh3s4wxnOcIErXOEGl2CDB2yCDV6wwSfY4BPs8Al2-AQ7fII9fsAef2CDP7DDH9jhM-zwh3b4Izv8kR3-xA5_Yoc_scOf2eHP7PBndvgLe_yFPeCwxx_Y4w_s8Qf2uMce99jjHnvcs4y9t3c44A8c8AcO-AMH_Cllb78G_IED_sABf-CAP3DAHzjgDxzwBw74A69A9gpk70D2DmTvQPYOZO9A9g5k70D2DmTvQPYOZM9A9gxoT_9uQO9G0LvRoXcj6d3IoXcj2Dvo3Uiyd6N37wb0bgS9Gx16N5LejRx6N4K9A9kzkD0D2TOQPTSN6NCNjt6NHHo3gr0D2TOQPQPZM5A9A9kzkL0D2TuQvQPZO5C9I9gzkD0D2TNk756B7BnIngF7yN69g969g969g969k-yhm0Pv3gn2DmTPQPYMZP8A6sDtdw) 

## 🚀 Overview

This project implements a robust **Event-Driven Hub-and-Spoke Architecture** to synchronize products and orders between a **Wholesaler (Source)** and **Retailers (Clients)** using PrestaShop.

The system features:
- **Centralized Middleware (Laravel)**: Acts as the "Hub", orchestrating traffic between stores.
- **PrestaShop Connector Module**: A custom module installed on both Source and Client instances to push events via Webhooks.
- **Async Processing**: Uses **Redis** and **Laravel Horizon** for scalable, non-blocking queue management.
- **Security**: JWT-like Token Authentication (`X-API-KEY`) using constant-time comparison.
- **Containerized Infrastructure**: Fully Dockerized environment (Nginx, PHP 8.3, MySQL, Redis, PrestaShop).

---

## 🏗 Architecture

### 1. Data Flow (Hub and Spoke)
Instead of point-to-point connections (Spaghetti), all stores talk *only* to the Middleware.

- **Product Sync (Source -> Clients):**
  1. **Source** updates a product.
  2. Module hook `actionProductSave` triggers.
  3. Webhook sent to `POST /api/sync/product`.
  4. API queues `ProcessProductSync` job (Low Latency).
  5. Worker executes job -> Updates stock on **Client** stores via PrestaShop API.

- **Order Replication (Client -> Source):**
  1. **Client** receives an order.
  2. Module hook `actionValidateOrder` triggers.
  3. Webhook sent to `POST /api/sync/order`.
  4. API queues `ProcessOrderReplication` job.
  5. Worker executes job -> Creates order on **Source** to reserve stock.

### 2. Tech Stack
- **Framework**: Laravel 11.x
- **Language**: PHP 8.3
- **Queue**: Redis + Laravel Horizon
- **Database**: MySQL 8.0
- **Documentation**: Swagger/OpenAPI (Darkaonline/L5-Swagger)

---

## 🛠️ Installation & Setup

### Prerequisites
- Docker & Docker Compose
- Curl

### 1. Start Infrastructure
```bash
docker-compose up -d --build
```

### 2. Verify Services
- **Laravel API**: `http://api.fulfillment.local` (or localhost:8000)
- **Wholesaler Store**: `http://mayorista.com` (Mapped to localhost:8080)
- **Retailer Store**: `http://minorista.com` (Mapped to localhost:8081)
- **Horizon Dashboard**: `http://localhost:8000/horizon`

### 3. Install PrestaShop Module
The environment comes with a custom `fulfillment_connector` module.
1. Log into PrestaShop Admin (`admin@fulfillment.local` / `password123` or `password`).
2. Go to **Modules > Module Manager**.
3. Install **Event-Driven Fulfillment Connector**.
4. **Configure**:
   - **API URL**: `http://fulfillment-nginx/api` (Internal Network URL)
   - **Token**: [Check .env `API_ACCESS_TOKEN`]
   - **Role**: Select `SOURCE` for Mayorista, `CLIENT` for Minorista.

---

## 🧪 Testing

We adhere to Test-Driven Development (TDD) principles. The suite includes Unit, Feature, and Integration tests.

### Run All Tests
```bash
docker-compose exec fulfillment-app php artisan test
```

### Key Test Classes
- **`SyncApiTest`**: Validates the end-to-end webhook reception and security.
- **`ProcessProductSyncTest`**: Mocks the PrestaShop Service to ensure jobs interact correctly with the external API.
- **`EnsureValidApiTokenTest`**: Verifies security headers and rejection of invalid tokens.

---

## 📚 API Documentation

Swagger UI is available at:
`http://localhost:8000/api/documentation`

**Endpoints:**
- `POST /api/sync/product`: Accepts product data payload.
- `POST /api/sync/order`: Accepts order data payload.

**Security Scheme:**
- Type: `apiKey`
- Name: `X-API-KEY`
- In: `Header`

---

## 🛡️ Security Best Practices

1. **Constant-Time Comparison**: The `EnsureValidApiToken` middleware uses `hash_equals()` to prevent timing attacks.
2. **Read-Only DTOs**: Data Transfer Objects (`ProductData`, `OrderData`) are immutable to prevent unintended mutations.
3. **Validated Requests**: All Webhooks are strictly validated before dispatching jobs.
4. **Isolated Networks**: Database and Redis are on an internal `fulfillment-network`, not exposed publicly.

---

## 🧩 Directory Structure

```
├── app
│   ├── Actions         # Logic classes (Single Responsibility)
│   ├── DTOs            # Data Transfer Objects
│   ├── Interfaces      # Contracts (Repository Pattern)
│   ├── Jobs            # Queued Jobs
│   ├── Services        # External Integrations (PrestaShopService)
├── modules
│   └── fulfillment_connector # Custom PrestaShop Module Source Code
├── docker
│   ├── fulfillment     # PHP-FPM Configuration
│   └── nginx           # Nginx Configuration
└── tests               # Automated Test Suite
```

## License
Commercial License.
