# FXDeer (Laravel)

Sistem automasi berita yang terintegrasi dengan n8n untuk fetching dan distribusi berita. Backend Laravel mengelola business logic, user management, dan billing integration dengan Stripe, PayPal, dan Duitku.

## Quick Start

### Prerequisites
- PHP 8.2+ (XAMPP)
- Composer
- MySQL/MariaDB
- Redis (untuk locking mechanism)

### Installation

1. **Install Dependencies**
```bash
cd newsauto-app
C:\xampp\php\php.exe ..\composer.phar install
```

2. **Configure Environment**
```bash
cp .env.example .env
C:\xampp\php\php.exe artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=newsauto
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret
```

3. **Run Migrations**
```bash
C:\xampp\php\php.exe artisan migrate
```

4. **Start Development Server**
```bash
C:\xampp\php\php.exe artisan serve
```

## API Documentation

Aplikasi ini menyediakan berbagai API endpoint untuk integrasi eksternal (n8n, Bot, dll). Semua request menggunakan format JSON.

### 1. n8n Coordination (News Automation)
Digunakan oleh n8n untuk mengelola alur distribusi berita otomatis.

- **`GET /api/events/users/active`**: Mengambil daftar user aktif beserta metadata pengiriman (bot token, chat id, quota).
- **`GET /api/events/plans/{plan_id}`**: Mengambil detail konfigurasi fitur untuk plan tertentu.
- **`POST /api/events/eligible`**: Validasi awal apakah user boleh menerima berita (cek status & kuota).
  - **Body**: `{ "user_id": 1, "pipeline": "A" }`
- **`POST /api/events/mark-sent`**: Konfirmasi berita terkirim & kurangi kuota harian (Idempotent).
  - **Body**: `{ "event_id": "...", "user_id": 1, "pipeline": "A", "event_time_utc": "...", "language": "en", "channel": "telegram" }`

### 2. Trading Signals API
Untuk manajemen sinyal trading yang ditampilkan di dashboard atau dikirim via bot.

- **`GET /api/signals`**: List semua sinyal trading terbaru.
- **`POST /api/signals`**: Input sinyal baru (mendukung single object atau array untuk bulk insert).
  - **Body**: `{ "signal": "BUY", "pair": "EURUSD", "price": "1.0850", "sl": "1.0800", "tp": "1.0950", "reason": "...", "conf_level": "High" }`
- **`PUT /api/signals/{id}`**: Update data sinyal (misal: isi hasil TP/SL).
- **`DELETE /api/signals/{id}`**: Hapus sinyal secara spesifik.
- **`GET /api/signals/stats`**: Statistik harian sinyal (win rate, top pair, dll).
- **`POST /api/signals/bulk-delete`**: Hapus banyak sinyal sekaligus.
  - **Body**: `{ "ids": [1, 2, 3] }`

### 3. Signal Configuration
Setting koneksi data provider (TwelveData, API keys, dll).

- **`GET /api/signal_config`**: Lihat config aktif.
- **`POST /api/signal_config`**: Simpan config baru.
- **`PUT /api/signal_config/{id}`**: Update config (status, pairs, keys).
- **`DELETE /api/signal_config/{id}`**: Hapus config.

### 4. Admin Control Plane
- **`POST /api/admin/settings`**: Update global kill-switches (Emergency Pause).
  - **Body**: `{ "emergency_pause": true, "kill_switch_pipeline_a": false }`
- **`POST /api/admin/users/{user}/status`**: Suspend atau activate user secara manual.
- **`POST /api/admin/reset-quotas`**: Reset paksa semua kuota harian user untuk hari ini.
- **`GET /api/user`**: Mengambil profil user yang sedang login (membutuhkan Bearer Token).

### 5. Webhooks
- **`POST /api/stripe/webhook`**: Integrasi pembayaran Stripe.
- **`POST /api/duitku/callback`**: Integrasi pembayaran Duitku.

## Database Schema

- **users**: User accounts dengan status dan preferences
- **plans**: Subscription plans dengan features dan quotas
- **subscriptions**: User subscriptions dengan Stripe integration
- **event_registry**: Event tracking dengan idempotency (PRIMARY KEY: event_id)
- **usage_log**: Daily usage tracking per user
- **translations_cache**: Cached translations untuk optimasi

## Development

### Run Tests
```bash
C:\xampp\php\php.exe artisan test
```

### Create Migration
```bash
C:\xampp\php\php.exe artisan make:migration create_table_name
```

### Create Model
```bash
C:\xampp\php\php.exe artisan make:model ModelName
```

## Documentation

Lihat [walkthrough.md](file:///C:/Users/Pavilion/.gemini/antigravity/brain/d2dffcd4-c7a1-4fc8-b295-e73511514345/walkthrough.md) untuk dokumentasi lengkap.

## Tech Stack

- **Framework**: Laravel 11.x
- **PHP**: 8.2.12
- **Database**: MySQL/MariaDB
- **Cache**: Redis
- **Payment**: Stripe
- **Integration**: n8n

## License

Proprietary
