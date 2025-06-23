import time
import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.chrome.service import Service as ChromeService
from selenium.webdriver.chrome.options import Options

class TestChatFunctionality:
    @pytest.fixture(autouse=True)
    def setup(self):
        # Setup Chrome WebDriver with options
        chrome_options = Options()
        chrome_options.add_argument('--headless')
        chrome_options.add_argument('--no-sandbox')
        chrome_options.add_argument('--disable-dev-shm-usage')
        
        # Use ChromeDriver manager to get the appropriate version
        self.driver = webdriver.Chrome(
            service=ChromeService(ChromeDriverManager().install()),
            options=chrome_options
        )
        self.driver.implicitly_wait(10)
        self.base_url = "https://porada.sapletta.pl"
        
        yield
        # Teardown
        self.driver.quit()

    def test_send_message(self):
        """Test sending a message in the chat"""
        self.driver.get(f"{self.base_url}/chat.html")
        
        # Find and interact with chat elements
        chat_input = self.driver.find_element(By.ID, "chat-input")
        send_button = self.driver.find_element(By.ID, "send-button")
        
        # Send a test message
        test_message = "Cześć, to jest test wiadomości"
        chat_input.send_keys(test_message)
        send_button.click()
        
        # Wait for response
        try:
            WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.CLASS_NAME, "message"))
            )
        except Exception as e:
            print(f"Error waiting for response: {str(e)}")
            raise
        
        # Verify message was sent and response received
        messages = self.driver.find_elements(By.CLASS_NAME, "message")
        assert len(messages) >= 1, "No messages found in chat"
        assert test_message in messages[-1].text or messages[-2].text, "Test message not found in chat"

    def test_generate_pdf_button(self):
        """Test the Generate PDF button functionality"""
        self.driver.get(f"{self.base_url}/chat.html")
        
        # First send a message to have some content
        chat_input = self.driver.find_element(By.ID, "chat-input")
        send_button = self.driver.find_element(By.ID, "send-button")
        chat_input.send_keys("Test message for PDF")
        send_button.click()
        
        # Wait for the message to be sent
        time.sleep(2)
        
        # Click the Generate PDF button
        pdf_button = self.driver.find_element(By.ID, "generate-pdf")
        pdf_button.click()
        
        # Wait for PDF generation to complete
        time.sleep(2)
        
        # Verify success message
        messages = self.driver.find_elements(By.CLASS_NAME, "message")
        assert any("PDF generated successfully" in msg.text for msg in messages), "PDF generation success message not found"

    def test_500_error(self):
        """Test that the result.php endpoint doesn't return 500 error"""
        self.driver.get(f"{self.base_url}/result.php")
        
        # Check for 500 error
        assert "500" not in self.driver.page_source, "500 error found on result.php"
