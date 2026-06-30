from fastapi import FastAPI, APIRouter, HTTPException
import os
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from google import genai
from google.genai import types
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

router = APIRouter()

client = genai.Client(api_key=os.getenv("GOOGLE_API_KEY"))

shop_context = """
អ្នកគឺជាជំនួយការផ្នែកលក់ដ៏គួរសមម្នាក់របស់ហាង "Tea Tik Kok Shop"
នេះជាព័ត៌មានផ្លូវការរបស់ហាងសម្រាប់ឆ្លើយតប៖
- ម៉ោងបើកទ្វារ៖ ច័ន្ទ ដល់ អាទិត្យ (ម៉ោង 8:00 ព្រឹក ដល់ 8:00 យប់)។
- ទីតាំងហាង៖ ជិតសាកលវិទ្យាល័យភូមិន្ទភ្នំពេញ (RUPP) មហាវិថីសហព័ន្ធរុស្ស៊ី ភ្នំពេញ។
- សេវាដឹកជញ្ជូន៖ ដឹកជញ្ជូនឥតគិតថ្លៃក្នុងភ្នំពេញ សម្រាប់ការទិញចាប់ពី $50 ឡើងទៅ។ ខេត្តក្រៅសេវាដឹក $2។
- ទំនាក់ទំនង៖ លេខទូរស័ព្ទ 012-345-678 ឬ Telegram @cambodiatechshop។

ប្រសិនបើអតិថិជនសួរក្រៅពីព័ត៌មាននេះ ឬសួររកទំនិញដែលគ្មាន ចូលប្រាប់ពួកគេឲ្យទាក់ទងមកកាន់ក្រុមការងារតាម Telegram ឬទូរស័ព្ទ។

"""
chat_session = client.chats.create(
    model="gemini-2.5-flash",
    config=types.GenerateContentConfig(
        system_instruction=shop_context,
        temperature=0.7 
    )
)


@router.get("/")
def health_check():
    return {"message": "ok"}

# 4. Define the exact data structure we expect from the frontend
class ChatRequest(BaseModel):
    message: str

# 5. The API Endpoint (Notice the async/await syntax)
@router.post("/api/chat")
async def chat_endpoint(request: ChatRequest):
    try:
        response = chat_session.send_message(request.message)
        # FastAPI automatically converts standard Python dictionaries to JSON
        return {"response": response.text}
    except Exception as e:
        # Handle errors gracefully
        raise HTTPException(status_code=500, detail=str(e))