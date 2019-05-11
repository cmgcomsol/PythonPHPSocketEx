import multiprocessing
import socket




def MyClientFunction(counter):
	s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
	s.connect(("localhost", 12000))
	s.send(b"NAME\r\n")
	print(counter, ":", str(s.recv(1024)))
	s.close()

myClientList=[]
for i in range(1000):
	print("Sending request",i)
	myClientList.append(multiprocessing.Process(target=MyClientFunction, args=(i,)))
	myClientList[-1].start()

while True:
	count=0
	for item in myClientList:
		if item.is_alive():
			count +=1
	print(count,"processes still alive")
	if count==0:
		break


#for ending this one use the threaded client i am little tired for thinking this one out.
#changed my mind

s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect(("localhost", 12000))
s.send(b"END\r\n")
print(str(s.recv(1024)))
s.close()

