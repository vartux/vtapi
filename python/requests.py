#!bin/python
import requests

class Request:
    def __init__(self):
        self.__endpoint = "https://vtapi-production.up.railway.app/api/ws/send"
        self.__headers = {
            'Accept': 'application/json',
            'Authorization': 'Bearer 3|dfsQRpeJ64KfvNIDWHSEvhgtRpuUOdPOrZJC1QCt3d4b88b3',
        }
    
    def sendSms(self,phone,message):
        data = {
            'phone': phone,
            'message': message
        }
        try:
            r = requests.post(url = self.__endpoint, params = data, headers=self.__headers)
        except:
            print("Request Error!")
        
        if(r.status_code != 200):
            print("Error en request")
            print(r.json()["errors"])

r = Request()
r.sendSms("+5842449840875","holis")
