from fastapi import FastAPI, APIRouter, HTTPException
import os
import json
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from google import genai
from google.genai import types
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

router = APIRouter()

client = genai.Client(api_key=os.getenv("GOOGLE_API_KEY"))

import urllib.request

def get_dynamic_catalog():
    try:
        # Fetch directly from the Laravel Backend API (similar to Telegram Bot)
        backend_url = os.getenv("BACKEND_API_URL", "https://ecommerce-production-3bc1.up.railway.app/api")
        
        req = urllib.request.Request(f"{backend_url}/products")
        with urllib.request.urlopen(req, timeout=10) as response:
            data = json.loads(response.read().decode())
            
        # Laravel's pagination usually wraps items in a 'data' array
        products = data.get('data', []) if isinstance(data, dict) else data
        
        # Get the top 5 latest products just like in TelegramWebhookController
        products = products[:5]
            
        catalog_text = "📦 បញ្ជីទំនិញថ្មីៗទើបមកដល់ (New Arrivals):\n"
        
        for item in products:
            name = item.get('name', 'Unknown')
            price = item.get('price', 0)
            image = item.get('image_url', '')
            
            # Format image URL if it's stored locally on the Laravel backend
            if image and not str(image).startswith('http'):
                base_url = backend_url.replace('/api', '')
                image = f"{base_url}/storage/{image}"
            
            catalog_text += f"- {name} (តម្លៃ: ${price})\n"
            catalog_text += f"  រូបភាពទំនិញ: ![{name}]({image})\n\n"
            
        return catalog_text
    
    except Exception as e:
        print(f"Error fetching catalog: {e}")
        return "⚠️ កំពុងរៀបចំទំនិញថ្មីៗ..."

base_context = """
អ្នកគឺជាជំនួយការផ្នែកលក់ដ៏គួរសមម្នាក់របស់ហាង "Tea Tik Kok Shop"
នេះជាព័ត៌មានផ្លូវការរបស់ហាងសម្រាប់ឆ្លើយតប៖
- ម៉ោងបើកទ្វារ៖ ច័ន្ទ ដល់ អាទិត្យ (ម៉ោង 8:00 ព្រឹក ដល់ 8:00 យប់)។
- ទីតាំងហាង៖ ជិតសាកលវិទ្យាល័យភូមិន្ទភ្នំពេញ (RUPP) មហាវិថីសហព័ន្ធរុស្ស៊ី ភ្នំពេញ។
- សេវាដឹកជញ្ជូន៖ ដឹកជញ្ជូនឥតគិតថ្លៃក្នុងភ្នំពេញ សម្រាប់ការទិញចាប់ពី $50 ឡើងទៅ។ ខេត្តក្រៅសេវាដឹក $2។
- ទំនាក់ទំនង៖ លេខទូរស័ព្ទ 012-345-678 ឬ Telegram @rupp_shop_bot។

⚠️ ច្បាប់សំខាន់បំផុតពេលអតិថិជនសួររឿងទំនិញ៖
ប្រសិនបើអតិថិជនសួររក "ទំនិញថ្មីៗ" ឬសួររកទំនិញណាមួយ អ្នកត្រូវតែបង្ហាញឈ្មោះ តម្លៃ និង រូបភាព របស់វាជានិច្ចដោយប្រើទម្រង់ Markdown: ![ឈ្មោះទំនិញ](URL_រូបភាព)

ប្រសិនបើអតិថិជនសួរក្រៅពីព័ត៌មាននេះ ឬសួររកទំនិញដែលគ្មាន ចូលប្រាប់ពួកគេឲ្យទាក់ទងមកកាន់ក្រុមការងារតាម Telegram ឬទូរស័ព្ទ។
"""

full_system_instruction = base_context + "\n\n" + get_dynamic_catalog()

chat_session = client.chats.create(
    model="gemini-2.5-flash",
    config=types.GenerateContentConfig(
        system_instruction=full_system_instruction,
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