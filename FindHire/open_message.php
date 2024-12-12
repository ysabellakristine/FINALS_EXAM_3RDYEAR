<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Real-Time Chat</title>
  <style>
    /* Basic styling for the chat window */
    body {
      font-family: Arial, sans-serif;
      background-color: rgb(0, 176, 252);
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
    }
    
    .chat-window {
      width: 80%;
      height: 400px;
      overflow-y: scroll;
      margin: 20px auto;
      border: 1px solid #ddd;
      padding: 10px;
      background-color: #f9f9f9;
    }

    .message {
      margin: 10px;
      padding: 5px;
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
    }

    .admin-message {
      background-color: #f1f1f1;
      text-align: right;
    }

    .user-message {
      background-color: #e1f5fe;
      text-align: left;
    }

    .chat-head {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .admin-message .chat-head {
      background-color: yellow;
    }

    .user-message .chat-head {
      background-color: blue;
    }

    .username {
      font-weight: bold;
    }

    input {
      width: 80%;
      padding: 10px;
      margin-top: 10px;
    }

    button {
      padding: 10px;
      margin-top: 10px;
    }
  </style>
</head>
<body>

  <h1>Real-Time Chat</h1>

  <div class="chat-window" id="chat-window"></div> <!-- Chat window where messages will appear -->

  <input type="text" id="message-input" placeholder="Type a message..." />
  <button onclick="sendMessage()">Send</button>

  <script>
    // Connect to the WebSocket server
    const socket = new WebSocket('ws://localhost:8080');

    // Variables to store user details
    let username = '';
    let role = '';

    // WebSocket event: when connection is established
    socket.onopen = function() {
      console.log('WebSocket connection established');
    };

    // WebSocket event: when message is received from the server
    socket.onmessage = function(event) {
      const message = JSON.parse(event.data); // Parse the incoming message
      const chatWindow = document.getElementById('chat-window');
      
      // Create a new div for each message
      const newMessage = document.createElement('div');
      newMessage.classList.add('message');

      // Add different styles based on the role
      if (message.role === 'admin') {
        newMessage.classList.add('admin-message');
      } else {
        newMessage.classList.add('user-message');
      }

      // Create the chat head (colored circle)
      const chatHead = document.createElement('div');
      chatHead.classList.add('chat-head');
      if (message.role === 'admin') {
        chatHead.style.backgroundColor = 'yellow'; // Admin's color
      } else {
        chatHead.style.backgroundColor = 'blue'; // User's color
      }

      // Username and message text
      const usernameSpan = document.createElement('span');
      usernameSpan.classList.add('username');
      usernameSpan.textContent = `${message.from}:`; // Display the sender's username

      // Add message content
      newMessage.appendChild(chatHead);
      newMessage.appendChild(usernameSpan);
      newMessage.appendChild(document.createTextNode(message.text));

      // Append the message to the chat window
      chatWindow.appendChild(newMessage);
      chatWindow.scrollTop = chatWindow.scrollHeight; // Auto-scroll to bottom
    };

    // WebSocket event: when there's an error
    socket.onerror = function(error) {
      console.error('WebSocket error:', error);
    };

    // Send a message to the WebSocket server
    function sendMessage() {
      const messageInput = document.getElementById('message-input');
      const message = messageInput.value;
      
      if (message.trim() !== '') { // Only send non-empty messages
        const messageData = {
          from: username,  // Sender's username
          text: message,   // Message text
          role: role       // User's role (admin/user)
        };

        console.log('Sending message:', messageData);
        socket.send(JSON.stringify(messageData)); // Send the message data to the server
        messageInput.value = ''; // Clear input field
      } else {
        console.log('Message input is empty');
      }
    }

    // Set username and role when the user connects (just for the sake of this example)
    window.onload = function() {
      username = `User${Math.floor(Math.random() * 100)}`; // Random username for the example
      role = Math.random() > 0.5 ? 'admin' : 'user';  // Random role for example
      console.log(`Username: ${username}, Role: ${role}`);
    };

  </script>

</body>
</html>
