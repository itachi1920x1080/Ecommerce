from fastapi import APIRouter
from pydantic import BaseModel

router = APIRouter(prefix="/chat", tags=["Chat"])

class ChatRequest(BaseModel):
    message: str

@router.post("/")
def chat(data: ChatRequest):

    msg = data.message.lower()

    if "hello" in msg:
        reply = "Hello! How can I help you?"
    elif "price" in msg:
        reply = "Please tell me the product name."
    else:
        reply = "I don't understand."

    return {"reply": reply}
