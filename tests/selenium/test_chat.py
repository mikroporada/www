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
        self.base_url = "https://porada.sapletta.pl"
        
        yield
        # Teardown
        self.driver.quit()

    def test_send_message(self):
        """Test sending a message in the chat"""
        url = f"{self.base_url}/chat.html"
        print(f"\nTesting URL: {url}")
        self.driver.get(url)
        
        # Print page title and current URL for debugging
        print(f"Page title: {self.driver.title}")
        print(f"Current URL: {self.driver.current_url}")
        
        # Print page source for debugging
        print("\nPage source (first 1000 chars):")
        print(self.driver.page_source[:1000])
        
        # Try to find elements with a wait
        wait = WebDriverWait(self.driver, 10)
        try:
            chat_input = wait.until(EC.presence_of_element_located((By.ID, "chat-input")))
            send_button = wait.until(EC.element_to_be_clickable((By.ID, "send-button")))
            print("Found chat input and send button")
        except Exception as e:
            print(f"Error finding elements: {str(e)}")
            # Try to find any input elements
            print("\nAvailable input elements:")
            inputs = self.driver.find_elements(By.TAG_NAME, "input")
            for i, input_elem in enumerate(inputs):
                print(f"Input {i+1}: id='{input_elem.get_attribute('id')}', name='{input_elem.get_attribute('name')}', type='{input_elem.get_attribute('type')}'")
            raise
        
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
        url = f"{self.base_url}/result.php"
        print(f"\nTesting URL: {url}")
        self.driver.get(url)
        
        # Print page title and current URL for debugging
        print(f"Page title: {self.driver.title}")
        print(f"Current URL: {self.driver.current_url}")
        
        # Check for 500 error
        assert "500" not in self.driver.page_source, "500 error found on result.php"
        print("No 500 error detected on result.php")
