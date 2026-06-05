# 🧠 MindCare Apps

> Backend API untuk aplikasi kesehatan mental berbasis AI — membantu pengguna memantau suasana hati, menulis jurnal, dan berinteraksi dengan chatbot empatik.

---

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Instalasi & Setup](#instalasi--setup)
- [Environment Variables](#environment-variables)
- [API Endpoints](#api-endpoints)
- [Struktur Proyek](#struktur-proyek)
- [Lisensi](#lisensi)

---

## Tentang Proyek

**MindCare Apps** adalah RESTful API backend yang dibangun dengan Laravel 13, dirancang sebagai fondasi untuk aplikasi kesehatan mental. Pengguna dapat melakukan mood check-in harian, menulis jurnal pribadi, membaca artikel kesehatan, dan mengobrol dengan chatbot bertenaga AI yang responsif dan empatik.

---

## Fitur Utama

- 🔐 **Autentikasi** — Register, login, logout, dan hapus akun menggunakan Laravel Sanctum
- 😊 **Mood Check** — Kuesioner suasana hati harian dengan riwayat lengkap
- 📓 **Jurnal Harian** — Buat dan baca, jurnal pribadi dengan skor mood sebelum & sesudah menulis
- 📰 **Artikel** — Konten kesehatan mental yang bisa dibaca
- 🤖 **Chatbot AI** — Percakapan dengan AI empatik beserta riwayat chat tersimpan
- 📊 **Daily Summary** — Ringkasan harian aktivitas pengguna lengkap dengan analisis AI

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 13 |
| PHP | ^8.3 |
| Autentikasi | Laravel Sanctum |
| Database | SQLite (default) / MySQL |
| AI Integration | HTTP Client ke external AI API |

---

## Instalasi & Setup

### Prasyarat

- PHP >= 8.3
- Composer
- Node.js & NPM

### Langkah Instalasi

**1. Clone repositori**
```bash
git clone https://github.com/username/MindCareApps.git
cd MindCareApps
```

**2. Jalankan setup otomatis** *(install semua dependency, generate key, dan migrasi)*
```bash
composer run setup
```

Atau lakukan secara manual:

```bash
# Install dependency PHP
composer install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi & seeder
php artisan migrate --seed
```

**3. Jalankan server development**
```bash
composer run dev
```

API akan berjalan di `http://127.0.0.1:8000`

---

## Environment Variables

Salin `.env.example` menjadi `.env` lalu sesuaikan nilai berikut:

```env
APP_NAME=MindCare
APP_URL=http://localhost

DB_CONNECTION=sqlite
# Untuk MySQL, uncomment dan isi:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=mindcare
# DB_USERNAME=root
# DB_PASSWORD=

# API Key untuk integrasi AI (chatbot & analisis)
GEMINI_API_KEY=your_api_key_here
```

---

## API Endpoints

Base URL: `http://127.0.0.1:8000/api`

Semua endpoint bertanda 🔒 membutuhkan header:
```
Authorization: Bearer {token}
```

### Auth

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/register` | Daftar akun baru |
| POST | `/login` | Login & dapatkan token |
| POST | `/logout` | 🔒 Logout |
| GET | `/profile` | 🔒 Lihat profil |
| DELETE | `/account` | 🔒 Hapus akun |

### Mood Check

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/mood-questions` | 🔒 Ambil daftar pertanyaan mood |
| POST | `/mood-check` | 🔒 Submit jawaban mood check |
| GET | `/mood-history` | 🔒 Riwayat mood check |

### Jurnal

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/journals` | 🔒 List semua jurnal |
| POST | `/journals` | 🔒 Buat jurnal baru |

### Artikel

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/articles` | List artikel *(publik)* |
| GET | `/articles/{id}` | Detail artikel *(publik)* |

### Chatbot

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/chatbot/send` | 🔒 Kirim pesan ke chatbot AI |
| GET | `/chatbot/history` | 🔒 Riwayat percakapan |

### Daily Summary

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/daily-summary` | 🔒 Ringkasan harian |
| POST | `/daily-summary/ai-analysis` | 🔒 Analisis AI dari aktivitas hari ini |

### Format Response

**Sukses:**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": { }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Pesan error",
  "errors": { }
}
```

**HTTP Status Codes:** `200` OK · `201` Created · `401` Unauthorized · `404` Not Found · `422` Validation Error · `500` Server Error

---

## Struktur Proyek

```
MindCareApps/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── MoodCheckController.php
│   │   ├── JournalController.php
│   │   ├── ArticleController.php
│   │   ├── ChatbotController.php
│   │   └── DailySummaryController.php
│   └── Models/
│       ├── User.php
│       ├── MoodCheckin.php
│       ├── MoodQuestion.php
│       ├── DailyJournal.php
│       ├── Article.php
│       └── ChatbotMessage.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── .env.example
└── API_DOCUMENTATION.md
```
