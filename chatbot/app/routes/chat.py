import os
from fastapi import APIRouter
from pydantic import BaseModel
import google.generativeai as genai
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# Configure Gemini API
genai.configure(api_key=os.getenv("GEMINI_API_KEY"))
model = genai.GenerativeModel('gemini-1.5-flash')

router = APIRouter(prefix="/chat", tags=["Chat"])

class ChatRequest(BaseModel):
    message: str

@router.post("/")
def chat(data: ChatRequest):
    system_prompt = (
        "You are a helpful and polite customer support agent for Tea Tik kok Shop. "
        "Keep your answers brief, professional, and directly address the user's question. "
        "Context: We offer free standard shipping (3-5 days), a 30-day return policy, "
        "accept all major credit cards and PayPal, and our support email is support@teatikkok.com. "
        "We do not have physical store locations. If the user asks a question unrelated to "
        "ecommerce or our shop, politely steer the conversation back to our products."
    )
    
    try:
        # Pass the system prompt and the user's message to Gemini
        response = model.generate_content(f"System: {system_prompt}\n\nUser: {data.message}")
        reply = response.text
    except Exception as e:
        print(f"Error calling Gemini API: {e}")
        reply = "I'm having trouble connecting to my AI brain right now. Please try again later or email support@teatikkok.com."

    return {"reply": reply}
