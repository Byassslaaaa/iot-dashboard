# ESP32 Setup Guide

Panduan konfigurasi ESP32 untuk Smart Trash IoT Dashboard.

## Konfigurasi Credentials

Sebelum upload code ke ESP32, Anda perlu mengisi credentials di file `esp32_smart_trash.ino`:

### 1. WiFi Configuration

```cpp
const char* ssid = "YOUR_WIFI_SSID";           // Nama WiFi Anda
const char* password = "YOUR_WIFI_PASSWORD";    // Password WiFi
```

**Contoh:**
```cpp
const char* ssid = "MyHomeWiFi";
const char* password = "mypassword123";
```

### 2. Telegram Bot

Untuk mendapatkan Telegram Bot Token dan Chat ID:

#### a. Buat Bot Baru
1. Buka Telegram, cari **@BotFather**
2. Kirim `/newbot`
3. Ikuti instruksi, beri nama bot Anda
4. Copy **Bot Token** yang diberikan

#### b. Dapatkan Chat ID
1. Cari bot Anda di Telegram
2. Kirim pesan `/start`
3. Buka browser: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
4. Cari `"chat":{"id":123456789}` - ini adalah Chat ID Anda

**Update di code:**
```cpp
#define BOT_TOKEN "1234567890:ABCdefGHIjklMNOpqrsTUVwxyz"
#define CHAT_ID  "123456789"
```

### 3. Dashboard API URL

Cari IP address komputer yang menjalankan Laravel:

**Windows:**
```bash
ipconfig
# Cari "IPv4 Address" di WiFi adapter
```

**Mac/Linux:**
```bash
ifconfig
# atau
ip addr show
```

**Update di code:**
```cpp
const char* dashboardUrl = "http://192.168.1.4:8000/api/sensor/data";
//                               ^^^^^^^^^^^^^ ganti dengan IP Anda
```

## Hardware Requirements

### Components
- ESP32 DevKit V1
- HC-SR04 Ultrasonic Sensor
- IR Obstacle Sensor
- SG90 Servo Motor
- Buzzer (Active)
- OLED Display SSD1306 128x64 (I2C)
- Breadboard & Jumper Wires
- Power Supply 5V

### Pin Connections

| Component | ESP32 Pin |
|-----------|-----------|
| Ultrasonic TRIG | GPIO 12 |
| Ultrasonic ECHO | GPIO 27 |
| IR Sensor OUT | GPIO 26 |
| Buzzer + | GPIO 25 |
| Servo Signal | GPIO 23 |
| OLED SDA | GPIO 21 |
| OLED SCL | GPIO 22 |
| VCC (All) | 5V |
| GND (All) | GND |

## Arduino IDE Setup

### 1. Install ESP32 Board

1. Buka **File → Preferences**
2. Di "Additional Board Manager URLs", tambahkan:
   ```
   https://raw.githubusercontent.com/espressif/arduino-esp32/gh-pages/package_esp32_index.json
   ```
3. Buka **Tools → Board → Boards Manager**
4. Cari "esp32", install **ESP32 by Espressif Systems**

### 2. Install Libraries

Buka **Sketch → Include Library → Manage Libraries**, install:

- **Adafruit GFX Library** by Adafruit
- **Adafruit SSD1306** by Adafruit
- **ESP32Servo** by Kevin Harrington
- **UniversalTelegramBot** by Brian Lough
- **ArduinoJson** by Benoit Blanchon (dependency)

### 3. Board Settings

- **Board**: ESP32 Dev Module
- **Upload Speed**: 115200
- **Flash Frequency**: 80MHz
- **Flash Mode**: QIO
- **Flash Size**: 4MB
- **Partition Scheme**: Default 4MB
- **Core Debug Level**: None
- **Port**: (pilih COM port ESP32 Anda)

## Upload & Testing

### 1. Upload Code
1. Hubungkan ESP32 ke komputer via USB
2. Pilih Port yang sesuai di **Tools → Port**
3. Klik **Upload** (atau tekan Ctrl+U)
4. Tunggu sampai "Done uploading"

### 2. Monitor Serial
1. Buka **Tools → Serial Monitor**
2. Set baud rate ke **115200**
3. Perhatikan output:
   ```
   Connecting to WiFi...
   WiFi Connected
   IP Address: 192.168.1.xxx

   Sending to dashboard: {"distance":15,"ir_triggered":false,"servo_position":0,"buzzer_active":false}
   Dashboard Response: 200
   ```

### 3. Testing

**Test Ultrasonic:**
- Dekatkan tangan < 20cm
- Servo harus membuka (90°)
- OLED menampilkan "STATUS: OPEN"

**Test IR Sensor:**
- Block IR sensor
- Buzzer berbunyi
- OLED menampilkan "FULL"
- Telegram mendapat notifikasi

**Test Dashboard:**
- Buka `http://localhost:8000`
- Login ke dashboard
- Buka "Live Monitoring"
- Data harus update setiap 5 detik

## Troubleshooting

### WiFi Not Connecting
- Pastikan SSID dan password benar
- Cek jarak ESP32 ke router
- Gunakan WiFi 2.4GHz (bukan 5GHz)

### Dashboard Error
- Pastikan Laravel server berjalan (`php artisan serve`)
- Cek IP address sudah benar
- Pastikan ESP32 dan komputer di network yang sama
- Test manual dengan curl/Postman

### Telegram Not Working
- Verifikasi Bot Token dan Chat ID
- Kirim `/start` ke bot dulu
- Cek koneksi internet

### Sensor Issues
- Cek koneksi kabel
- Verifikasi pin GPIO
- Test sensor satu-persatu dengan code terpisah

## Data Flow

```
ESP32 Sensors → Read Data (every 200ms)
    ↓
Check Status (FULL/NORMAL)
    ↓
Update OLED Display
    ↓
Send to Dashboard (every 5 seconds)
    ↓
Laravel API stores data
    ↓
Dashboard updates real-time
```

## Power Consumption

- **Idle**: ~80mA
- **Active (WiFi)**: ~120mA
- **Servo Moving**: +200mA
- **Buzzer Active**: +50mA

Recommended: 5V 2A power supply

## Notes

- ESP32 harus terhubung ke WiFi yang sama dengan komputer
- Dashboard harus running sebelum ESP32 dinyalakan
- Telegram notifikasi hanya sekali saat pertama full
- Data dikirim ke dashboard setiap 5 detik
- OLED akan menampilkan status koneksi

## Support

Jika ada masalah, cek:
1. Serial Monitor output
2. Network connectivity
3. Pin connections
4. Library versions

---

**Happy Coding!** 🚀
