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

### Configuration Keys

#### Telegram Settings
- `telegram_bot_token`: Bot API token from @BotFather
- `telegram_bot_username`: Bot username (without @)
- `telegram_chat_id`: Default chat ID for notifications
- `telegram_enabled`: Enable/disable Telegram notifications (0/1)

#### Email Settings
- `smtp_host`: SMTP server host (default: smtp.gmail.com)
- `smtp_port`: SMTP server port (default: 587)
- `smtp_username`: SMTP authentication username
- `smtp_password`: SMTP authentication password/app password
- `smtp_encryption`: Encryption type (tls/ssl)
- `email_from_name`: Display name for sent emails
- `email_from_address`: From email address
- `email_enabled`: Enable/disable email notifications (0/1)

## API Endpoints

### Send Notification
**Endpoint**: `POST /api/notifications/send.php`

**Request Body**:
```json
{
  "type": "appointment|order|system",
  "recipient_id": 1,
  "message": "Notification message",
  "methods": ["web", "email", "telegram"],
  "subject": "Email subject (optional)",
  "email": "recipient@example.com (for email)",
  "name": "Recipient Name (for templates)"
}
```

### Get Notifications
**Endpoint**: `GET /api/notifications/get.php`

**Parameters**:
- `user_id`: User ID (required)
- `limit`: Number of notifications to return (default: 10)
- `unread_only`: Return only unread notifications (true/false)

## Setup Instructions

### 1. Telegram Bot Setup
1. Create a bot via @BotFather on Telegram
2. Get the bot token
3. Add bot token to configuration
4. Set `telegram_enabled` to 1

### 2. Email Setup
1. Configure SMTP settings (Gmail example):
   - Host: smtp.gmail.com
   - Port: 587
   - Encryption: TLS
   - Username: your-email@gmail.com
   - Password: app-specific password
2. Set `email_enabled` to 1

## Testing

Use the test page at `/test_notifications.php` to test all notification channels.