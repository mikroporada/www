class ChatService {
    constructor() {
        this.baseUrl = '/api/llm';
        this.sessionId = this.generateSessionId();
        this.setupEventListeners();
    }

    generateSessionId() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    setupEventListeners() {
        const chatInput = document.getElementById('chat-input');
        const sendButton = document.getElementById('send-button');
        const chatContainer = document.getElementById('chat-container');
        const htmlEditor = document.getElementById('html-editor');
        const generatePdfButton = document.getElementById('generate-pdf');

        sendButton.addEventListener('click', () => this.sendMessage(chatInput.value));
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage(chatInput.value);
        });

        generatePdfButton.addEventListener('click', () => this.generatePDF());
    }

    async sendMessage(message) {
        if (!message.trim()) return;

        const chatContainer = document.getElementById('chat-container');
        const userMessage = this.createMessageElement('user', message);
        chatContainer.appendChild(userMessage);

        try {
            const response = await fetch(`${this.baseUrl}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    session_id: this.sessionId
                })
            });

            const data = await response.json();
            const botMessage = this.createMessageElement('bot', data.response);
            chatContainer.appendChild(botMessage);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            // Update HTML editor with response
            const htmlEditor = document.getElementById('html-editor');
            htmlEditor.value += data.response;
        } catch (error) {
            console.error('Error:', error);
            const errorMessage = this.createMessageElement('error', 'Error occurred while processing your request');
            chatContainer.appendChild(errorMessage);
        }
    }

    createMessageElement(type, content) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = content;
        return messageDiv;
    }

    async generatePDF() {
        const htmlEditor = document.getElementById('html-editor');
        const chatContainer = document.getElementById('chat-container');

        try {
            const response = await fetch(`${this.baseUrl}/generate-pdf`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    html_content: htmlEditor.value,
                    session_id: this.sessionId
                })
            });

            const data = await response.json();
            const message = this.createMessageElement('info', 'PDF generated successfully!');
            chatContainer.appendChild(message);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        } catch (error) {
            console.error('Error:', error);
            const errorMessage = this.createMessageElement('error', 'Error occurred while generating PDF');
            chatContainer.appendChild(errorMessage);
        }
    }
}

// Initialize chat when page loads
document.addEventListener('DOMContentLoaded', () => {
    new ChatService();
});
