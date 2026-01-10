document.addEventListener('DOMContentLoaded', function () {
  // Biến cho Lời chào (Greeting)
  const toggleButton = document.getElementById('chatbot-toggle-button');
  const greetingBox = document.getElementById('chatbot-greeting');
  const greetingText = greetingBox.querySelector('.greeting-text');
  const greetingClose = greetingBox.querySelector('.greeting-close');

  // Biến cho Khung chat chính (Window)
  const chatWindow = document.getElementById('chatbot-window');
  const closeButton = chatWindow.querySelector('.chatbot-close');
  const chatBody = document.getElementById('chatbot-message-body');
  const chatInput = document.getElementById('chatbot-input');
  const sendButton = document.getElementById('chatbot-send-button');

  // Toàn màn hình chat
  const fullscreenButton = chatWindow.querySelector('.chatbot-toggle-fullscreen');
  const fullscreenIcon = fullscreenButton.querySelector('i');

  // --- LOGIC CHO LỜI CHÀO (GREETING) ---

  const greetings = [{
    text: "Xin chào! Mình là trợ lý",
    duration: 4000
  },
  {
    text: "Bạn cần hỗ trợ gì?",
    duration: 5000
  }
  ];
  let currentGreetingIndex = 0;
  let greetingTimeoutId = null;
  // Cờ theo dõi nếu người dùng đã đóng
  let greetingsDismissed = false;

  // Hàm ẩn thông báo (dùng chung)
  function hideGreeting() {
    if (greetingTimeoutId) {
      clearTimeout(greetingTimeoutId);
      greetingTimeoutId = null;
    }
    greetingBox.classList.remove('visible');
    setTimeout(() => {
      // Chỉ ẩn display nếu người dùng thực sự dismiss
      // Nếu không, nó sẽ tự hiện lại ở showNextGreeting
      if (greetingsDismissed) {
        greetingBox.style.display = 'none';
      }
    }, 500);
  }

  // Hàm hiển thị thông báo tiếp theo
  function showNextGreeting() {
    if (greetingsDismissed) {
      // Đảm bảo ẩn nếu đang hiện
      hideGreeting();
      // Dừng hoàn toàn
      return;
    }

    // Qauy lại từ đầu nếu hết danh sách
    if (currentGreetingIndex >= greetings.length) {
      // Reset lại chỉ số
      currentGreetingIndex = 0;
    }

    const currentGreeting = greetings[currentGreetingIndex];
    greetingText.textContent = currentGreeting.text;
    greetingBox.style.display = 'flex';

    setTimeout(() => {
      greetingBox.classList.add('visible');
    }, 50);

    // Lên lịch cho hành động tiếp theo
    greetingTimeoutId = setTimeout(() => {
      // Bắt đầu fade out
      greetingBox.classList.remove('visible');
      setTimeout(() => { // Đợi fade out xong
        // Không ẩn display ở đây nữa, để showNextGreeting tự quyết định
        // Tăng chỉ số (sẽ được reset ở lần gọi sau nếu cần)
        currentGreetingIndex++;
        // Gọi lại chính nó để hiện cái tiếp theo/quay lại đầu
        showNextGreeting();
      }, 500); // Thời gian fade out

    }, currentGreeting.duration); // Thời gian hiển thị
  }

  // Logic hiển thị tin nhắn trong khung chat
  function addMessage(message, senderClass, isHtml = false) {
    const div = document.createElement('div');
    // Dùng innerHTML để render các thẻ <br> nếu AI trả về
    div.className = senderClass;
    if (senderClass === 'ai-thinking') {
      div.id = 'ai-thinking-message';
    }
    // Xử lý hiển thị
    if (isHtml) {
        // Nếu là HTML (từ AI đã parse), gán trực tiếp
        div.innerHTML = message;
    } else {
        // Nếu là tin nhắn người dùng, replace xuống dòng và chống XSS cơ bản
        // (Lưu ý: Bạn có thể dùng textContent nếu không muốn user gửi HTML)
        div.innerHTML = message.replace(/\n/g, '<br>');
    }

    // Mở tất cả link trong tab mới
    const links = div.querySelectorAll('a');
    links.forEach(link => {
        link.setAttribute('target', '_blank'); // Mở tab mới
        link.setAttribute('rel', 'noopener noreferrer'); // Bảo mật (tránh lỗi security)
    });

    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
    return div;
  }

  function removeThinking() {
    const thinkingMsg = document.getElementById('ai-thinking-message');
    if (thinkingMsg) {
      thinkingMsg.remove();
    }
  }

  async function handleSend() {
    const message = chatInput.value.trim();
    if (message === '') return;

    // 1. Dừng và ẩn lời chào ngay khi người dùng bắt đầu chat
    greetingsDismissed = true;
    hideGreeting();

    // 2. Hiển thị tin nhắn User
    addMessage(message, 'user-message');
    chatInput.value = '';

    // 3. Hiển thị "Đang nghĩ..."
    addMessage("Đang suy nghĩ...", 'ai-thinking', false);

    try {
      // 4. Gửi đến MCP Server (Laravel)
      const res = await fetch(API_URL, { // API_URL từ biến global
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN // CSRF_TOKEN từ biến global
        },
        body: JSON.stringify({
          message: message
        })
      });

      if (!res.ok) {
        throw new Error('Lỗi máy chủ: ' + res.status);
      }
      const data = await res.json();

      // 5. Xóa "Đang nghĩ..."
      removeThinking();

      let aiContent = data.reply;

      // Kiểm tra xem thư viện marked đã được load chưa
      if (typeof marked !== 'undefined') {
          // Parse Markdown sang HTML (Biến [Link](url) thành <a href="url">Link</a>)
          aiContent = marked.parse(data.reply);
          
          // Hiển thị tin nhắn AI với cờ isHtml = true
          addMessage(aiContent, 'ai-message', true);
      } else {
          // Fallback nếu quên chưa chèn script marked.js
          console.warn('Chưa load thư viện marked.js!');
          addMessage(data.reply, 'ai-message', false);
      }

    } catch (error) {
      console.error(error);
      removeThinking();
      addMessage("Xin lỗi, tôi gặp lỗi khi xử lý yêu cầu của bạn. Vui lòng thử lại.", 'ai-message');
    }
  }

  // --- GÁN CÁC SỰ KIỆN ---

  // 1. Khi nhấn nút X trên thông báo chào mời
  greetingClose.addEventListener('click', function () {
    // Đánh dấu đã đóng
    greetingsDismissed = true;
    // Gọi hàm ẩn chung (sẽ ẩn hẳn display)
    hideGreeting();
  });

  // 2. Khi nhấn nút tròn chính để mở chat
  toggleButton.addEventListener('click', function () {
    // Đánh dấu đã đóng để dừng vòng lặp greeting (Dừng lời chào)
    greetingsDismissed = true;
    // Gọi hàm ẩn chung
    hideGreeting();

    // Mở/đóng khung chat chính
    const isHidden = chatWindow.style.display === 'none' || chatWindow.style.display === '';
    chatWindow.style.display = isHidden ? 'flex' : 'none';
  });

  // 3. Khi nhấn nút X trên khung chat chính
  closeButton.addEventListener('click', function () {
    // Ẩn khung chat
    chatWindow.style.display = 'none';

    // Xóa class fullscreen để CSS :has trong file ai-chatbot.csskhông còn tác dụng (GIF chatbot sẽ hiện lại)
    chatWindow.classList.remove('fullscreen');

    // Reset icon nút phóng to về trạng thái ban đầu (để lần sau mở lên icon đúng)
    fullscreenIcon.classList.remove('fa-compress');
    fullscreenIcon.classList.add('fa-expand');
    fullscreenButton.title = "Toàn màn hình";
  });

  //4. Sự kiện click nút full screen
  fullscreenButton.addEventListener('click', function () {
    // Bật/tắt class 'fullscreen' trên cửa sổ chat
    chatWindow.classList.toggle('fullscreen');

    // Kiểm tra xem có class 'fullscreen' hay không để đổi icon
    if (chatWindow.classList.contains('fullscreen')) {
      fullscreenIcon.classList.remove('fa-expand');
      fullscreenIcon.classList.add('fa-compress');
      fullscreenButton.title = "Thu nhỏ";
    } else {
      fullscreenIcon.classList.remove('fa-compress');
      fullscreenIcon.classList.add('fa-expand');
      fullscreenButton.title = "Toàn màn hình";
    }
  });

  // Sự kiện cho Gửi tin nhắn
  sendButton.addEventListener('click', handleSend);
  chatInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      handleSend();
    }
  });

  // --- Bắt đầu hiển thị thông báo chào mời sau 2 giây ---
  if (!greetingsDismissed) {
    setTimeout(showNextGreeting, 2000);
  }
});