<?php
// admin/admin_chat.php
// Admin chat management page
include 'includes/header.php';
?>
<div class="container mt-4">
    <h2>Chat Management</h2>
    <div id="chat-list-section">
        <h4>All Chats</h4>
        <div id="chat-list"></div>
    </div>
    <div id="chat-detail-section" style="display:none;">
        <button class="btn btn-secondary mb-2" onclick="showChatList()">&larr; Back to Chats</button>
        <h4>Chat with <span id="chat-with"></span></h4>
        <div id="chat-messages" style="height:300px; overflow-y:auto; border:1px solid #ccc; padding:10px; background:#fafafa;"></div>
        <div class="input-group mt-2">
            <input type="text" id="admin-message-input" class="form-control" placeholder="Type a message...">
            <button class="btn btn-primary" onclick="sendAdminMessage()">Send</button>
        </div>
        <div class="mt-2">
            <label for="assign-worker">Assign/Reassign Worker:</label>
            <select id="assign-worker" class="form-select" style="width:auto; display:inline-block;"></select>
            <button class="btn btn-sm btn-success" onclick="assignWorker()">Assign</button>
        </div>
    </div>
</div>
<script src="../assets/js/jquery.min.js"></script>
<script>
let currentChatId = null;
let adminId = <?php echo isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 1; ?>;

function loadChats() {
    $.get('../api/chat/list.php', {admin_id: adminId}, function(res) {
        if (res.success) {
            let html = '<ul class="list-group">';
            res.chats.forEach(function(chat) {
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Chat #${chat.chat_id} (${chat.status})</span>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewChat(${chat.chat_id})">View</button>
                </li>`;
            });
            html += '</ul>';
            $('#chat-list').html(html);
        } else {
            $('#chat-list').html('<div class="alert alert-danger">Failed to load chats</div>');
        }
    }, 'json');
}

function viewChat(chatId) {
    currentChatId = chatId;
    $('#chat-list-section').hide();
    $('#chat-detail-section').show();
    loadChatMessages();
    loadWorkerList();
}

function showChatList() {
    $('#chat-detail-section').hide();
    $('#chat-list-section').show();
    currentChatId = null;
}

function loadChatMessages() {
    if (!currentChatId) return;
    $.get('../api/chat/messages.php', {chat_id: currentChatId}, function(res) {
        if (res.success) {
            let html = '';
            res.messages.forEach(function(msg) {
                let who = msg.sender_type === 'admin' ? 'You' : msg.sender_type;
                html += `<div><b>${who}:</b> ${msg.message} <span class="text-muted small">${msg.created_at}</span></div>`;
            });
            $('#chat-messages').html(html);
            $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
        } else {
            $('#chat-messages').html('<div class="alert alert-danger">Failed to load messages</div>');
        }
    }, 'json');
}

function sendAdminMessage() {
    let msg = $('#admin-message-input').val();
    if (!msg.trim() || !currentChatId) return;
    $.ajax({
        url: '../api/chat/send_message.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            chat_id: currentChatId,
            sender_type: 'admin',
            sender_id: adminId,
            message: msg
        }),
        success: function(res) {
            if (res.success) {
                $('#admin-message-input').val('');
                loadChatMessages();
            }
        }
    });
}

function loadWorkerList() {
    // Fetch all workers for assignment
    $.get('../api/chat/get_technicians.php', function(res) {
        let html = '<option value="">Select worker</option>';
        if (res.success && Array.isArray(res.technicians)) {
            res.technicians.forEach(function(worker) {
                html += `<option value="${worker.id}">${worker.full_name}</option>`;
            });
        } else {
            html += '<option value="">No workers found</option>';
        }
        $('#assign-worker').html(html);
    }, 'json');
}

function assignWorker() {
    let workerId = $('#assign-worker').val();
    if (!workerId || !currentChatId) return;
    $.ajax({
        url: '../api/chat/assign_worker.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            chat_id: currentChatId,
            worker_id: workerId,
            assigned_by: adminId
        }),
        success: function(res) {
            if (res.success) {
                alert('Worker assigned!');
                loadChats();
            }
        }
    });
}

// Poll for new messages every 5 seconds when viewing a chat
timer = null;
$('#chat-detail-section').on('show', function() {
    timer = setInterval(loadChatMessages, 5000);
}).on('hide', function() {
    clearInterval(timer);
});

$(document).ready(function() {
    loadChats();
});
</script>
<?php include 'includes/footer.php'; ?> 