/*
 * Smart Trash Bin - ESP32 (SIMPLIFIED & OPTIMIZED)
 * Versi Ringan untuk Performa Maksimal
 */

#include <WiFi.h>
#include <HTTPClient.h>

// ================= PIN =================
#define TRIG 12
#define ECHO 27
#define IR_SENSOR 26
#define BUZZER 25
#define SERVO_PIN 23

// ================= WIFI =================
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const char* serverUrl = "http://192.168.100.128:8000/api/sensor/data";

// ================= SERVO (Tanpa Library - Lebih Ringan) =================
// Gunakan PWM langsung
const int servoChannel = 0;
const int servoFreq = 50;
const int servoResolution = 16;

// ================= VARIABEL =================
int distance = 0;
bool irFull = false;
int servoPos = 0;  // 0=tutup, 90=buka
unsigned long closeTimer = 0;
bool waitClose = false;

// Interval kirim data
unsigned long lastSend = 0;
const int SEND_INTERVAL = 1000;  // 1 detik

// ================= FUNGSI SERVO RINGAN =================
void servoWrite(int angle) {
    // Konversi angle ke duty cycle (0° = 1ms, 90° = 1.5ms, 180° = 2ms)
    int dutyCycle = map(angle, 0, 180, 1638, 8192); // untuk 50Hz PWM
    ledcWrite(servoChannel, dutyCycle);
}

// ================= SETUP =================
void setup() {
    Serial.begin(115200);

    // Pin modes
    pinMode(TRIG, OUTPUT);
    pinMode(ECHO, INPUT);
    pinMode(IR_SENSOR, INPUT);
    pinMode(BUZZER, OUTPUT);

    // Setup Servo PWM (lebih ringan dari library Servo)
    ledcSetup(servoChannel, servoFreq, servoResolution);
    ledcAttachPin(SERVO_PIN, servoChannel);
    servoWrite(0); // Tutup servo

    // Connect WiFi
    Serial.println("Connecting WiFi...");
    WiFi.begin(ssid, password);

    int attempts = 0;
    while (WiFi.status() != WL_CONNECTED && attempts < 20) {
        delay(500);
        Serial.print(".");
        attempts++;
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\nWiFi Connected!");
        Serial.println(WiFi.localIP());
    } else {
        Serial.println("\nWiFi Failed!");
    }
}

// ================= LOOP RINGAN =================
void loop() {
    unsigned long now = millis();

    // 1. BACA IR SENSOR (Prioritas tertinggi)
    irFull = (digitalRead(IR_SENSOR) == LOW);

    // 2. BACA ULTRASONIC
    digitalWrite(TRIG, LOW);
    delayMicroseconds(2);
    digitalWrite(TRIG, HIGH);
    delayMicroseconds(10);
    digitalWrite(TRIG, LOW);

    long duration = pulseIn(ECHO, HIGH, 25000); // Timeout 25ms (lebih cepat)
    distance = duration * 0.034 / 2;

    // Filter invalid
    if (distance == 0 || distance > 400) distance = 30;

    // 3. LOGIKA SEDERHANA
    if (irFull) {
        // PENUH: Tutup servo + buzzer ON
        if (servoPos != 0) {
            servoWrite(0);
            servoPos = 0;
        }
        digitalWrite(BUZZER, HIGH);
        waitClose = false;

    } else {
        // NORMAL: Logika servo + buzzer OFF
        digitalWrite(BUZZER, LOW);

        if (distance < 20) {
            // Objek dekat: Buka
            if (servoPos != 90) {
                servoWrite(90);
                servoPos = 90;
            }
            waitClose = false;
            closeTimer = 0;

        } else {
            // Tidak ada objek
            if (servoPos == 90) {
                if (!waitClose) {
                    closeTimer = now;
                    waitClose = true;
                } else if (now - closeTimer >= 2000) {
                    // 2 detik lewat: Tutup
                    servoWrite(0);
                    servoPos = 0;
                    waitClose = false;
                }
            }
        }
    }

    // 4. KIRIM DATA KE SERVER (setiap 1 detik)
    if (now - lastSend >= SEND_INTERVAL) {
        sendData();
        lastSend = now;
    }

    // Delay minimal untuk stabilitas sensor
    delay(30);  // Lebih cepat dari 50ms
}

// ================= KIRIM DATA =================
void sendData() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(1500); // Timeout pendek

    // JSON simple
    String json = "{";
    json += "\"distance\":" + String(distance) + ",";
    json += "\"ir_triggered\":" + String(irFull ? "true" : "false") + ",";
    json += "\"servo_position\":" + String(servoPos) + ",";
    json += "\"buzzer_active\":" + String(irFull ? "true" : "false");
    json += "}";

    int code = http.POST(json);

    if (code > 0) {
        Serial.println("Sent: " + String(code));
    } else {
        Serial.println("Error: " + String(code));
    }

    http.end();
}
