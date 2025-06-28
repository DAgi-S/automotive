# Chat System Integration Plan for Nati Automotive

## 1. Overview
A real-time chat system will be integrated to enable communication between users, admin, and workers. Features include:
- User <-> Admin chat
- Admin assigns worker for help/support
- Notifications for new messages (admin, user, worker)
- Chat management in admin, worker, and user panels
- Guest user chat support
- Email notifications for missed messages

---

## 2. Database Tables

### a. `tbl_chats`
- `chat_id` (PK, AUTO_INCREMENT)
- `user_id` (nullable, FK to tbl_user)
- `guest_id` (nullable, for guest sessions)
- `admin_id` (nullable, FK to tbl_admin)
- `worker_id` (nullable, FK to tbl_worker)
- `assigned_by` (nullable, FK to tbl_admin)
- `status` (open, closed, pending, escalated)
- `created_at`, `updated_at`

### b. `tbl_chat_messages`
- `message_id` (PK, AUTO_INCREMENT)
- `chat_id` (FK to tbl_chats)
- `sender_type` (user/admin/worker/guest)
- `sender_id` (nullable, FK to respective table)
- `message` (TEXT)
- `is_read` (boolean)
- `created_at`

### c. `tbl_guest_sessions`
- `guest_id` (PK, AUTO_INCREMENT)
- `session_token`
- `created_at`

---

## 3. Admin Panel Integration
- **Chat Management Page:**
  - List all chats (filter by status, assigned worker, user, guest)
  - View chat history, send messages
  - Assign/reassign worker to chat
  - Close/escalate chats
- **Notifications:**
  - Real-time notification bell for new/unread messages
  - Email notification for missed messages (optional)

---

## 4. Worker Panel Integration
- **Chat Assignment Page:**
  - List assigned chats
  - View/respond to messages
  - Mark chat as resolved/need admin
- **Notifications:**
  - Real-time and email notifications for new messages

---

## 5. User Panel Integration
- **Chat Page:**
  - List user's chats (open/closed)
  - Start new chat (support/help)
  - Real-time messaging with admin/worker
- **Notifications:**
  - Real-time notification for replies
  - Email notification for missed replies

---

## 6. Guest Chat
- **Guest Chat Widget/Page:**
  - Start chat as guest (session-based, optional email input)
  - Admin/worker can reply
  - Guest receives messages in-session, optional email for follow-up

---

## 7. API Endpoints
- `POST /api/chat/start` (user/guest)
- `POST /api/chat/send_message` (all roles)
- `GET /api/chat/messages?chat_id=...` (all roles)
- `POST /api/chat/assign_worker` (admin)
- `GET /api/chat/list` (admin/worker/user)
- `POST /api/chat/close` (admin/worker)
- `GET /api/chat/unread_count` (all roles)

---

## 8. Notifications & Email
- Integrate with existing notification system (in-app + bell)
- Send email for missed messages (if not read in X minutes)
- Admin/user/worker can configure notification preferences

---

## 9. UI/UX
- Use existing chat UI as base (see single-chat-screen.html, chat.js)
- Responsive, mobile-first design
- Show sender avatar, timestamps, read status
- Support file/image attachments (future phase)

---

## 10. Security & Privacy
- Only participants can access a chat
- Guest chats expire after inactivity
- All messages sanitized and stored securely

---

## 11. Migration & Rollout
- Add new tables via migration SQL
- Integrate APIs and UI in phases (admin, worker, user, guest)
- Test notification and email flows
- Document for support team

---

## 12. Next Steps
1. Review and approve DB schema
2. Implement DB migration
3. Build API endpoints
4. Integrate admin, worker, user, guest UIs
5. Test notifications and email
6. Go live and monitor 