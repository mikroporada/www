from fastapi import FastAPI, HTTPException, UploadFile, File
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import subprocess
import json
import os
from dotenv import load_dotenv
from weasyprint import HTML

load_dotenv()

app = FastAPI(title="LLM Service")

# Configure CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

class ChatRequest(BaseModel):
    message: str
    session_id: str

class PDFRequest(BaseModel):
    html_content: str

@app.post("/chat")
async def chat_endpoint(request: ChatRequest):
    try:
        # Call Ollama with custom model
        process = subprocess.Popen(
            ["ollama", "run", os.getenv("OLLAMA_MODEL", "llama2"), "--stream"],
            stdin=subprocess.PIPE,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True
        )
        
        # Send user message
        process.stdin.write(request.message + "\n")
        process.stdin.close()
        
        # Read response
        response = ""
        while True:
            line = process.stdout.readline()
            if not line:
                break
            response += line
        
        return {"response": response.strip()}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/generate-pdf")
async def generate_pdf(request: PDFRequest):
    try:
        # Generate PDF from HTML
        pdf_path = f"/app/tmp/{request.session_id}.pdf"
        os.makedirs("/app/tmp", exist_ok=True)
        
        HTML(string=request.html_content).write_pdf(pdf_path)
        
        return {"pdf_path": pdf_path}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/health")
async def health_check():
    return {"status": "healthy"}
