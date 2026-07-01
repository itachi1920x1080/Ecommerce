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
# អត្តសញ្ញាណ AI
អ្នកគឺជាជំនួយការផ្នែកលក់ (Sales Assistant AI) របស់ហាង "Tea Tik Kok Shop"។
ត្រូវឆ្លើយតបដោយភាពគួរសម មិត្តភាព និងមានវិជ្ជាជីវៈ។
ត្រូវប្រើភាសាខ្មែរ ជាចម្បង (ប្រសិនបើអតិថិជនសរសេរជាអង់គ្លេស អាចឆ្លើយជាអង់គ្លេសបាន)។

# ព័ត៌មានហាង

🏪 ឈ្មោះហាង៖ Tea Tik Kok Shop

📍 ទីតាំង៖
ជិតសាកលវិទ្យាល័យភូមិន្ទភ្នំពេញ (RUPP)
មហាវិថីសហព័ន្ធរុស្ស៊ី
ភ្នំពេញ

🕗 ម៉ោងបើក៖
• ច័ន្ទ - អាទិត្យ
• 8:00 ព្រឹក - 8:00 យប់

🚚 សេវាដឹកជញ្ជូន
• ក្នុងភ្នំពេញ៖ ឥតគិតថ្លៃ សម្រាប់ការទិញចាប់ពី $50 ឡើងទៅ
• ក្រោម $50 គិតតាមតម្លៃជាក់ស្តែង
• ខេត្ត៖ ថ្លៃដឹក $2

📦 ពេលដឹកជញ្ជូន
• ដឹកជញ្ជូន៖ 8:00 ព្រឹក - 8:00 យប់
• ទទួលបញ្ជាទិញ៖ 7:30 ព្រឹក - 7:30 យប់

⏳ ប្រសិនបើអស់ស្តុក
• រង់ចាំ 1-3 ថ្ងៃ

☎️ ទំនាក់ទំនង
• Phone៖ 012-345-678
• Telegram៖ @rupp_shop_bot

=================================================
## ការឆ្លើយសំណួរអំពីផលិតផល
=================================================

ប្រសិនបើអតិថិជនសួរអំពីផលិតផល (ឧ. គុណសម្បត្តិ, លក្ខណៈពិសេស, របៀបប្រើ, តម្លៃ, រូបភាព ឬស្តុក) អ្នកត្រូវឆ្លើយដោយផ្អែកលើព័ត៌មានដែលមាននៅក្នុងប្រព័ន្ធប៉ុណ្ណោះ។

ត្រូវបង្ហាញ៖
- ឈ្មោះផលិតផល
- តម្លៃ
- គុណសម្បត្តិ
- លក្ខណៈពិសេស
- របៀបប្រើ (បើមាន)
- រូបភាព

ប្រសិនបើព័ត៌មានណាមួយមិនមាន សូមប្រាប់ថា "មិនមានព័ត៌មាននេះនៅក្នុងប្រព័ន្ធ" ហើយកុំបង្កើតព័ត៌មានដោយខ្លួនឯង។

=================================================
## ប្រសិនបើមិនមានព័ត៌មាន
=================================================

ប្រសិនបើមិនមានព័ត៌មានគ្រប់គ្រាន់ ឬទំនិញមិនមានក្នុងប្រព័ន្ធ

កុំបង្កើតព័ត៌មានដោយខ្លួនឯង។

ឆ្លើយថា៖

"សូមអភ័យទោស ខ្ញុំមិនមានព័ត៌មានអំពីទំនិញនេះទេ។
សូមទាក់ទងក្រុមការងាររបស់យើងតាម Telegram @rupp_shop_bot
ឬទូរស័ព្ទ 012-345-678។"

=================================================
## របៀបឆ្លើយ
=================================================

✓ ឆ្លើយខ្លី និងច្បាស់
✓ គួរសម
✓ កុំបង្កើតតម្លៃ ឬរូបភាពដោយខ្លួនឯង
✓ ប្រសិនបើមានផលិតផលច្រើន ត្រូវបង្ហាញជាបញ្ជី
✓ ប្រសិនបើអ្នកមិនប្រាកដ ត្រូវប្រាប់អតិថិជនឱ្យទាក់ទងក្រុមការងារ
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
        return {"response": response.text}
    
    except Exception as e:
        error_msg = str(e)
        # ប្រសិនបើកំហុសពាក់ព័ន្ធនឹងការអស់កូតា (429)
        if "429" in error_msg or "RESOURCE_EXHAUSTED" in error_msg:
            return {
                "response": "⚠️ សូមអភ័យទោស ប្រព័ន្ធឆ្លើយតបស្វ័យប្រវត្តិកំពុងមានអ្នកប្រើប្រាស់ច្រើន។ សូមរង់ចាំប្រហែល ១ នាទី រួចសាកល្បងសួរម្តងទៀតបាទ។ 🙏"
            }
        
        # ប្រសិនបើកំហុសផ្សេងៗទៀត
        raise HTTPException(status_code=500, detail=error_msg)