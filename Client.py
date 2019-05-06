import threading
import socket

class MyClientSocket (threading.Thread):
	def __init__(self,counter):
		threading.Thread.__init__(self)
		self.counter=counter


	def run(self):
		s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		s.connect(("localhost", 12000))
		s.send(b"NAME\r\n")
		print(self.counter,":",str(s.recv(1024)))
		s.close()

myClientList=[]
for i in range(1000):
	myClientList.append(MyClientSocket(i))
	myClientList[-1].run()

s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect(("localhost", 12000))
s.send(b"END\r\n")
print(str(s.recv(1024)))
s.close()
