// var $messages = $('.messages-content'),
var $messages = $('.messages-content'),
    chatId = window.CHAT_ID || null,
    senderType = window.SENDER_TYPE || null,
    senderId = window.SENDER_ID || null;

$(document).ready(function () {
    // $messages.mCustomScrollbar(); // Removed custom scrollbar
    if (chatId) {
        loadMessages();
        setInterval(loadMessages, 3000); // Poll every 3s
    }
});

function updateScrollbar() {
    // $messages.mCustomScrollbar("update").mCustomScrollbar('scrollTo', 'bottom', {
    //     scrollInertia: 10,
    //     timeout: 0
    // });
    $messages.scrollTop($messages[0].scrollHeight); // Use standard scroll
}

function loadMessages() {
    if (!chatId) return;
    $.get('/api/chat/messages.php', { chat_id: chatId }, function(res) {
        if (res.success) {
            $messages.find('.mCSB_container').empty();
            res.messages.forEach(function(msg) {
                var who = (msg.sender_type === senderType && msg.sender_id == senderId) ? 'message-personal' : '';
                var html = `<div class="message ${who}">${msg.message}<span class="timestamp">${msg.created_at}</span></div>`;
                $messages.find('.mCSB_container').append(html);
            });
            updateScrollbar();
        }
    }, 'json');
}

$('.message-submit').click(function () {
    sendMessage();
});

$(window).on('keydown', function (e) {
    if (e.which == 13) {
        sendMessage();
        return false;
    }
});

function sendMessage() {
    var msg = $('.message-input').val();
    if ($.trim(msg) == '' || !chatId || !senderType || !senderId) {
        return false;
    }
    $.ajax({
        url: '/api/chat/send_message.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            chat_id: chatId,
            sender_type: senderType,
            sender_id: senderId,
            message: msg
        }),
        success: function(res) {
            if (res.success) {
                $('.message-input').val('');
                loadMessages();
            }
        }
    });
}

function setDate() {
    d = new Date();
    if (m != d.getMinutes()) {
        m = d.getMinutes();

        var hours = d.getHours();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert to 12-hour format

        var timestamp = hours + ':' + padZero(m) + ' ' + ampm;

        $('<div class="timestamp">' + timestamp + '</div>').appendTo($('.message:last'));
    }
}

// Helper function to pad single-digit minutes with a leading zero
function padZero(number) {
    return number < 10 ? '0' + number : number;
}

function getCurrentTime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var timeString = hours + ':' + minutes + ' ' + ampm;
    return timeString;
}

$('.button').click(function () {
    $('.menu .items span').toggleClass('active');
    $('.menu .button').toggleClass('active');
});
