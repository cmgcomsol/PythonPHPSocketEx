'''
more geeky way for process and less error prone i suppose.
'''

import socket
import multiprocessing
import time
import json
from datetime import datetime
from PIL import Image
import hashlib
import base64
import io


def MyServerFunction(clientSocket:socket,endProgram:int):

	byteObj = clientSocket.recv(2097152)
	# b=bytearray();
	print("Received obj", byteObj)
	msg = byteObj.decode("utf8")
	msg = msg.strip()
	# print("After message decoding",msg,type(msg))
	# print(msg=="NAME")

	if msg == "NAME":
		print("Identfied name request")
		clientSocket.send(b"SERVER running courtesy PYTHON\r\n")
		clientSocket.close()
		return 0

	if msg == "END":
		endProgram.value = 1
		print("Identfied end request")
		clientSocket.send(b"OK ending!!!\r\n")
		clientSocket.close()
		return 0

	print(msg)
	decodeddata = json.loads(msg)

	if type(decodeddata) == dict and decodeddata['control'] == 'test':
		print(decodeddata)
		newdata = {}
		newdata['control'] = 'testresult'
		if decodeddata['gender'] == 'male':
			newdata['name'] = "Mr. " + decodeddata['name']
		else:
			newdata['name'] = "Ms. " + decodeddata['name']

		dtobj = datetime.strptime(decodeddata['dob'], "%d %B %Y")
		year = datetime.now().year - dtobj.year
		if datetime.now().month < dtobj.month:
			print("Month >")
			year -= 1
		elif datetime.now().month == dtobj.month and datetime.now().day < dtobj.day:
			print("Month = and day >=")
			year -= 1
		age = year
		newdata['age'] = age
		reply = bytearray(json.dumps(newdata), 'utf8')
		reply.append(ord('\r'))
		reply.append(ord('\n'))
		print("sending reply")
		print(reply)
		clientSocket.send(reply)

	elif type(decodeddata) == dict and decodeddata['control'] == 'test3':
		print("Image socket test huh!!!")
		imagedata = base64.b64decode(decodeddata['data'])

		hashobj = hashlib.md5()
		hashobj.update(imagedata)

		if hashobj.hexdigest() != decodeddata['md5']:
			print("hash mismatch", hashobj.hexdigest(), decodeddata['md5'])
			clientSocket.send(b"file hash mismatch!!!\r\n")
		else:
			newdata = {}
			image = Image.open(io.BytesIO(imagedata))
			image = image.rotate(-90)
			newdata['filename'] = 'rotated-arrow.jpg'

			imgByteArr = io.BytesIO()
			#image.save("modified.jpg", format='JPEG')  # for testing if its good
			image.save(imgByteArr, format='JPEG')  # for sending
			imgByteArr = imgByteArr.getvalue()

			hashobj = hashlib.md5()
			hashobj.update(imgByteArr)
			newdata['md5'] = hashobj.hexdigest()

			newdata['data'] = base64.b64encode(imgByteArr)

			newdata['control'] = 'testresult'

			reply = bytearray(json.dumps(str(newdata)), 'utf8')
			print("sending reply")
			print(reply)
			clientSocket.send(reply)


	else:
		print(msg)
		clientSocket.send(b"Unidetfied command!!!\r\n")

	print("Unexpected stage reached")
	clientSocket.close()

	# self.clientSocket.close()



def tryToBindToPort(mySocket):
	'''
	to make sure the socket is binded to on host to port else it will fail on
	single attempt if port is blocked by any previous thread/process

	:param mySocket of type socke:
	:return:
	'''
	sec=1;
	while True: #wait indefinitely
		time.sleep(1)
		try:
			mySocket.bind(('127.0.0.1', 12000))
			return mySocket
		except OSError as e:
			print(sec,e)
			sec+=1


#create an INET, STREAMing socket
serversocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
#bind the socket to a public host,
# and a well-known port

#binding by me is done in trytobindtoport so ingoring it
#serversocket.bind(('127.0.0.1', 12000))


serversocket=tryToBindToPort(serversocket)



#become a server socket
serversocket.listen(5)

myConnList=[]
count=1

endProgram = multiprocessing.Value('i', 0) #for sharing data between processes
while 1:

	print(endProgram.value)
	if endProgram.value == 1:
		print(count, "Ending...")
		time.sleep(1)
		for item in myConnList:
			if item.is_alive():
				item.join()
		break


	#accept connections from outside
	print(count,"Waiting for new connection...")
	serversocket.settimeout(1)
	try:
		(clientsocket, address) = serversocket.accept()
	except Exception as e:
		print("timed out server socket")
		continue

	print(count,"Got new socket...")
    #now do something with the clientsocket
	#in this case, we'll pretend this is a threaded server
	print(count,"handling request")


	#myConnList.append(MyServerSocket(clientsocket))

	myConnList.append(multiprocessing.Process(target=MyServerFunction, args=(clientsocket,endProgram)))
	myConnList[-1].start()

	tmpList=[]
	for item in myConnList:
	    if item.is_alive():
		    tmpList.append(item)

	myConnList=tmpList
	print(count,"there are",len(myConnList),"working client connection(s) in list")

	count += 1

serversocket.close()
