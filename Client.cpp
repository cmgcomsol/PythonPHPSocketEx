#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netdb.h> 

void error(const char *msg)
{
    perror(msg);
    exit(0);
}

bool doIEnd(char cmd[]){
	printf("\n for comparisson %s",cmd);
	
	if(cmd[0]=='E' && cmd[1]=='N' && cmd[2]=='D' ){
		printf("\nReturning True\n");
		return true;
	}
	printf("\nReturning False\n");
	return false;
}

int main(int argc, char *argv[])
{
    int sockfd, portno, n;
    struct sockaddr_in serv_addr;
    struct hostent *server;

    char buffer[1024];
    /*if (argc < 3) {
       fprintf(stderr,"usage %s hostname port\n", argv[0]);
       exit(0);
    }*/
    
	while(1){
		portno = 12000;
		sockfd = socket(AF_INET, SOCK_STREAM, 0);
		if (sockfd < 0) 
			error("ERROR opening socket");
			
		server = gethostbyname("localhost");
		if (server == NULL) {
			fprintf(stderr,"ERROR, no such host\n");
			exit(0);
		}
	
	
		bzero((char *) &serv_addr, sizeof(serv_addr)); //clear serv_addr memory
	
		serv_addr.sin_family = AF_INET;
		
		bcopy((char *)server->h_addr, 
			 (char *)&serv_addr.sin_addr.s_addr,
			 server->h_length);
			 
		serv_addr.sin_port = htons(portno);
		
		
		if (connect(sockfd,(struct sockaddr *) &serv_addr,sizeof(serv_addr)) < 0) 
        error("ERROR connecting");
		
		printf("Please enter the message: ");
		bzero(buffer,1024);
		fgets(buffer,1023,stdin);
		
		if(buffer[0]=='\n'){
			close(sockfd);
			break;
		}
		
		n = write(sockfd,buffer,strlen(buffer));
		
		if (n < 0) 
			 error("ERROR writing to socket");
			 
		printf("\nMessage sent :%s\n",buffer);
		
		
		if(doIEnd(buffer)){
			close(sockfd);
			break;
		}
		
		
		
		bzero(buffer,1024);
		
		n = read(sockfd,buffer,1023);
		if (n < 0) 
			 error("ERROR reading from socket");
		printf("%s\n",buffer);
	
		close(sockfd);
    }
	
	
	return 0;
}
