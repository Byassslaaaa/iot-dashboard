# ESP32 Setup Guide

Panduan lengkap konfigurasi ESP32 untuk Smart Trash IoT Dashboard.

## Prerequisites

- ESP32 DevKit V1
- Arduino IDE (versi 1.8.x atau 2.x)
- Kabel USB micro
- Komputer dengan WiFi

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

⚠️ **Penting**: Gunakan WiFi 2.4GHz, bukan 5GHz (ESP32 tidak support 5GHz)

### 2. Telegram Bot Configuration

Untuk mendapatkan Telegram Bot Token dan Chat ID:

#### a. Buat Bot Baru di Telegram

1. Buka aplikasi Telegram
2. Cari **@BotFather**
3. Kirim pesan `/newbot`
4. Ikuti instruksi:
   - Beri nama bot: `Smart Trash Alert Bot`
   - Beri username: `smarttrash_alert_bot` (harus unik)
5. Copy **Bot Token** yang diberikan (contoh: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)

#### b. Dapatkan Chat ID

1. Cari bot Anda di Telegram
2. Klik **Start** atau kirim `/start`
3. Buka browser, akses URL berikut (ganti YOUR_BOT_TOKEN):
   ```
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates
   ```
4. Cari bagian `"chat":{"id":123456789}` - angka tersebut adalah Chat ID Anda

**Update di code:**
```cpp
#define BOT_TOKEN "123456789:ABCdefGHIjklMNOpqrsTUVwxyz"
#define CHAT_ID  "123456789"
```

### 3. Dashboard API URL

Cari IP address komputer yang menjalankan Laravel:

**Windows:**
```bash
ipconfig
# Cari "IPv4 Address" di adapter WiFi yang aktif
# Contoh: 192.168.1.4
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
//                               ^^^^^^^^^^^^^ ganti dengan IP komputer Anda
```

⚠️ **Catatan**: ESP32 dan komputer harus terhubung ke WiFi yang sama!

## Hardware Requirements

### Components List

| No | Component | Quantity | Notes |
|----|-----------|----------|-------|
| 1 | ESP32 DevKit V1 | 1 | Main controller |
| 2 | HC-SR04 Ultrasonic | 1 | Distance sensor |
| 3 | IR Obstacle Sensor | 1 | Full detection |
| 4 | SG90 Servo Motor | 1 | Lid control |
| 5 | Active Buzzer | 1 | Alert sound |
| 6 | OLED SSD1306 128x64 | 1 | I2C display |
| 7 | Breadboard 830 points | 1 | Prototyping |
| 8 | Jumper Wires M-M | 20+ | Connections |
| 9 | Power Supply 5V 2A | 1 | Recommended |

### Pin Connections Diagram

```
ESP32 DevKit V1
┌─────────────────────────┐
│                         │
│  GPIO 12 ────────────── TRIG (Ultrasonic)
│  GPIO 27 ────────────── ECHO (Ultrasonic)
│  GPIO 26 ────────────── OUT (IR Sensor)
│  GPIO 25 ────────────── + (Buzzer)
│  GPIO 23 ────────────── Signal (Servo)
│  GPIO 21 ────────────── SDA (OLED)
│  GPIO 22 ────────────── SCL (OLED)
│                         │
│  5V ─────────────────── VCC (All sensors)
│  GND ────────────────── GND (All sensors)
│                         │
└─────────────────────────┘
```

**Connection Details:**

| Component | Pin | Wire to ESP32 |
|-----------|-----|---------------|
| **HC-SR04** |  |  |
| VCC | → | 5V |
| GND | → | GND |
| TRIG | → | GPIO 12 |
| ECHO | → | GPIO 27 |
| **IR Sensor** |  |  |
| VCC | → | 5V |
| GND | → | GND |
| OUT | → | GPIO 26 |
| **Buzzer** |  |  |
| + | → | GPIO 25 |
| - | → | GND |
| **Servo SG90** |  |  |
| Red (VCC) | → | 5V |
| Brown (GND) | → | GND |
| Orange (Signal) | → | GPIO 23 |
| **OLED Display** |  |  |
| VCC | → | 3.3V |
| GND | → | GND |
| SDA | → | GPIO 21 |
| SCL | → | GPIO 22 |

## Arduino IDE Setup

### 1. Install ESP32 Board Support

1. Buka Arduino IDE
2. Klik **File → Preferences**
3. Di bagian "Additional Board Manager URLs", tambahkan:
   ```
   https://raw.githubusercontent.com/espressif/arduino-esp32/gh-pages/package_esp32_index.json
   ```
4. Klik **OK**
5. Buka **Tools → Board → Boards Manager**
6. Cari "**esp32**"
7. Install "**ESP32 by Espressif Systems**" (versi 2.0.x atau lebih baru)

### 2. Install Required Libraries

Buka **Sketch → Include Library → Manage Libraries**, cari dan install:

| Library | Author | Version |
|---------|--------|---------|
| Adafruit GFX Library | Adafruit | Latest |
| Adafruit SSD1306 | Adafruit | Latest |
| ESP32Servo | Kevin Harrington | Latest |
| UniversalTelegramBot | Brian Lough | Latest |
| ArduinoJson | Benoit Blanchon | 6.x.x |

### 3. Board Configuration

Pilih settings berikut di menu **Tools**:

- **Board**: "ESP32 Dev Module"
- **Upload Speed**: 115200
- **CPU Frequency**: 240MHz (WiFi/BT)
- **Flash Frequency**: 80MHz
- **Flash Mode**: QIO
- **Flash Size**: 4MB (32Mb)
- **Partition Scheme**: Default 4MB
- **Core Debug Level**: None
- **PSRAM**: Disabled
- **Port**: (pilih COM port ESP32 Anda, contoh: COM3)

## Upload Code

### 1. Persiapan

1. Hubungkan ESP32 ke komputer via USB
2. Tunggu driver terinstall otomatis
3. Buka Device Manager (Windows) untuk cek COM port
4. Buka file `esp32_smart_trash.ino` di Arduino IDE

### 2. Update Credentials

Edit baris berikut dengan credentials Anda:

```cpp
// Line 40-41: WiFi
const char* ssid = "NamaWiFiAnda";
const char* password = "PasswordWiFiAnda";

// Line 43-44: Telegram
#define BOT_TOKEN "YourBotToken"
#define CHAT_ID  "YourChatID"

// Line 49: Dashboard URL
const char* dashboardUrl = "http://192.168.1.4:8000/api/sensor/data";
```

### 3. Compile & Upload

1. Klik **Verify** (✓) untuk compile
2. Tunggu sampai "Done compiling"
3. Klik **Upload** (→) untuk upload
4. Tunggu proses upload selesai
5. Jika muncul "Connecting...", tekan tombol **BOOT** di ESP32

**Expected Output:**
```
Connecting........_____....
Writing at 0x00010000... (100 %)
Wrote 847872 bytes in 14.2 seconds
Hard resetting via RTS pin...
```

## Testing & Debugging

### 1. Serial Monitor

1. Buka **Tools → Serial Monitor**
2. Set baud rate ke **115200**
3. Perhatikan output:

**Expected Output:**
```
Connecting to WiFi...
WiFi Connected
IP Address: 192.168.1.100

Sending to dashboard: {"distance":15,"ir_triggered":false,"servo_position":0,"buzzer_active":false}
Dashboard Response: 200
{"success":true,"data":{"reading_id":1,"status":"empty","capacity":50}}
```

### 2. OLED Display Test

**Normal Mode:**
```
Jarak: 15 cm
STATUS: READY
WiFi: Connected
Dashboard: Active
```

**Object Detected (< 20cm):**
```
Jarak: 8 cm
STATUS: OPEN
WiFi: Connected
Dashboard: Active
```

**Full Mode (IR triggered):**
```
  ○   ○
 ⚫  ⚫

  FULL
```

### 3. Component Testing

**Test Ultrasonic:**
- Dekatkan tangan < 20cm dari sensor
- Servo harus membuka (90°)
- OLED menampilkan "STATUS: OPEN"
- Setelah 2 detik (tanpa objek), servo menutup (0°)

**Test IR Sensor:**
- Letakkan objek di depan IR sensor (LED IR menyala)
- Buzzer harus berbunyi
- OLED menampilkan emoji mata + "FULL"
- Telegram mendapat notifikasi "PERINGATAN! Tempat sampah TERISI PENUH"

**Test Dashboard Connection:**
- Pastikan Laravel server running: `php artisan serve`
- Buka browser: `http://localhost:8000`
- Login dengan credentials default

  ![Login Page](public/login.png)

  - Email: `admin@smarttrash.com`
  - Password: `password`

- Buka menu **Live Monitoring**
- Data sensor harus update setiap 5 detik

## Troubleshooting

### WiFi Connection Issues

**Problem**: ESP32 tidak connect ke WiFi

**Solutions:**
- ✓ Cek SSID dan password sudah benar (case-sensitive)
- ✓ Pastikan menggunakan WiFi 2.4GHz (bukan 5GHz)
- ✓ Dekatkan ESP32 ke router
- ✓ Cek apakah router mengizinkan koneksi baru
- ✓ Restart ESP32 dan router
- ✓ Coba WiFi hotspot dari HP untuk testing

### Dashboard Connection Error

**Problem**: Error sending to dashboard (-1)

**Solutions:**
- ✓ Pastikan Laravel server berjalan: `php artisan serve`
- ✓ Cek IP address sudah benar dengan `ipconfig`
- ✓ Pastikan ESP32 dan komputer di WiFi yang sama
- ✓ Test manual dengan Postman/curl:
  ```bash
  curl -X POST http://192.168.1.4:8000/api/sensor/data \
    -H "Content-Type: application/json" \
    -d '{"distance":15,"ir_triggered":false,"servo_position":0,"buzzer_active":false}'
  ```
- ✓ Cek firewall komputer (allow port 8000)
- ✓ Gunakan `php artisan serve --host=0.0.0.0` untuk listen semua interface

### Telegram Not Working

**Problem**: Tidak mendapat notifikasi Telegram

**Solutions:**
- ✓ Verifikasi Bot Token dan Chat ID benar
- ✓ Kirim `/start` ke bot dulu sebelum test
- ✓ Cek koneksi internet ESP32
- ✓ Test bot manual via Telegram
- ✓ Lihat Serial Monitor untuk error message

### Sensor Issues

**Ultrasonic tidak akurat:**
- Pastikan TRIG dan ECHO tidak tertukar
- Jangan ada halangan di depan sensor
- Bersihkan sensor dari debu

**IR Sensor selalu triggered:**
- Cek jarak objek (terlalu dekat)
- Adjust sensitivity dengan potensiometer di sensor
- Cek wiring, pastikan tidak ada short circuit

**Servo tidak bergerak:**
- Cek power supply cukup (minimal 5V 2A)
- Pastikan pin signal terhubung ke GPIO 23
- Test servo dengan code terpisah
- Ganti servo jika rusak

**OLED tidak menyala:**
- Cek alamat I2C (default 0x3C)
- Scan I2C address dengan I2C Scanner sketch
- Pastikan SDA/SCL tidak tertukar
- Gunakan pull-up resistor 4.7kΩ jika perlu

### Upload Issues

**Problem**: Upload gagal atau stuck "Connecting..."

**Solutions:**
- Tekan dan tahan tombol **BOOT** saat upload
- Lepas tombol setelah "Connecting..." berubah menjadi "Writing..."
- Gunakan kabel USB yang bagus (bukan charging-only)
- Install driver CP210x atau CH340 jika perlu
- Coba port USB lain
- Reduce upload speed ke 921600 atau 460800

## Power Consumption & Battery

### Current Draw

| Condition | Current | Voltage |
|-----------|---------|---------|
| Idle (WiFi off) | ~40mA | 5V |
| WiFi Connected | ~80-120mA | 5V |
| Servo Active | +200mA | 5V |
| Buzzer Active | +50mA | 5V |
| Peak (all active) | ~400mA | 5V |

### Power Options

1. **USB Power** (Recommended for development)
   - Via USB dari komputer
   - Stable 5V supply

2. **Wall Adapter**
   - 5V 2A minimum
   - Use micro USB cable

3. **Battery (Portable)**
   - Power bank 5V
   - 18650 Li-ion 3.7V + Step-up converter to 5V
   - Battery life ~6-8 hours (2000mAh)

## Data Flow Architecture

```
┌─────────────┐
│   ESP32     │
│             │
│  Sensors ◄──┤  Read every 200ms
│    ↓        │
│  Process ◄──┤  Check FULL/NORMAL
│    ↓        │
│  Display ◄──┤  Update OLED
│    ↓        │
│  Dashboard◄─┤  POST every 5s
└─────────────┘
      ↓
      ↓ HTTP POST
      ↓
┌─────────────┐
│  Laravel    │
│  Server     │
│             │
│  API  ◄─────┤  /api/sensor/data
│    ↓        │
│  Database ◄─┤  Store readings
│    ↓        │
│  View ◄─────┤  Real-time update
└─────────────┘
```

## Performance Optimization

### 1. Reduce WiFi Power

```cpp
// Add to setup()
WiFi.setSleep(true);  // Enable WiFi sleep
```

### 2. Adjust Update Interval

```cpp
// Change from 5s to 10s
const unsigned long DASHBOARD_INTERVAL = 10000;
```

### 3. Disable Serial Debug (Production)

```cpp
// Comment out Serial.println()
// Serial.println("Sending to dashboard...");
```

## Advanced Configuration

### Custom Thresholds

Edit nilai threshold di code:

```cpp
// Jarak deteksi objek (default 20cm)
if (distance > 0 && distance < 20) {  // Ubah 20 ke nilai lain

// Delay servo close (default 2 detik)
if (millis() - ultrasonicTimer >= 2000) {  // Ubah 2000 ke nilai lain
```

### Multiple Trash Bins

Untuk monitoring multiple trash bins:
1. Deploy multiple ESP32
2. Each with unique ID
3. Modify API to include bin_id
4. Dashboard shows all bins

## Support & Resources

### Documentation
- ESP32 Datasheet: [espressif.com](https://www.espressif.com/en/products/socs/esp32)
- Arduino ESP32: [docs.espressif.com](https://docs.espressif.com/projects/arduino-esp32/en/latest/)

### Community
- GitHub Issues: Report bugs
- Arduino Forum: Ask questions
- ESP32 Discord: Real-time help

### Common Libraries Docs
- [Adafruit GFX](https://learn.adafruit.com/adafruit-gfx-graphics-library)
- [Adafruit SSD1306](https://github.com/adafruit/Adafruit_SSD1306)
- [UniversalTelegramBot](https://github.com/witnessmenow/Universal-Arduino-Telegram-Bot)

---

## Quick Reference Card

```
┌──────────────────────────────────────┐
│  SMART TRASH - ESP32 QUICK REFERENCE │
├──────────────────────────────────────┤
│                                      │
│  WiFi: YOUR_SSID / YOUR_PASSWORD     │
│  Dashboard: http://YOUR_IP:8000      │
│  Telegram: @YourBotName              │
│                                      │
│  ──────────────────────────────────  │
│  PINS:                               │
│  Ultrasonic: 12 (TRIG), 27 (ECHO)    │
│  IR Sensor:  26                      │
│  Buzzer:     25                      │
│  Servo:      23                      │
│  OLED:       21 (SDA), 22 (SCL)      │
│                                      │
│  ──────────────────────────────────  │
│  BEHAVIOR:                           │
│  < 20cm  → Servo OPEN (90°)         │
│  > 20cm  → Servo CLOSE (0°) delay 2s│
│  IR=LOW  → FULL + Buzzer + Telegram │
│                                      │
│  ──────────────────────────────────  │
│  UPDATE: Every 5 seconds to server   │
│  BAUD:   115200                      │
│  POWER:  5V 2A recommended           │
└──────────────────────────────────────┘
```

---

**Happy Making!** 🚀🗑️

_For questions or issues, check Serial Monitor output first, then refer to Troubleshooting section._
