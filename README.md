# Smart Trash IoT Dashboard

Web-based monitoring dashboard for Smart Trash Bin system using ESP32 microcontroller. This application provides real-time monitoring, data visualization, and alert management for IoT-enabled smart waste management.

## Features

- **Real-time Monitoring**: Live sensor data display with auto-refresh every 2 seconds
- **Authentication System**: Secure login/register with Laravel Breeze
- **Interactive Dashboard**:
  - Overview with statistics and charts
  - Live sensor readings (Ultrasonic, IR, Servo, Buzzer)
  - System logs tracking
  - Alert notifications with Telegram integration
  - Device information and specifications
- **Responsive Sidebar**: Collapsible navigation with icon-only and expanded modes
- **Data Visualization**: Chart.js integration for historical data
- **API Endpoints**: RESTful API for ESP32 communication

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Database**: SQLite
- **Charts**: Chart.js
- **Authentication**: Laravel Breeze
- **Build Tool**: Vite

## Hardware Components

- ESP32 DevKit V1 (WiFi + Bluetooth)
- HC-SR04 Ultrasonic Sensor (2-400cm range)
- Infrared Obstacle Sensor
- SG90 Micro Servo Motor
- Buzzer
- OLED Display (I2C)

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- Git

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/Byassslaaaa/iot-dashboard.git
   cd iot-dashboard
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   - Open browser: `http://localhost:8000`
   - Default login credentials (after seeding):
     - Email: `admin@smarttrash.com`
     - Password: `password`

## API Endpoints

### ESP32 Integration

**POST** `/api/sensor/data`
- Receive sensor data from ESP32
- Required fields:
  ```json
  {
    "distance": 15,
    "ir_triggered": false,
    "servo_position": 0,
    "buzzer_active": false
  }
  ```

**GET** `/api/sensor/status`
- Get current trash bin status and latest reading

**GET** `/api/sensor/readings`
- Get historical data for charts
- Optional parameter: `?days=14`

## ESP32 Configuration

Update your ESP32 sketch with:
```cpp
const char* serverUrl = "http://your-domain.com/api/sensor/data";
// Send POST request with sensor data every few seconds
```

## Pin Configuration

| Component | ESP32 Pin |
|-----------|-----------|
| Ultrasonic TRIG | GPIO 12 |
| Ultrasonic ECHO | GPIO 27 |
| IR Sensor | GPIO 26 |
| Buzzer | GPIO 25 |
| Servo Motor | GPIO 23 |
| OLED SDA | GPIO 21 |
| OLED SCL | GPIO 22 |

## Project Structure

```
iot-dashboard/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/SensorController.php
│   │   └── DashboardController.php
│   └── Models/
│       ├── TrashBin.php
│       ├── SensorReading.php
│       ├── Alert.php
│       └── SystemLog.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── dashboard/
│   │   ├── components/
│   │   └── layouts/
│   ├── js/
│   └── css/
├── routes/
│   ├── web.php
│   └── api.php
└── public/
    ├── logo.png
    └── about.png
```

## Database Schema

- **trash_bins**: Main trash bin information
- **sensor_readings**: All sensor data readings
- **lid_events**: Track lid open/close events
- **alerts**: System alerts and notifications
- **system_logs**: Activity logs
- **daily_statistics**: Aggregated daily data

## Development

### Run in development mode
```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite dev server (for hot reload)
npm run dev
```

### Build for production
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Features Breakdown

### 1. Overview Dashboard
- Real-time capacity percentage
- Status indicators (Empty/Normal/Full)
- Daily statistics
- Usage trends chart

### 2. Live Monitoring
- Current sensor readings
- Connection status indicator
- Recent readings table
- Auto-refresh functionality

### 3. System Logs
- Detailed activity tracking
- Log level filtering
- Searchable entries

### 4. Alerts Management
- Real-time notifications
- Mark as read/resolved
- Telegram integration ready

### 5. Settings
- Device configuration
- Notification preferences
- Threshold settings

### 6. About Device
- Hardware specifications
- Pin configuration
- Feature list

## Contributing

Feel free to submit issues or pull requests for improvements.

## License

This project is open-source software licensed under the MIT license.

## Contact

For questions or support, please open an issue on GitHub.

---

**Smart Trash IoT Dashboard** - Making waste management smarter with IoT technology.
