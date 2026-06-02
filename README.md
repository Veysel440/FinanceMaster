<h1 align="center">FinanceMaster</h1>

<p align="center">
  <strong>Kişisel ve kurumsal finansı tek yerden yönet.</strong><br/>
  Laravel 12 REST API · Next.js 15 SPA · MySQL · Sanctum · PHP 8.2
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-%5E8.2-777BB4?logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Laravel-%5E12.0-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Sanctum-%5E4.3-FF2D20" alt="Sanctum 4.3">
  <img src="https://img.shields.io/badge/Next.js-15.4.5-000?logo=next.js&logoColor=white" alt="Next.js 15">
  <img src="https://img.shields.io/badge/React-19.1-61DAFB?logo=react&logoColor=white" alt="React 19">
  <img src="https://img.shields.io/badge/TypeScript-%5E5-3178C6?logo=typescript&logoColor=white" alt="TypeScript 5">
  <img src="https://img.shields.io/badge/Tailwind-%5E4.1-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind 4">
  <img src="https://img.shields.io/badge/MySQL-8%2B-4479A1?logo=mysql&logoColor=white" alt="MySQL 8+">
  <img src="https://img.shields.io/badge/PHPUnit-%5E11.5-3776AB" alt="PHPUnit 11.5">
  <img src="https://img.shields.io/badge/License-MIT-green" alt="License MIT">
</p>

---

## İçindekiler

1. [Proje Kimliği](#1-proje-kimliği)
2. [Mimari](#2-mimari)
3. [Teknoloji Yığını](#3-teknoloji-yığını)
4. [Güvenlik](#4-güvenlik)
5. [Kurulum](#5-kurulum)
6. [Dizin Yapısı](#6-dizin-yapısı)
7. [API Yüzeyi](#7-api-yüzeyi)
8. [Test Altyapısı](#8-test-altyapısı)
9. [Geliştirici Akışı](#9-geliştirici-akışı)
10. [Yol Haritası](#10-yol-haritası)

---

## 1. Proje Kimliği

**FinanceMaster**, bireylerin ve küçük işletmelerin para akışlarını tek bir
arayüzden yönetmek için tasarlanmış, **API-first** bir finans takip
platformudur. Gelir-gider kaydı, kategorize edilmiş aylık bütçeler,
finansal hedefler ve dönemsel raporlama mevcut sürümün temel modülleridir.

### Vizyon

Uzun vadede uygulamanın kapsamı şu eksenlere genişletilecek:

- **Çoklu hesap takibi** — banka hesapları, kredi kartları, nakit cüzdan,
  e-cüzdan; hesaplar arası transfer ve toplam net değer hesaplama
- **Yatırım takibi** — hisse senedi, yatırım fonu, kripto para alım-satım
  geçmişi, gerçek-zamanlı portföy değerlemesi, kar/zarar raporlaması
- **Döviz ve emtia takibi** — TRY / USD / EUR / GBP, altın / gümüş;
  günlük kurla otomatik dönüşüm ve çoklu para birimli raporlar
- **Tekrarlayan işlemler** — kira, abonelik, fatura için otomatik
  kayıt ve hatırlatıcı
- **Bildirim ve uyarı sistemi** — bütçe aşımı, hedef yaklaşma,
  alışılmadık harcama tespiti

Hedef: kullanıcının finansal hayatını gözlemleyen, modelleyen ve
proaktif uyarı üreten bir **finans asistanı**.

### Dağıtım birimleri

Proje iki bağımsız bileşenden oluşur:

| Bileşen | Konum | Sorumluluk |
|---------|-------|------------|
| **Backend** | repo kökü (Laravel 12) | REST API, iş kuralları, persistence, kimlik doğrulama |
| **Frontend SPA** | `finance-master-frontend/` (Next.js 15) | Kullanıcı arayüzü, grafikler, form akışları |

API-first yaklaşımı sayesinde aynı backend; web SPA, gelecek mobil
istemciler ve üçüncü taraf entegrasyonlar için tek kaynak görevi görür.

---

## 2. Mimari

### 2.1 Katmanlı Yapı

```
┌──────────────────────────────────────────────────────────────┐
│  HTTP Katmanı  routes/api.php · FormRequest · Middleware     │
│  Controller    (App\Http\Controllers\Api\...)                │
│    → yalnızca köprü: Request → Service → JsonResponse        │
├──────────────────────────────────────────────────────────────┤
│  Service Layer  (App\Services\...)                           │
│    iş kuralları · bütçe kontrol · bildirim tetikleme         │
├──────────────────────────────────────────────────────────────┤
│  Repository Sözleşmeleri  (App\Interface\...)                │
│  Eloquent Uygulamaları    (App\Repositories\...)             │
├──────────────────────────────────────────────────────────────┤
│  Model / Eloquent ORM · Resource · Notification · Migration  │
└──────────────────────────────────────────────────────────────┘
```

Her katman yalnızca bir alt katmanı bilir; üst katmana referans tutmaz.
DI container, interface'leri concrete sınıflara
`App\Providers\RepositoryServiceProvider` üzerinden bağlar.

### 2.2 Servisler

`app/Services/` altında 7 servis bulunur:

| Servis | İşlev |
|--------|-------|
| `TransactionService` | İşlem CRUD, kullanıcı kapsamı, expense sonrası `BudgetService::checkBudgetsForTransaction` tetikleme |
| `BudgetService` | Bütçe CRUD; harcama ile karşılaştırma; limit aşımında `BudgetLimitExceededNotification` gönderme |
| `CategoryService` | Kullanıcıya özel + varsayılan kategori CRUD |
| `GoalService` | Finansal hedef CRUD; `getGoalProgress` ile ilerleme yüzdesi |
| `ReportService` | `getSummary`, `getCategoryBreakdown`, `getTrendData` — period/tarih filtreli |
| `UserService` | Profil, ayar, profil fotoğrafı; `UserServiceInterface` sözleşmesini uygular |
| `NotificationService` | `BudgetLimitExceededNotification` ve `MonthlySummaryNotification` gönderim sarmalayıcı |

### 2.3 Repository Sözleşmeleri

`app/Interface/` altında 6 sözleşme tanımlıdır:

| Sözleşme | Uygulama |
|----------|----------|
| `TransactionRepositoryInterface` | `App\Repositories\TransactionRepository` |
| `BudgetRepositoryInterface` | `App\Repositories\BudgetRepository` |
| `CategoryRepositoryInterface` | `App\Repositories\CategoryRepository` |
| `GoalRepositoryInterface` | `App\Repositories\GoalRepository` |
| `ReportRepositoryInterface` | `App\Repositories\ReportRepository` |
| `UserRepositoryInterface` | `App\Repositories\UserRepository` |

Servis sözleşmeleri `app/Services/Contracts/` altında tutulur — şu an
yalnızca `UserServiceInterface` mevcuttur (PR-04). Diğer servisler için
sözleşmeler PR-09'da eklenecektir.

### 2.4 Mimari Kararlar

- **Thin controller** — controller yalnızca request'i deserialize eder,
  ilgili service'i çağırır, JsonResponse döndürür. İş kuralı içermez.
- **Repository pattern** — DB sorguları service'ten yalıtık; testlerde
  Eloquent uygulamasını in-memory bir sahte ile değiştirmek mümkün.
- **FormRequest validation** — her endpoint için ayrı `App\Http\Requests\*\*`
  sınıfı; controller validation'ı hiç görmez.
- **API Resource transformer** — response şekli `App\Http\Resources\*\*`
  ile sabitlenir; modelin internal yapısı dışarı sızmaz.
- **Driver-agnostik raporlar** — `ReportRepository::yearMonthExpression()`
  helper'ı MySQL/MariaDB/SQLite/PostgreSQL için uygun SQL üretir.

---

## 3. Teknoloji Yığını

### Backend

| Katman | Teknoloji | Sürüm |
|--------|-----------|-------|
| Dil | PHP | `^8.2` |
| Framework | Laravel | `^12.0` |
| API Auth | Laravel Sanctum | `^4.3` |
| Auth Scaffold | Laravel Breeze (Blade) | `^2.3` (dev) |
| REPL | Laravel Tinker | `^2.10` |
| Logs (dev) | Laravel Pail | `^1.2` |
| Code Style | Laravel Pint | `^1.13` |
| UI scaffold yardımcısı | Laravel UI | `^4.6` |

### Frontend SPA (`finance-master-frontend/`)

| Katman | Teknoloji | Sürüm |
|--------|-----------|-------|
| Framework | Next.js | `15.4.5` |
| UI Library | React + React DOM | `19.1.0` |
| Dil | TypeScript | `^5` |
| CSS | Tailwind CSS | `^4.1.11` (PostCSS plugin) |
| Grafik | Recharts | `^3.1.1` |
| Form | react-hook-form + Yup + `@hookform/resolvers` | `7.62 / 1.7 / 5.2` |
| i18n | i18next + react-i18next + next-i18next | `25.3 / 15.6 / 15.4` |
| HTTP | Axios | `^1.11` |
| Icon | lucide-react | `^0.536` |
| Bildirim | react-hot-toast | `^2.5` |
| Görsel kırpma | react-easy-crop | `^5.5` |
| Lint | ESLint + `eslint-config-next` | `^9 / 15.4.5` |

### Test

| Katman | Teknoloji | Sürüm |
|--------|-----------|-------|
| Test runner | PHPUnit | `^11.5.3` |
| Mock | Mockery | `^1.6` |
| Faker | fakerphp/faker | `^1.23` |
| Hata raporu | Collision (dev) | `^8.6` |

### Veritabanı & altyapı

| Katman | Teknoloji | Not |
|--------|-----------|-----|
| RDBMS (önerilen) | MySQL | 8+ |
| RDBMS (alternatif) | SQLite | `pdo_sqlite` PHP extension gerekir |
| Cache / Session / Queue driver | `database` | `cache`, `sessions`, `jobs` tabloları |
| Local container (opsiyonel) | Laravel Sail | `^1.41` |

### Build & araç (Laravel root)

| Araç | Sürüm |
|------|-------|
| Vite | `^6.2.4` |
| `laravel-vite-plugin` | `^1.2` |
| Tailwind (Blade tarafı) | `^3.1` |
| Alpine.js (Blade tarafı) | `^3.4` |

> Root `package.json`'daki Vite/Alpine/Tailwind 3 yığını, Breeze'in Blade
> sayfaları (`/login`, `/register`, `/dashboard`) için kullanılır;
> SPA tarafı bunlardan bağımsızdır.

---

## 4. Güvenlik

### Mevcut (PR-01..05 ile yerinde)

- **Sanctum token tabanlı kimlik doğrulama** — `POST /api/login` ve
  `POST /api/register` `1|...` formatında Bearer token üretir;
  korunan endpoint'ler `Authorization: Bearer <token>` header'ı bekler.
- **Route-level `auth:sanctum` middleware** — kimlik kontrolü
  `routes/api.php` içindeki grup seviyesinde uygulanır; controller
  constructor'larında middleware çağrısı bulunmaz (Laravel 12 uyumu).
- **FormRequest validation** — `app/Http/Requests/*` altında her
  endpoint için kurallar tanımlanır; controller validation'ı görmez.
- **Brute-force throttle** — `POST /api/login` için `throttle:5,1`
  (dakikada 5 deneme), `POST /api/register` için `throttle:10,1`.
- **CSRF** — Laravel 12 default `web` grubunda otomatik etkin
  (Blade login akışı için).
- **Hash::check + ValidationException** — yanlış kimlik bilgilerinde
  generic "These credentials do not match our records." mesajı,
  kullanıcı varlığı sızdırılmaz.
- **Bcrypt** — `BCRYPT_ROUNDS=12`.
- **CORS** — `config/cors.php` `allowed_origins: [localhost:3000,
  localhost:3001]`, `supports_credentials: true`; SPA için stateful
  Sanctum domain'leri `config/sanctum.php`'de tanımlı.

### Yakında (planlanmış PR'lar)

- **IDOR koruması (PR-06)** — `category_id` gibi foreign key alanları
  için `exists:categories,id`'ye `user_id` veya `is_default=true` scope
  eklenecek. Şu an kullanıcı, başka bir kullanıcının kategorisini
  transaction'a iliştirebilir.
- **Policy sınıfları (PR-09)** — Service katmanındaki manuel
  `user_id === Auth::id()` karşılaştırması yerine `TransactionPolicy`,
  `BudgetPolicy`, `GoalPolicy`, `CategoryPolicy` ile `Gate::authorize`.
- **Mass assignment sıkılaştırma (PR-07)** — `User::$fillable` içindeki
  `password` alanının update endpoint'lerinde whitelist'lenmesi;
  `setPasswordAttribute` mutator double-hash workaround'ının kaldırılması.
- **2FA (PR-20)** — Fortify veya `laragear/two-factor` ile e-posta /
  TOTP tabanlı ikinci faktör.
- **`APP_DEBUG=false` production notu** — `.env.production` ayrı
  tutulmalı; staging deploy script'inde otomatik enforce edilecek.
- **Audit log (PR-20 sonrası)** — `spatie/activitylog` ile model
  değişiklik geçmişi.

---

## 5. Kurulum

### 5.1 Gereksinimler

- **PHP 8.2+** (`pdo_mysql` extension yüklü; SQLite kullanılacaksa
  `pdo_sqlite` da gereklidir)
- **Composer 2.x**
- **Node.js 18+** (frontend SPA için)
- **MySQL 8+** (önerilen) veya SQLite

### 5.2 Backend kurulum

```bash
git clone https://github.com/Veysel440/FinanceMaster.git
cd FinanceMaster

composer install
cp .env.example .env
php artisan key:generate

# .env içinde DB_DATABASE, DB_USERNAME, DB_PASSWORD doldurulduktan sonra:
php artisan migrate
php artisan db:seed --class=CategorySeeder

php artisan serve
# → http://localhost:8000
```

### 5.3 Frontend SPA kurulum

```bash
cd finance-master-frontend
npm install
npm run dev
# → http://localhost:3000
```

> SPA, backend'in `http://localhost:8000` üzerinde çalıştığını varsayar.
> CORS ve Sanctum stateful domain'leri buna göre yapılandırılmıştır.
> Farklı bir port kullanılacaksa `config/cors.php` ve `config/sanctum.php`
> güncellenmelidir.

### 5.4 Ortam değişkenleri

`.env.example`'daki kritik değişkenler:

| Değişken | Açıklama | Varsayılan |
|----------|----------|------------|
| `APP_NAME` | Uygulama adı | `Laravel` |
| `APP_ENV` | Çalışma ortamı (`local`, `production`) | `local` |
| `APP_DEBUG` | Hata detayı göster (production'da **false**) | `true` |
| `APP_URL` | Public base URL | `http://localhost` |
| `APP_LOCALE` | Varsayılan dil | `en` |
| `DB_CONNECTION` | DB driver — `mysql` (önerilen) veya `sqlite` | `mysql` |
| `DB_HOST` | DB host | `127.0.0.1` |
| `DB_PORT` | DB port | `3306` |
| `DB_DATABASE` | DB adı | `finance` |
| `DB_USERNAME` | DB kullanıcı | `root` |
| `DB_PASSWORD` | DB parola | (boş) |
| `SESSION_DRIVER` | Session storage | `database` |
| `CACHE_STORE` | Cache backend | `database` |
| `QUEUE_CONNECTION` | Queue backend | `database` |
| `MAIL_MAILER` | Mail driver (dev için `log`) | `log` |
| `BCRYPT_ROUNDS` | Bcrypt cost factor | `12` |

> **Neden `database` driver?** Tek bir MySQL instance kurulumunu sadeleştirir;
> `cache`, `sessions`, `jobs` tabloları varsayılan migration setiyle birlikte
> gelir. Production'da yüksek trafikte `redis`'e geçiş önerilir (kolay swap).

### 5.5 Kuyruk işçisi (bildirimler)

`BudgetLimitExceededNotification` ve `MonthlySummaryNotification`'ı async
olarak göndermek için:

```bash
php artisan queue:work --tries=3
```

### 5.6 SQLite alternatifi

`pdo_sqlite` PHP extension'ı yüklü ise MySQL yerine SQLite kullanılabilir:

```ini
# .env
DB_CONNECTION=sqlite
# (DB_HOST, DB_PORT vb. yorum satırı yapılmalı)
```

Extension Windows'ta `php.ini` içinde `;extension=pdo_sqlite` satırından
`;` kaldırılarak etkinleştirilir.

---

## 6. Dizin Yapısı

```
FinanceMaster/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── Auth/             # ApiAuthController (login/register/logout)
│   │   │   │   ├── Budget/           # BudgetController
│   │   │   │   ├── Category/         # CategoryController
│   │   │   │   ├── Goal/             # GoalController
│   │   │   │   ├── Profile/          # ProfileController (Blade artığı, kullanılmıyor)
│   │   │   │   ├── Report/           # ReportController (summary/breakdown/trend)
│   │   │   │   ├── Transaction/      # TransactionController
│   │   │   │   └── User/             # UserController (profile, settings, photo)
│   │   │   ├── Auth/                 # Breeze controller'ları (Blade)
│   │   │   └── Controller.php        # base
│   │   ├── Middleware/
│   │   │   └── SetLocale.php         # Auth::user()->locale ile App::setLocale
│   │   ├── Requests/                 # FormRequest sınıfları
│   │   └── Resources/                # API Resource transformer'ları
│   ├── Interface/                    # Repository sözleşmeleri (6 adet)
│   ├── Models/                       # 5 domain modeli
│   ├── Notifications/                # BudgetLimitExceeded, MonthlySummary
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── RepositoryServiceProvider.php   # interface → implementation bind
│   ├── Repositories/                 # 6 Eloquent repository
│   └── Services/                     # 7 servis + Contracts/ alt klasörü
├── bootstrap/
│   └── app.php                       # routing & middleware konfig
├── config/                           # auth, cors, sanctum, database, ...
├── database/
│   ├── factories/                    # UserFactory
│   ├── migrations/                   # 9 migration
│   └── seeders/                      # DatabaseSeeder, CategorySeeder
├── routes/
│   ├── api.php                       # API endpoint'leri (29 route)
│   ├── auth.php                      # Breeze web auth route'ları
│   ├── console.php                   # Artisan komutları
│   └── web.php                       # Blade dashboard + duplikasyon route'ları
├── resources/
│   ├── css/                          # Vite Tailwind 3 (Blade)
│   ├── js/                           # Alpine.js bootstrap
│   ├── lang/                         # en.json, tr.json
│   └── views/                        # Blade template'ler (Breeze)
├── tests/
│   ├── Feature/
│   │   ├── Auth/                     # 6 Breeze testi
│   │   ├── ExampleTest.php
│   │   └── ProfileTest.php
│   ├── Unit/
│   │   └── ExampleTest.php
│   └── TestCase.php
├── finance-master-frontend/          # Next.js 15 + React 19 + TS 5 SPA
│   ├── src/
│   │   ├── api/                      # axios client
│   │   ├── app/                      # Next App Router
│   │   ├── components/
│   │   ├── context/                  # React Context provider'ları
│   │   ├── pages/                    # opsiyonel Pages Router
│   │   ├── types/                    # TS tipleri
│   │   └── utils/
│   ├── public/
│   ├── package.json
│   ├── next.config.ts
│   ├── tailwind.config.js
│   ├── tsconfig.json
│   └── eslint.config.mjs
├── composer.json
├── package.json                      # Laravel root (Vite/Tailwind/Alpine)
├── phpunit.xml
└── README.md
```

---

## 7. API Yüzeyi

Tüm endpoint'ler `/api` prefix'i altındadır. Base URL: `http://localhost:8000`.

### 7.1 Auth (public)

| Method | URI | Middleware | Açıklama |
|--------|-----|-----------|----------|
| `POST` | `/api/login` | `throttle:5,1` | Email + parola → Bearer token |
| `POST` | `/api/register` | `throttle:10,1` | Yeni kullanıcı + Bearer token |
| `POST` | `/api/logout` | `auth:sanctum` | Mevcut token'ı revoke et |

### 7.2 Transactions

| Method | URI | Middleware | Açıklama |
|--------|-----|-----------|----------|
| `GET`    | `/api/transactions`         | `auth:sanctum` | Liste (type, category_id filtre; 10/sayfa) |
| `POST`   | `/api/transactions`         | `auth:sanctum` | Yeni işlem; expense ise bütçe kontrolü tetiklenir |
| `GET`    | `/api/transactions/{id}`    | `auth:sanctum` | Tek işlem detayı |
| `PUT`    | `/api/transactions/{id}`    | `auth:sanctum` | Güncelle |
| `DELETE` | `/api/transactions/{id}`    | `auth:sanctum` | Sil |

### 7.3 Budgets

| Method | URI | Middleware | Açıklama |
|--------|-----|-----------|----------|
| `GET`    | `/api/budgets`         | `auth:sanctum` | Liste + her bütçe için anlık durum (spent/remaining) |
| `POST`   | `/api/budgets`         | `auth:sanctum` | Yeni bütçe (kategori + ay + tutar) |
| `GET`    | `/api/budgets/{id}`    | `auth:sanctum` | Detay |
| `PUT`    | `/api/budgets/{id}`    | `auth:sanctum` | Güncelle |
| `DELETE` | `/api/budgets/{id}`    | `auth:sanctum` | Sil |

### 7.4 Categories

| Method | URI | Middleware | Açıklama |
|--------|-----|-----------|----------|
| `GET`    | `/api/categories`         | `auth:sanctum` | Kullanıcı + varsayılan kategoriler |
| `POST`   | `/api/categories`         | `auth:sanctum` | Yeni özel kategori |
| `GET`    | `/api/categories/{id}`    | `auth:sanctum` | Detay |
| `PUT`    | `/api/categories/{id}`    | `auth:sanctum` | Güncelle |
| `DELETE` | `/api/categories/{id}`    | `auth:sanctum` | Sil (default veya bağlı kategoriler silinemez) |

### 7.5 Goals

| Method | URI | Middleware | Açıklama |
|--------|-----|-----------|----------|
| `GET`    | `/api/goals`         | `auth:sanctum` | Liste |
| `POST`   | `/api/goals`         | `auth:sanctum` | Yeni hedef (title, target_amount, end_date) |
| `GET`    | `/api/goals/{id}`    | `auth:sanctum` | Detay |
| `PUT`    | `/api/goals/{id}`    | `auth:sanctum` | Güncelle |
| `DELETE` | `/api/goals/{id}`    | `auth:sanctum` | Sil |

### 7.6 Reports

| Method | URI | Middleware | Açıklama |
|--------|-----|-----------|----------|
| `GET` | `/api/reports/summary`             | `auth:sanctum` | Toplam gelir / gider / bakiye |
| `GET` | `/api/reports/category-breakdown`  | `auth:sanctum` | Kategoriye göre gider kırılımı (chart-ready) |
| `GET` | `/api/reports/trend`               | `auth:sanctum` | Aylık trend (driver-aware GROUP BY year-month) |

Tümü `?period=daily|weekly|monthly|yearly|custom` ve opsiyonel
`?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD` query parametrelerini kabul eder.

### 7.7 Profile & Settings

| Method | URI | Middleware | Açıklama |
|--------|-----|-----------|----------|
| `GET`    | `/api/profile`         | `auth:sanctum` | Mevcut kullanıcı |
| `PUT`    | `/api/profile`         | `auth:sanctum` | Profil güncelle (name, email) |
| `POST`   | `/api/profile/photo`   | `auth:sanctum` | Profil fotoğrafı yükle (multipart) |
| `DELETE` | `/api/profile/photo`   | `auth:sanctum` | Profil fotoğrafını sil |
| `GET`    | `/api/settings`        | `auth:sanctum` | currency + locale |
| `PUT`    | `/api/settings`        | `auth:sanctum` | currency / locale güncelle |

### 7.8 Standart response zarfı

**Liste response (paginated):**

```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 0
  }
}
```

**Tek kayıt:**

```json
{
  "success": true,
  "data": { "id": 1, "name": "...", "...": "..." }
}
```

**Mutation (create/update/delete):**

```json
{
  "success": true,
  "message": "İşlem güncellendi."
}
```

**Validation hatası (HTTP 422):**

```json
{
  "message": "The email field is required. (and 1 more error)",
  "errors": {
    "email":    ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

**Auth gereken çağrıda token yok (HTTP 401):**

```json
{ "message": "Unauthenticated." }
```

**Auth başarılı (login/register, HTTP 200/201):**

```json
{
  "token": "1|47z0437Qxa8PEPgsm06cXJKpcQvSFme7IKY3lOnw...",
  "token_type": "Bearer",
  "user": { "id": 1, "name": "Test", "email": "test@example.com" }
}
```

---

## 8. Test Altyapısı

`tests/` altındaki mevcut dosyalar:

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── AuthenticationTest.php
│   │   ├── EmailVerificationTest.php
│   │   ├── PasswordConfirmationTest.php
│   │   ├── PasswordResetTest.php
│   │   ├── PasswordUpdateTest.php
│   │   └── RegistrationTest.php
│   ├── ExampleTest.php
│   └── ProfileTest.php
├── Unit/
│   └── ExampleTest.php
└── TestCase.php
```

### Mevcut durum

- **Yalnızca Breeze hazır testleri ve `ExampleTest` boilerplate'i bulunur.**
- Tüm domain mantığı (`TransactionService`, `BudgetService`, `GoalService`,
  `ReportRepository`, IDOR senaryoları, token akışı) **test edilmemiştir**.
- PR-05 sonrası tahmini coverage: **~%8** (yalnızca framework hazır parçaları).
- Hedef: **%60** (PR-13 — Domain test paketi).

### Çalıştırma

```bash
php artisan test
# veya
vendor/bin/phpunit
# veya composer script:
composer test
```

### Eklenecek test ailesi (PR-13 kapsamında)

- `TransactionServiceTest` — IDOR, bütçe tetikleme, filtre, pagination
- `BudgetServiceTest` — `checkBudgetStatus` `exceeded` durumu, notify
- `ReportRepositoryTest` — `yearMonthExpression` MySQL/SQLite/PgSQL doğrulama
- `ApiAuthControllerTest` — login/register/logout token akışı
- `AuthorizationTest` — başka kullanıcının kaynağına erişim engellenir mi

---

## 9. Geliştirici Akışı

### 9.1 Branch stratejisi

| Prefix | Kullanım |
|--------|----------|
| `master` | Her zaman çalışır, deploy edilebilir |
| `feat/*` | Yeni özellik |
| `fix/*` | Hata düzeltme |
| `refactor/*` | Davranış değişmeyen yeniden yapılandırma |
| `docs/*` | Dokümantasyon |
| `security/*` | Güvenlik yaması |
| `chore/*` | Bağımlılık güncellemesi, build |

### 9.2 Commit formatı (Conventional Commits)

```
feat(transactions): add recurring transaction support
fix(auth): resolve Sanctum guard not defined error
refactor(budget): extract notification logic to BudgetAlertNotifier
security(idor): add category ownership validation to StoreTransactionRequest
docs(readme): update API surface and architecture sections
chore(deps): bump laravel/sanctum to ^4.4
```

Mesaj gövdesi **ne** yapıldığından çok **neden** yapıldığını anlatmalı:
hangi sorun çözüldü, hangi alternatif düşünüldü, hangi davranışsal
sonuçları olduğu.

### 9.3 Kod kalite kontrolleri

Mevcut:

```bash
php artisan test          # PHPUnit
vendor/bin/pint           # PSR-12 + Laravel kuralları (kurulu, devre dışı değil)
```

İlerleyen PR'larda eklenecek:

```bash
vendor/bin/phpstan analyse --level=6   # statik analiz (PR-12 adayı)
npm run lint                            # frontend ESLint
npm run typecheck                       # frontend TS
```

### 9.4 PR akışı

1. Master'dan yeni branch (`fix/`, `feat/` vb.) aç.
2. Küçük, kendi içinde tamamlanmış commit'ler at.
3. Pre-merge checklist:
   - Yeni endpoint varsa README §7'ye satır eklendi mi?
   - Yeni servis/repository varsa §2.2 / §2.3 güncel mi?
   - Migration eklendiyse `php artisan migrate` taze DB'de çalıştırıldı mı?
   - Test eklenmemişse PR description'da nedeni belirtildi mi?
4. PR description'a **before/after** davranışı yaz (özellikle bug fix'lerde).

---

## 10. Yol Haritası

| Faz | PR | Açıklama | Durum |
|-----|------|----------|-------|
| **Faz 1 — Altyapı** | PR-01 | API route dosyasının `bootstrap/app.php`'de aktive edilmesi | ✅ Tamamlandı |
| Faz 1 | PR-02 | Sanctum kurulumu, `sanctum` + `api` guard tanımı, CORS | ✅ Tamamlandı |
| Faz 1 | PR-03 | Controller `$this->middleware()` çağrılarının route layer'a taşınması, `Http\Kernel.php` silinmesi | ✅ Tamamlandı |
| Faz 1 | PR-04 | `ApiAuthController` (login/register/logout token akışı) + `UserServiceInterface` | ✅ Tamamlandı |
| Faz 1 | PR-05 | `BudgetService::checkBudgetsForTransaction` + driver-aware `ReportRepository` + MySQL migration | ✅ Tamamlandı |
| **Faz 2 — Güvenlik** | PR-06 | IDOR koruması: `category_id` user-scope validation; throttle revize | 🔄 Planlandı |
| Faz 2 | PR-07 | Mass assignment sıkılaştırma, `User::setPasswordAttribute` workaround kaldırma | 🔄 Planlandı |
| Faz 2 | PR-08 | `UpdateProfilePhotoRequest` MIME/size/dimensions kuralları | 🔄 Planlandı |
| **Faz 3 — Kalite** | PR-09 | `Policy` sınıfları + `Gate::authorize`; service'lerden manuel ownership check'leri kaldırma | 🔄 Planlandı |
| Faz 3 | PR-10 | `TransactionType`, `ReportPeriod`, `Currency` enum'ları; magic string temizliği | 🔄 Planlandı |
| Faz 3 | PR-11 | `BaseApiController` + `ApiResponse` trait — JsonResponse boilerplate tekrarını yok et | 🔄 Planlandı |
| Faz 3 | PR-12 | Larastan level 6+ statik analiz; CI'da enforce | 🔄 Planlandı |
| Faz 3 | PR-13 | Domain test paketi (Transaction/Budget/Goal/Report/Auth/IDOR) — hedef %60 coverage | 🔄 Planlandı |
| **Faz 4 — Özellikler** | PR-14 | Çoklu hesap (banka / kart / nakit) — `accounts` tablosu, `Transaction.account_id`, transfer | 🔄 Planlandı |
| Faz 4 | PR-15 | Yatırım takibi — hisse/fon/kripto portföyü, gerçek-zamanlı değerleme | 🔄 Planlandı |
| Faz 4 | PR-16 | Döviz ve emtia takibi (TRY/USD/EUR/altın/gümüş) — günlük kurla çoklu para birimi | 🔄 Planlandı |
| Faz 4 | PR-17 | Tekrarlayan işlemler (recurring) — kira, abonelik, fatura | 🔄 Planlandı |
| Faz 4 | PR-18 | Excel + PDF export (`maatwebsite/excel` + `dompdf`) | 🔄 Planlandı |
| Faz 4 | PR-19 | Bildirim genişletme — push, Slack, gelişmiş eşik ayarları | 🔄 Planlandı |
| **Faz 5 — Olgunluk** | PR-20 | 2FA + OpenAPI/Swagger dokümantasyonu + GitHub Actions CI | 🔄 Planlandı |

---

## Lisans

MIT — kaynak için `composer.json`'daki `license` alanına bakın.
