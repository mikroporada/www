import requests
import pytest
import os
from dotenv import load_dotenv

load_dotenv()

BASE_URL = os.getenv("LLM_SERVICE_URL", "http://localhost:8000")

def test_health_check():
    response = requests.get(f"{BASE_URL}/health")
    assert response.status_code == 200
    assert response.json() == {"status": "healthy"}

def test_chat_endpoint():
    test_message = "Cześć! Jak mogę Ci dzisiaj pomóc?"
    
    response = requests.post(
        f"{BASE_URL}/chat",
        json={
            "message": test_message,
            "session_id": "test-session"
        }
    )
    
    assert response.status_code == 200
    assert "response" in response.json()

def test_pdf_generation():
    test_html = """
    <html>
        <head>
            <title>Test Document</title>
        </head>
        <body>
            <h1>Test PDF Generation</h1>
            <p>This is a test document.</p>
        </body>
    </html>
    """
    
    response = requests.post(
        f"{BASE_URL}/generate-pdf",
        json={
            "html_content": test_html,
            "session_id": "test-session"
        }
    )
    
    assert response.status_code == 200
    assert "pdf_path" in response.json()

if __name__ == "__main__":
    pytest.main([__file__])
