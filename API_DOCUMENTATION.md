# 📘 DOKUMENTASI API MINDCARE

## 🌐 Base URL
```
http://127.0.0.1:8000/api
```

## 🔑 Autentikasi
Semua endpoint yang dilindungi membutuhkan token Bearer di header:
```
Authorization: Bearer [token]
```

---

## 📋 DAFTAR ENDPOINT

### 1. AUTH

#### Register
```
POST /api/register
Content-Type: application/json

{
  "name": "Budi Santoso",
  "email": "budi@email.com",
  "username": "budi123",
  "umur": 20,
  "gender": "Laki-laki",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```
POST /api/login
Content-Type: application/json

{
  "email": "budi@email.com",
  "password": "password123"
}
```

#### Logout (Auth)
```
POST /api/logout
Authorization: Bearer [token]
```

#### Profile (Auth)
```
GET /api/profile
Authorization: Bearer [token]
```

---

### 2. MOOD CHECK

#### Ambil Pertanyaan (Auth)
```
GET /api/mood-questions
Authorization: Bearer [token]
```

#### Submit Mood Check (Auth)
```
POST /api/mood-check
Authorization: Bearer [token]
Content-Type: application/json

{
  "answers": [
    {"question_id": 1, "option_id": 2},
    {"question_id": 2, "option_id": 3}
  ]
}
```

#### Riwayat Mood (Auth)
```
GET /api/mood-history
Authorization: Bearer [token]
```

---

### 3. JURNAL

#### List Jurnal (Auth)
```
GET /api/journals
Authorization: Bearer [token]
```

#### Buat Jurnal (Auth)
```
POST /api/journals
Authorization: Bearer [token]
Content-Type: application/json

{
  "title": "Hari ini",
  "content": "Isi jurnal...",
  "mood_before": 4,
  "mood_after": 7,
  "journal_date": "2026-05-19"
}
```

#### Detail Jurnal (Auth)
```
GET /api/journals/{id}
Authorization: Bearer [token]
```

#### Update Jurnal (Auth)
```
PUT /api/journals/{id}
Authorization: Bearer [token]
Content-Type: application/json

{
  "title": "Update",
  "content": "Update isi...",
  "mood_before": 4,
  "mood_after": 8,
  "journal_date": "2026-05-19"
}
```

#### Hapus Jurnal (Auth)
```
DELETE /api/journals/{id}
Authorization: Bearer [token]
```

---

### 4. ARTIKEL

#### List Artikel
```
GET /api/articles
GET /api/articles?category=Self%20Care
GET /api/articles?mood_tag=stres_ringan
GET /api/articles?search=meditasi
```

#### Detail Artikel
```
GET /api/articles/{id}
```

---

### 5. CHATBOT

#### Kirim Pesan (Auth)
```
POST /api/chatbot/send
Authorization: Bearer [token]
Content-Type: application/json

{
  "message": "Aku lagi stres"
}
```

#### History Chat (Auth)
```
GET /api/chatbot/history
Authorization: Bearer [token]
```

---

## 📊 FORMAT RESPONSE

### Response Sukses
```json
{
  "success": true,
  "message": "Berhasil",
  "data": { ... }
}
```

### Response Error
```json
{
  "success": false,
  "message": "Pesan error",
  "errors": { ... }
}
```

## 📝 KODE STATUS
- 200: OK
- 201: Created
- 401: Unauthorized
- 404: Not Found
- 422: Validation Error
- 429: Too Many Requests
- 500: Server Error