from fastapi import APIRouter
from pydantic import BaseModel

router = APIRouter(prefix="/chat", tags=["Chat"])

class ChatRequest(BaseModel):
    message: str

@router.post("/")
def chat(data: ChatRequest):

    msg = data.message.lower()

    if any(word in msg for word in ["hello", "hi", "hey"]):
        reply = "Hello! Welcome to Tea Tik kok Shop! How can I help you today?"
    elif any(word in msg for word in ["price", "cost", "how much"]):
        reply = "Please tell me the name of the product you're interested in, and I'll check the price for you."
    elif any(word in msg for word in ["shipping", "delivery"]):
        reply = "We offer free standard shipping on all orders! Delivery usually takes 3-5 business days."
    elif any(word in msg for word in ["return", "refund"]):
        reply = "We have a 30-day hassle-free return policy. If you're not satisfied, you can return your item for a full refund."
    elif any(word in msg for word in ["track", "status"]):
        reply = "You can track your order by clicking the 'Track Order' link in the top menu and entering your order number."
    elif any(word in msg for word in ["payment", "credit card", "paypal", "pay"]):
        reply = "We accept all major credit cards (Visa, MasterCard, Amex) as well as PayPal and Apple Pay."
    elif any(word in msg for word in ["contact", "support", "human"]):
        reply = "You can reach our human support team by visiting the 'Contact' page or emailing support@teatikkok.com."
    else:
        reply = "I'm sorry, I don't quite understand. Could you rephrase your question, or ask about our shipping, returns, or products?"

    return {"reply": reply}
