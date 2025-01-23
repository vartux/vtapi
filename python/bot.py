from telegram import Update
from telegram.ext import Application, CommandHandler, ContextTypes,MessageHandler,CallbackContext,filters
from api import Api

USER_DATA = {}

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat_id = update.effective_chat.id
    USER_DATA[chat_id] = {"step": "ask_phone"}
    await update.message.reply_text("Hola, soy tu bot!")
    await update.message.reply_text("Ingresa numero de telefono ex:+5804120000000: ")

async def handle_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat_id = update.effective_chat.id
    text = update.message.text
    print(chat_id)
    if chat_id in USER_DATA:
        step = USER_DATA[chat_id].get("step")

        if step == "ask_phone":
            USER_DATA[chat_id] = {
                "phone": text,
                "step": "ask_message"
                }
            await update.message.reply_text("Mensaje a enviar: ")


        elif step == "ask_message":
            USER_DATA[chat_id]["message"] = text
            print(USER_DATA)
            await update.message.reply_text(USER_DATA[chat_id]["message"] +" send to "+ USER_DATA[chat_id]["phone"])
            r = Api()
            r.sendSms(USER_DATA[chat_id]["phone"],USER_DATA[chat_id]["message"])
            del USER_DATA[chat_id]  # Limpiar datos después del flujo



async def help_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text("Usa /start para iniciar.")

app = Application.builder().token("8017990448:AAHs69vwYqhHU6ifH-8ahqjzqaJTHxQlTY8").build()

app.add_handler(CommandHandler("start", start))
app.add_handler(CommandHandler("help", help_command))
app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_message))


app.run_polling()
