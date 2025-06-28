# Notification System Documentation

## Overview

The notification system provides comprehensive multi-channel notification capabilities for the Automotive website, supporting:
- **Web App Notifications**: In-browser notifications with real-time updates
- **Email Notifications**: SMTP-based email delivery with templates
- **Telegram Bot Notifications**: Instant messaging via Telegram bot
- **SMS Notifications**: SMS delivery (framework ready for implementation)

## Database Tables

### notification_config
Stores configuration settings for all notification channels.

| Field | Type | Description |
|-------|------|-------------|
| id | int(11) | Primary key |
| config_key | varchar(100) | Configuration key (unique) |
| config_value | text | Configuration value |
| config_type | enum | Type: telegram, email, sms, push, general |
| is_active | tinyint(1) | Enable/disable configuration |
| description | text | Human-readable description |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

### tbl_notifications (Existing)
Stores individual notification records.

| Field | Type | Description |
|-------|------|-------------|
| id | int(11) | Primary key |
| recipient_id | int(11) | User ID of recipient |
| type | varchar(50) | Notification type (appointment, order, system) |
| message | text | Notification message |
| is_read | tinyint(1) | Read status (0=unread, 1=read) |
| created_at | timestamp | Creation timestamp |

## Configuration Keys

### Telegram Settings
- `telegram_bot_token`: Bot API token from @BotFather
- `telegram_bot_username`: Bot username (without @)
- `telegram_chat_id`: Default chat ID for notifications
- `telegram_enabled`: Enable/disable Telegram notifications (0/1)

### Email Settings
- `smtp_host`: SMTP server host (default: smtp.gmail.com)
- `smtp_port`: SMTP server port (default: 587)
- `smtp_username`: SMTP authentication username
- `smtp_password`: SMTP authentication password/app password
- `smtp_encryption`: Encryption type (tls/ssl)
- `email_from_name`: Display name for sent emails
- `email_from_address`: From email address
- `email_enabled`: Enable/disable email notifications (0/1)

## Core Components

### NotificationManager Class
**Location**: `includes/NotificationManager.php`

Main class handling all notification operations:
- Configuration loading from database
- Multi-channel message sending
- Template processing
- Error handling and logging

#### Key Methods:
- `sendNotification($data)`: Send notification via specified channels
- `sendTelegramMessage($chatId, $message, $templateKey, $variables)`: Send Telegram message
- `sendEmail($to, $subject, $message, $templateKey, $variables)`: Send email
- `createWebNotification($recipientId, $type, $message)`: Create web notification
- `markAsRead($notificationId, $userId)`: Mark notification as read
- `getUnreadNotifications($userId, $limit)`: Get unread notifications for user

### Notification Bell Component
**Location**: `includes/notification_bell.php`

Interactive notification bell for website header:
- Real-time unread count display
- Dropdown with recent notifications
- Auto-refresh every 30 seconds
- Mark as read functionality
- Responsive design with mobile support

### API Endpoints

#### Send Notification
**Endpoint**: `POST /api/notifications/send.php`

Send notifications via multiple channels.

**Request Body**:
```json
{
  "type": "appointment|order|system",
  "recipient_id": 1,
  "message": "Notification message",
  "methods": ["web", "email", "telegram"],
  "subject": "Email subject (optional)",
  "email": "recipient@example.com (for email)",
  "name": "Recipient Name (for templates)",
  "template_key": "template_name (optional)",
  "variables": {
    "custom_variable": "value"
  }
}
```

**Response**:
```json
{
  "success": true,
  "message": "Notification sent successfully",
  "results": {
    "web": {"success": true, "message": "Web notification created"},
    "email": {"success": true, "message": "Email sent"},
    "telegram": {"success": false, "message": "Bot token not configured"}
  }
}
```

#### Get Notifications
**Endpoint**: `GET /api/notifications/get.php`

Retrieve user notifications.

**Parameters**:
- `user_id`: User ID (required)
- `limit`: Number of notifications to return (default: 10)
- `unread_only`: Return only unread notifications (true/false)

**Response**:
```json
{
  "success": true,
  "notifications": [
    {
      "id": 1,
      "type": "appointment",
      "message": "Your appointment is confirmed",
      "is_read": 0,
      "created_at": "2025-01-21 10:30:00"
    }
  ],
  "counts": {
    "total": 10,
    "unread": 3
  }
}
```

#### Configuration Management
**Endpoint**: `GET/POST /api/notifications/config.php`

Manage notification configuration.

**GET Response**:
```json
{
  "success": true,
  "config": {
    "telegram": {
      "telegram_bot_token": {"value": "", "description": "Bot API Token"},
      "telegram_enabled": {"value": "0", "description": "Enable Telegram"}
    },
    "email": {
      "smtp_host": {"value": "smtp.gmail.com", "description": "SMTP Host"},
      "email_enabled": {"value": "0", "description": "Enable Email"}
    }
  }
}
```

**POST Request**:
```json
{
  "config": {
    "telegram_bot_token": "your_bot_token",
    "telegram_enabled": "1",
    "email_enabled": "1"
  }
}
```

#### Mark as Read
**Endpoint**: `POST /api/notifications/mark-read.php`

Mark single notification as read.

**Request**:
```json
{
  "notification_id": 1,
  "user_id": 1
}
```

#### Mark All as Read
**Endpoint**: `POST /api/notifications/mark-all-read.php`

Mark all user notifications as read.

**Request**:
```json
{
  "user_id": 1
}
```

## Setup Instructions

### 1. Database Setup
The notification_config table is automatically created. Default configuration is inserted with empty values for security.

### 2. Telegram Bot Setup
1. Create a bot via @BotFather on Telegram
2. Get the bot token
3. Add bot token to configuration via API or database
4. Set `telegram_enabled` to 1
5. Get chat ID for notifications (can use @userinfobot)

### 3. Email Setup
1. Configure SMTP settings (Gmail example):
   - Host: smtp.gmail.com
   - Port: 587
   - Encryption: TLS
   - Username: your-email@gmail.com
   - Password: app-specific password (not regular password)
2. Set `email_enabled` to 1

### 4. Web Integration
The notification bell is automatically included in the header for logged-in users. No additional setup required.

## Usage Examples

### Send Appointment Notification
```php
$notificationManager = new NotificationManager($pdo);

$result = $notificationManager->sendNotification([
    'type' => 'appointment',
    'recipient_id' => 123,
    'message' => 'Your appointment is confirmed for tomorrow at 10:00 AM',
    'methods' => ['web', 'email', 'telegram'],
    'email' => 'customer@example.com',
    'name' => 'John Doe',
    'template_key' => 'appointment_created_telegram',
    'variables' => [
        'customer_name' => 'John Doe',
        'appointment_date' => '2025-01-22',
        'appointment_time' => '10:00 AM',
        'service_name' => 'Oil Change',
        'vehicle_info' => '2020 Toyota Camry'
    ]
]);
```

### JavaScript Integration
```javascript
// Send notification via AJAX
fetch('api/notifications/send.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        type: 'order',
        recipient_id: userId,
        message: 'Your order has been processed',
        methods: ['web']
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Notification sent successfully');
    }
});
```

## Testing

Use the test page at `/test_notifications.php` to:
- Send test notifications
- View recent notifications
- Test configuration
- Verify all channels work correctly

## Security Considerations

1. **API Keys**: Store sensitive keys (bot tokens, SMTP passwords) securely
2. **Input Validation**: All API endpoints validate input data
3. **Authentication**: Notification APIs should be protected with proper authentication
4. **Rate Limiting**: Consider implementing rate limiting for notification sending
5. **Data Sanitization**: All user input is sanitized before storage/display

## Troubleshooting

### Common Issues:
1. **Telegram not working**: Check bot token and chat ID
2. **Email not sending**: Verify SMTP credentials and app passwords
3. **Notifications not appearing**: Check user session and database connectivity
4. **Badge not updating**: Verify JavaScript is enabled and API endpoints are accessible

### Debug Steps:
1. Check browser console for JavaScript errors
2. Verify database tables exist and have correct structure
3. Test API endpoints individually
4. Check server logs for PHP errors
5. Verify notification configuration values in database

## Future Enhancements

1. **Push Notifications**: Browser push notification support
2. **SMS Integration**: SMS gateway integration
3. **Notification Templates**: Rich HTML email templates
4. **Scheduling**: Delayed/scheduled notification sending
5. **Analytics**: Notification delivery analytics and reporting
6. **User Preferences**: Per-user notification preferences
7. **Bulk Notifications**: Mass notification sending capabilities