## Tech Stack
- **Backend**: Laravel (PHP)
- **Database**: PostgreSQL
- **Cache**: Redis
- **Object Storage**: MinIO (S3 compatible)
- **Local Environment**: Laravel Herd
- **Dependency Manager**: Composer

---

## Setup Aplikasi

### 1. Clone Repository
```bash
git clone https://github.com/<your-username>/php-fullstact-test.git
cd php-fullstact-test
````

Jika struktur repository berisi folder `api/`, masuk ke folder tersebut:

```bash
cd api
```

---

### 2. Install Dependency

```bash
composer install
```

---

### 3. Konfigurasi Environment

Salin file environment:

```bash
cp .env.example .env
```

Edit `.env` dan sesuaikan konfigurasi berikut.

#### Database (PostgreSQL)

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=peepl
DB_USERNAME=peepl
DB_PASSWORD=peepl
```

#### Redis

```env
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

#### MinIO (S3 Compatible)

```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin123
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=peepl-bucket
AWS_ENDPOINT=http://127.0.0.1:9000
AWS_USE_PATH_STYLE_ENDPOINT=true
```

---

### 4. Generate Application Key

```bash
php artisan key:generate
```

---

### 5. Migrasi Database

```bash
php artisan migrate
```

Migrasi ini akan membuat tabel `my_client` sesuai SQL schema yang diberikan, termasuk kolom `deleted_at` untuk **soft delete**.

---

### 6. Menjalankan Aplikasi (Laravel Herd)

Daftarkan project ke Herd:

```bash
herd link peepl-api
```

Akses aplikasi di:

```
http://peepl-api.test
```

---

## API Endpoints & Pengujian Requirement

### No 1 – GET List Client

```http
GET /api/my-clients
```

* Jika database masih kosong, respons:

```json
[]
```

* Setelah data ditambahkan, respons berupa array client.

---

### No 2 – CREATE Client + Upload Logo

```http
POST /api/my-clients
```

Body type: **multipart/form-data**

| Field         | Value              |
| ------------- | ------------------ |
| name          | PT Test            |
| slug          | pt-test            |
| is_project    | 0                  |
| self_capture  | 1                  |
| client_prefix | TEST               |
| address       | Jalan 123          |
| phone_number  | 08123456789        |
| city          | Jakarta            |
| client_logo   | file (.jpg / .png) |

Hasil:

* Data tersimpan di PostgreSQL
* File logo ter-upload ke MinIO
* Kolom `client_logo` berisi URL file
* Redis otomatis membuat key dengan nama slug (`pt-test`) dan value JSON client

---

### No 3 – GET Client by Slug

```http
GET /api/my-clients/slug/{slug}
```

Contoh:

```http
GET /api/my-clients/slug/pt-test
```

Endpoint ini:

* Mengambil data berdasarkan slug
* Menggunakan Redis sebagai cache utama

---

### No 4 – UPDATE Client (Slug Change)

```http
PUT /api/my-clients/{id}
```

Contoh payload:

```json
{
  "slug": "pt-test-2"
}
```
Hasil: 

* Redis key lama (`pt-test`) dihapus
* Redis key baru (`pt-test-2`) dibuat
* Data di PostgreSQL diperbarui

---

### No 5 – DELETE Client (Soft Delete)

```http
DELETE /api/my-clients/{id}
```

Hasil:

* Kolom `deleted_at` terisi (soft delete)
* Data tidak muncul di list
* Redis key dihapus

---

## Verifikasi Redis (Opsional)

Masuk ke tinker:

```bash
php artisan tinker
```

Hasil:

```php
\Illuminate\Support\Facades\Redis::connection()->get('pt-test-2');
```

---

## Catatan Kesesuaian SQL

* Tabel menggunakan nama **`my_client`**.
* Beberapa kolom menggunakan tipe **CHAR**, sehingga PostgreSQL menambahkan trailing spaces.
  Data dinormalisasi (trim) di layer aplikasi agar:

  * output API bersih
  * slug Redis konsisten
* Kolom `is_project` dibatasi nilai `'0'` atau `'1'`.
* Soft delete menggunakan kolom `deleted_at`.

---

## Kesimpulan

* CRUD PostgreSQL
* Redis cache berbasis slug
* Upload file ke S3-compatible storage
* Update dan delete cache sesuai perubahan data
* Soft delete sesuai SQL schema

