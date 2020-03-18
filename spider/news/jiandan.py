import requests
import re
import json
import threading
import time
import mysql.connector
import random
from bs4 import BeautifulSoup

# Load Config
f = open("config.json", "r")
config = json.load(f)

mydb = mysql.connector.connect(
    host=config['database']['hostname'],
    user=config['database']['username'],
    passwd=config['database']['password'],
    database=config['database']['database'],
    port=config['database']['port']
)

f.close()

userAgent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6"

headers = {
    "User-Agent": userAgent,
}

if __name__ == '__main__':
    cursor = mydb.cursor(buffered=True)

    res = []
    for i in range(1, 25):
        url = "http://jandan.net/treehole/page-" + str(25 - i)
        try:
            r = requests.get(url, timeout=1, )
            tmp = re.findall("<p>(.*?)</p>", r.text)
            for i in tmp:
                if i != '\'+ nl2br($(\'#comment\').val()) +\'':
                    res.append(i)
                    cursor.execute("SELECT 1 FROM moments WHERE moment_content = %s", (i, ))
                    if cursor.rowcount == 0:
                        print(i)
                        cursor.execute("INSERT INTO moments (user_id, moment_content) VALUES (%s, %s)", (random.randint(292, 450), i))
                        mydb.commit()
        except:
            print("timeout")

        mydb.commit()