#!/usr/bin/python

# This script is meant to be run as a daemon. It periodically refreshes
# the available jobs and uses the schedule info for each job to execute
# it at the right time.
# Assumptions: 
# 1) no websites will be deleted. 
# 2) if a scrape is running and scrapeInterval or scrapeDepth are changed
#    then these will only take effect for the following scrape
import time
import threading
import urllib2
import datetime
import _mysql

# Contstants
TIMESTEP = 1 # minutes
REFRESH_TIME = 1 # minutes
SCRAPE_SCRIPT="http://csil-srprj-2.cs.uiuc.edu/monarch/tests/testcrawl.php"



# Define thread
class JobThread(threading.Thread):
    def __init__(self, link):
        self.link = link
        threading.Thread.__init__(self)
	print datetime.datetime.now(),": Thread started for ",link
       
    def run(self):
        urllib2.urlopen(self.link)
        
def main():
	stepcount = 0
        jobs = [] 
	# Script execution
	db = _mysql.connect(host="localhost", user="ryan",
			    passwd="adobe", db="communityanalysis", port=3306, 
			    unix_socket="/usr/local/mysql/mysql.sock") 

	while True:
	    if stepcount %  REFRESH_TIME == 0:
		db.query("SELECT * FROM websites")
		# Get result as a tuple of dictionaries
		jobs = db.store_result().fetch_row(maxrows=0, # get all rows
	                                           how=1)
	    for job in jobs:
         	 if stepcount % int(job["scrapeInterval"]) == 0:
                     # Execute job
	            JobThread(SCRAPE_SCRIPT+'?name='+job["name"]+'&topLevel='+job["scrapeNumTopLevel"]).start()
        
	    time.sleep(TIMESTEP * 60)
	    stepcount += 1

if __name__ =="__main__":
    main()
