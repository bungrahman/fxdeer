# FXDeer (Laravel)

Sistem automasi berita yang terintegrasi dengan n8n untuk fetching dan distribusi berita. Backend Laravel mengelola business logic, user management, dan billing integration dengan Stripe, PayPal, dan Duitku.

## 🚀 Quick Start

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

## 📚 API Documentation

### n8n Integration Endpoints

#### 1. Check Eligibility
```http
POST /api/events/eligible
Content-Type: application/json

{
  "user_id": 1,
  "pipeline": "A"
}
```

**Response**:
```json
{
  "status": "allowed",
  "remaining_quota": 5
}
```

#### 2. Mark Event as Sent
```http
POST /api/events/mark-sent
Content-Type: application/json

{
  "event_id": "unique-event-id",
  "user_id": 1,
  "pipeline": "A",
  "event_time_utc": "2026-02-14T08:00:00Z",
  "language": "en",
  "channel": "telegram"
}
```

### Stripe Webhook
```http
POST /api/stripe/webhook
Stripe-Signature: [signature]
```

## 🗄️ Database Schema

- **users**: User accounts dengan status dan preferences
- **plans**: Subscription plans dengan features dan quotas
- **subscriptions**: User subscriptions dengan Stripe integration
- **event_registry**: Event tracking dengan idempotency (PRIMARY KEY: event_id)
- **usage_log**: Daily usage tracking per user
- **translations_cache**: Cached translations untuk optimasi

## 🔧 Development

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

## 📖 Documentation

Lihat [walkthrough.md](file:///C:/Users/Pavilion/.gemini/antigravity/brain/d2dffcd4-c7a1-4fc8-b295-e73511514345/walkthrough.md) untuk dokumentasi lengkap.

## 🛠️ Tech Stack

- **Framework**: Laravel 11.x
- **PHP**: 8.2.12
- **Database**: MySQL/MariaDB
- **Cache**: Redis
- **Payment**: Stripe
- **Integration**: n8n

## 📝 License

Proprietary
