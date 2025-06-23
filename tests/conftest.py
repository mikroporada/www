"""
Configuration file for pytest.
"""
import pytest
from selenium import webdriver
from selenium.webdriver.chrome.service import Service as ChromeService
from webdriver_manager.chrome import ChromeDriverManager

@pytest.fixture
def browser():
    """Fixture to provide a browser instance for tests."""
    options = webdriver.ChromeOptions()
    options.add_argument("--headless")  # Run in headless mode for CI
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    
    driver = webdriver.Chrome(
        service=ChromeService(ChromeDriverManager().install()),
        options=options
    )
    
    yield driver
    driver.quit()
