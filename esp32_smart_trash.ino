/*
 * Smart Trash Bin - ESP32
 * IoT Dashboard Integration
 *
 * Hardware:
 * - ESP32 DevKit V1
 * - HC-SR04 Ultrasonic Sensor
 * - IR Obstacle Sensor
 * - SG90 Servo Motor
 * - Buzzer
 * - OLED SSD1306 128x64
 *
 * Connections:
 * - TRIG: GPIO 12
 * - ECHO: GPIO 27
 * - IR_SENSOR: GPIO 26
 * - BUZZER: GPIO 25
 * - SERVO: GPIO 23
 * - OLED SDA: GPIO 21
 * - OLED SCL: GPIO 22
 */

#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <ESP32Servo.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <UniversalTelegramBot.h>

// ================= PIN =================
#define TRIG 12
#define ECHO 27
#define IR_SENSOR 26
#define BUZZER 25
#define SERVO_PIN 23

// ================= WIFI & TELEGRAM =================
const char* ssid = "YOUR_WIFI_SSID";           // Ganti dengan SSID WiFi Anda
const char* password = "YOUR_WIFI_PASSWORD";    // Ganti dengan password WiFi Anda

#define BOT_TOKEN "YOUR_TELEGRAM_BOT_TOKEN"     // Ganti dengan token bot Telegram
#define CHAT_ID  "YOUR_TELEGRAM_CHAT_ID"        // Ganti dengan chat ID Telegram

// ================= DASHBOARD API =================
// Ganti dengan IP address komputer yang menjalankan Laravel
// Cara cek IP: buka CMD/Terminal, ketik "ipconfig" (Windows) atau "ifconfig" (Mac/Linux)
const char* dashboardUrl = "http://YOUR_SERVER_IP:8000/api/sensor/data";

WiFiClientSecure secured_client;
UniversalTelegramBot bot(BOT_TOKEN, secured_client);

// ================= OBJECT =================
Servo servo;
Adafruit_SSD1306 display(128, 64, &Wire, -1);

// ================= VARIABEL =================
long duration;
int distance;
bool full = false;
bool notifTerkirim = false;
unsigned long ultrasonicTimer = 0;
bool waitingToClose = false;

// Interval untuk mengirim data ke dashboard (5 detik)
unsigned long lastDashboardUpdate = 0;
const unsigned long DASHBOARD_INTERVAL = 5000;

// ================= OLED ALERT =================
void drawEyesWithFullText() {
    display.clearDisplay();
    display.fillCircle(32, 28, 12, WHITE);
    display.fillCircle(36, 30, 4, BLACK);
    display.fillCircle(96, 28, 12, WHITE);
    display.fillCircle(100, 30, 4, BLACK);
    display.setTextSize(2);
    display.setTextColor(WHITE);
    display.setCursor(34, 48);
    display.print("FULL");
    display.display();
}

// ================= SEND TO DASHBOARD =================
void sendToDashboard(int dist, bool irTriggered, int servoPos, bool buzzerActive) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    http.begin(dashboardUrl);
    http.addHeader("Content-Type", "application/json");

    // Buat JSON payload
    String jsonPayload = "{";
    jsonPayload += "\"distance\":" + String(dist) + ",";
    jsonPayload += "\"ir_triggered\":" + String(irTriggered ? "true" : "false") + ",";
    jsonPayload += "\"servo_position\":" + String(servoPos) + ",";
    jsonPayload += "\"buzzer_active\":" + String(buzzerActive ? "true" : "false");
    jsonPayload += "}";

    Serial.println("Sending to dashboard: " + jsonPayload);

    int httpResponseCode = http.POST(jsonPayload);

    if (httpResponseCode > 0) {
      Serial.print("Dashboard Response: ");
      Serial.println(httpResponseCode);
      String response = http.getString();
      Serial.println(response);
    } else {
      Serial.print("Error sending to dashboard: ");
      Serial.println(httpResponseCode);
    }

    http.end();
  } else {
    Serial.println("WiFi Disconnected - Cannot send to dashboard");
  }
}

// ================= SETUP =================
void setup() {
    pinMode(TRIG, OUTPUT);
    pinMode(ECHO, INPUT);
    pinMode(IR_SENSOR, INPUT);
    pinMode(BUZZER, OUTPUT);

    servo.attach(SERVO_PIN);
    servo.write(0);

    Wire.begin(21, 22);
    display.begin(SSD1306_SWITCHCAPVCC, 0x3C);
    display.clearDisplay();

    Serial.begin(115200);

    // Tampilkan status connecting di OLED
    display.setTextSize(1);
    display.setTextColor(WHITE);
    display.setCursor(0, 0);
    display.println("Connecting to WiFi...");
    display.display();

    WiFi.begin(ssid, password);
    secured_client.setInsecure();

    int wifiAttempts = 0;
    while (WiFi.status() != WL_CONNECTED && wifiAttempts < 30) {
        delay(500);
        Serial.print(".");
        wifiAttempts++;
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\nWiFi Connected");
        Serial.print("IP Address: ");
        Serial.println(WiFi.localIP());

        display.clearDisplay();
        display.setCursor(0, 0);
        display.println("WiFi Connected!");
        display.print("IP: ");
        display.println(WiFi.localIP());
        display.display();
        delay(2000);
    } else {
        Serial.println("\nWiFi Connection Failed");
        display.clearDisplay();
        display.setCursor(0, 0);
        display.println("WiFi Failed!");
        display.println("Running Offline");
        display.display();
        delay(2000);
    }
}

// ================= LOOP =================
void loop() {
    // ===== BACA IR (PRIORITAS TERTINGGI) =====
    full = (digitalRead(IR_SENSOR) == LOW);

    // ===== BACA ULTRASONIK =====
    digitalWrite(TRIG, LOW);
    delayMicroseconds(2);
    digitalWrite(TRIG, HIGH);
    delayMicroseconds(10);
    digitalWrite(TRIG, LOW);

    duration = pulseIn(ECHO, HIGH, 30000);
    distance = duration * 0.034 / 2;  // 0.034 cm/microsecond (kecepatan suara)

    // Variabel untuk tracking status saat ini
    int currentServoPos = servo.read();
    bool currentBuzzer = full;

    // ================= MODE FULL =================
    if (full) {
        servo.write(0);
        digitalWrite(BUZZER, HIGH);
        currentServoPos = 0;
        currentBuzzer = true;
        drawEyesWithFullText();

        if (!notifTerkirim) {
            bot.sendMessage(CHAT_ID, "⚠️ PERINGATAN!\nTempat sampah TERISI PENUH 🚮", "");
            notifTerkirim = true;
        }

        // Kirim data ke dashboard
        if (millis() - lastDashboardUpdate >= DASHBOARD_INTERVAL) {
            sendToDashboard(distance, full, currentServoPos, currentBuzzer);
            lastDashboardUpdate = millis();
        }

        delay(200);
        return;
    }

    // ================= MODE NORMAL =================
    notifTerkirim = false;
    digitalWrite(BUZZER, LOW);
    currentBuzzer = false;

    // ===== LOGIKA ULTRASONIK + DELAY 2 DETIK =====
    if (distance > 0 && distance < 20) {
        servo.write(90);
        currentServoPos = 90;
        waitingToClose = false;
    } else {
        if (!waitingToClose) {
            ultrasonicTimer = millis();
            waitingToClose = true;
        }

        if (millis() - ultrasonicTimer >= 2000) {
            servo.write(0);
            currentServoPos = 0;
            waitingToClose = false;
        }
    }

    // ================= OLED NORMAL =================
    display.clearDisplay();
    display.setTextSize(1);
    display.setTextColor(WHITE);
    display.setCursor(0, 0);
    display.print("Jarak: ");
    display.print(distance);
    display.println(" cm");

    if (distance < 20) {
        display.println("STATUS: OPEN");
    } else {
        display.println("STATUS: READY");
    }

    // Tampilkan info WiFi dan Dashboard
    display.setCursor(0, 40);
    if (WiFi.status() == WL_CONNECTED) {
      display.println("WiFi: Connected");
      display.println("Dashboard: Active");
    } else {
      display.println("WiFi: Disconnected");
      display.println("Dashboard: Offline");
    }

    display.display();

    // ================= KIRIM KE DASHBOARD =================
    if (millis() - lastDashboardUpdate >= DASHBOARD_INTERVAL) {
        sendToDashboard(distance, full, currentServoPos, currentBuzzer);
        lastDashboardUpdate = millis();
    }

    delay(200);
}
