import os
import time
import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.firefox import GeckoDriverManager
from selenium.webdriver.firefox.service import Service as FirefoxService
from selenium.webdriver.firefox.options import Options as FirefoxOptions

class TestChatFunctionality:
    @pytest.fixture(autouse=True)
    def setup(self):
        # Setup Firefox WebDriver with options
        firefox_options = FirefoxOptions()
        firefox_options.add_argument('--headless')
        
        # Use GeckoDriver manager to get the appropriate version
        self.driver = webdriver.Firefox(
            service=FirefoxService(GeckoDriverManager().install()),
            options=firefox_options
        )
        self.driver.implicitly_wait(10)
        # Use local test page
        self.base_url = f"file://{os.path.dirname(os.path.abspath(__file__))}"
        
        yield
        # Teardown
        self.driver.quit()

    def test_send_message(self):
        """Test sending a message in the chat"""
        url = f"{self.base_url}/test_chat_page.html"
        print(f"\nTesting URL: {url}")
        self.driver.get(url)
        
        # Print page title and current URL for debugging
        print(f"Page title: {self.driver.title}")
        print(f"Current URL: {self.driver.current_url}")
        
        # Wait for and find elements
        wait = WebDriverWait(self.driver, 10)
        chat_input = wait.until(EC.presence_of_element_located((By.ID, "chat-input")))
        send_button = wait.until(EC.element_to_be_clickable((By.ID, "send-button")))
        print("Found chat input and send button")
        
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
        url = f"{self.base_url}/test_chat_page.html"
        print(f"\nTesting URL: {url}")
        self.driver.get(url)
        
        # Wait for elements to be present
        wait = WebDriverWait(self.driver, 10)
        chat_input = wait.until(EC.presence_of_element_located((By.ID, "chat-input")))
        send_button = wait.until(EC.element_to_be_clickable((By.ID, "send-button")))
        
        # Send a test message
        test_message = "Test message for PDF"
        chat_input.send_keys(test_message)
        send_button.click()
        
        # Wait for the message to be sent and response to appear
        wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "message"), "test response"))
        
        # Click the Generate PDF button
        pdf_button = wait.until(EC.element_to_be_clickable((By.ID, "generate-pdf")))
        pdf_button.click()
        
        # Wait for PDF generation success message
        wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "message"), "PDF generated successfully"))
        
        # Verify success message
        messages = self.driver.find_elements(By.CLASS_NAME, "message")
        assert any("PDF generated successfully" in msg.text for msg in messages), "PDF generation success message not found"

    def test_page_loads_correctly(self):
        """Test that the test page loads correctly"""
        url = f"{self.base_url}/test_chat_page.html"
        print(f"\nTesting URL: {url}")
        self.driver.get(url)
        
        # Verify page title
        assert "Test Chat Page" in self.driver.title, "Page title is incorrect"
        
        # Verify main elements are present
        wait = WebDriverWait(self.driver, 10)
        wait.until(EC.presence_of_element_located((By.ID, "chat-container")))
        wait.until(EC.presence_of_element_located((By.ID, "chat-input")))
        wait.until(EC.presence_of_element_located((By.ID, "send-button")))
        wait.until(EC.presence_of_element_located((By.ID, "generate-pdf")))
        
        print("All expected elements found on the page")
