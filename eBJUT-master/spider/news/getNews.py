import requests
import re
import json
import threading
import time
import mysql.connector
from bs4 import BeautifulSoup

# Load Config
f = open("config.json", "r")
config = json.load(f)

# User-Agent
userAgent = config['spider']['user-agent']

# BJUT Account
username = config['spider']['username']
password = config['spider']['password']

# Enable Web VPN
enable_web_vpn = config['spider']['enable_web_vpn']

# SSO Auth URL
loginURL = "https://cas.bjut.edu.cn/login" if not enable_web_vpn else "https://cas.webvpn.bjut.edu.cn/login"

# Main URL
mainURL = "https://my.bjut.edu.cn" if not enable_web_vpn else "https://my-443.webvpn.bjut.edu.cn"

# Item limit
# Default: "10"
item_limit = str(config['spider']['item_limit'])

# Threads per second
threads_per_sec = config['spider']['threads_per_sec']

# News List URL
newsURLs = [
    ["校发通知",
     mainURL + "/group/undergraduate/info?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_L1qbLZUzTY0Z&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_L1qbLZUzTY0Z_action=listMoreAjaxQuery&sEcho=2&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["教学通告",
     mainURL + "/group/undergraduate/index?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_VsXYk6XfotYw&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_VsXYk6XfotYw_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["院部处通知",
     mainURL + "/group/undergraduate/index?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_BCM3YjYVWLgs&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_BCM3YjYVWLgs_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["公示公告",
     mainURL + "/group/undergraduate/index?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_o85oRAHYRrFd&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_o85oRAHYRrFd_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["院部处工作信息",
     mainURL + "/group/undergraduate/index?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_5r4J0rBrzqET&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_5r4J0rBrzqET_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["科技动态",
     mainURL + "/group/undergraduate/index?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_SidZJWIddBMh&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_SidZJWIddBMh_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["海报",
     mainURL + "/group/undergraduate/index?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_7DI6skwJzgqc&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_7DI6skwJzgqc_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["会议通知",
     mainURL + "/group/undergraduate/info?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_oRzTnpMMCW0K&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_oRzTnpMMCW0K_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["学校工作信息",
     mainURL + "/group/undergraduate/info?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_GE0UiuXeUNxI&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_GE0UiuXeUNxI_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"],
    ["迎新离校工作通知",
     mainURL + "/group/undergraduate/info?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_IQS6jsQeo1pf&p_p_lifecycle=0&p_p_state=exclusive&p_p_mode=view&_bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_IQS6jsQeo1pf_action=listMoreAjaxQuery&sEcho=1&iColumns=3&sColumns=title%2Cpublis_dept%2Cpublished&iDisplayStart=0&iDisplayLength=" + item_limit + "&mDataProp_0=title&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=false&mDataProp_1=publis_dept&sSearch_1=&bRegex_1=false&bSearchable_1=false&bSortable_1=false&mDataProp_2=published&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1"]
]

# Database
mydb = mysql.connector.connect(
    host=config['database']['hostname'],
    user=config['database']['username'],
    passwd=config['database']['password'],
    database=config['database']['database'],
    port=config['database']['port']
)

f.close()

max_news_id = 0


def work(newsWid, cursor, sess):
    data = {}
    try:
        data['title'] = newsTitles[i].strip()
    except:
        data['title'] = newsTitles[i]
    try:
        data['publishDate'] = newsDate[i].strip()
    except:
        data['publishDate'] = newsDate[i]
    try:
        data['department'] = newsDepartment[i].strip()
    except:
        data['department'] = newsDepartment[i]
    data['isExternal'] = (newsWids[i] is None)
    try:
        data['externalURL'] = newsExternalURLs[i].strip().replace("my.bjut.edu.cn", "my-443.webvpn.bjut.edu.cn")
    except:
        data['externalURL'] = newsExternalURLs[i]
    try:
        data['category'] = newsURLs[j][0].strip()
    except:
        data['category'] = newsURLs[j][0]

    if newsExternalURLs[i] is None:
        try:
            r = sess.get(
                mainURL + "/group/undergraduate/index?p_p_id=bulletinListForCustom_WAR_infoDiffusionV2portlet_INSTANCE_"
                          "VsXYk6XfotYw&p_p_lifecycle=0&p_p_state=pop_up&p_p_mode=view&_bulletinListForCustom_WAR_infoD"
                          "iffusionV2portlet_INSTANCE_VsXYk6XfotYw_action=browse&wid=" + newsWid)

            soup = BeautifulSoup(r.text, 'html.parser')
            newsContentsTmp = str(soup.findAll("div", {"class": "news-cr uecontent"})[0])

            # detect and remove image
            data['hasImage'] = False
            try:
                for imgContent in re.findall("(<img.*?/>)", newsContentsTmp):
                    newsContentsTmp = newsContentsTmp.replace(imgContent, "")
                    data['hasImage'] = True
                for imgContent in re.findall("(</img>)", newsContentsTmp):
                    newsContentsTmp = newsContentsTmp.replace(imgContent, "")
            except:
                pass

            # detect attachment
            if soup.findAll("div", {"class": "battch"}, newsContentsTmp) or "附件" in newsContentsTmp:
                data['hasAttachment'] = True
            else:
                data['hasAttachment'] = False
            data['content'] = newsContentsTmp.strip()
        except:
            pass
    else:
        data['content'] = None
        data['hasAttachment'] = False
        data['hasImage'] = False

    global max_news_id
    # print(data)
    try:
        cursor.execute(
            "INSERT IGNORE INTO news (news_id, news_title, news_publish_date, news_department, news_is_external, news_external_url, news_category, news_has_image, news_has_attachment, news_content) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
            [max_news_id + 1, data['title'], data['publishDate'], data['department'],
             data['isExternal'], data['externalURL'], data['category'],
             data['hasImage'], data['hasAttachment'], data['content']])
        mydb.commit()
        if cursor.rowcount == 1:
            print("News [" + data['title'] + "] found!")
            max_news_id += 1
        else:
            print("News [" + data['title'] + "] exist!")
            return False
        return True
    except:
        print("Failed!")
        return False


if __name__ == '__main__':

    headers = {
        "User-Agent": userAgent
    }
    sess = requests.session()
    sess.headers.update(headers)

    try:
        r = sess.get(loginURL)
    except:
        exit()

    param = re.findall(
        "<input type=\"hidden\" name=\"lt\" value=\"(.*)\" />[\s]*<input type=\"hidden\" name=\"execution\" value=\"(.*)\" />",
        r.text)
    # print(param[0][0])
    payload = {
        'username': username,
        'password': password,
        'lt': param[0][0],
        'execution': param[0][1],
        '_eventId': "submit"

    }
    r = sess.post(loginURL, data=payload)
    # print(r.text)

    try:
        cursor = mydb.cursor(buffered=True)
        cursor.execute("SELECT MAX(news_id) FROM news")
        max_news_id = cursor.fetchone()[0]
        for j in range(len(newsURLs)):
            r = sess.get(newsURLs[j][1])
            r = json.loads(r.text)

            newsWids = []
            newsTitles = []
            newsContents = []
            newsDate = []
            newsDepartment = []
            newsExternalURLs = []

            for i in range(len(r['aaData'])):
                newsDate.append(r['aaData'][i]['published'])
                newsDepartment.append(r['aaData'][i]['publis_dept'])

                # detect access by wid or external link
                try:
                    # match news wid
                    newsWids.append(
                        re.findall("[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}",
                                   r['aaData'][i]['title'])[0])
                    newsExternalURLs.append(None)
                except:
                    # match <a href="">
                    newsExternalURLs.append(re.findall("<a href=\'(.*?)\'", r['aaData'][i]['title'])[0])
                    newsWids.append(None)

                # match news title
                newsTitles.append(re.findall("title='(.*?)'.*?>", r['aaData'][i]['title'])[0])

            threads = []
            print("len = " + str(len(newsWids)))

            # fetch news details
            waitcount = 0
            for i in range(len(newsWids)):
                t = threading.Thread(target=work, args=(newsWids[i], cursor, sess))
                t.setDaemon(False)
                t.start()
                t.join()
                threads.append(t)
                # waitcount = waitcount + 1
                # if waitcount % threads_per_sec == 0:
                #     time.sleep(1)

            # for thr in threads:
            #     if thr.is_alive():
            #         thr.join(5)

            print("Job finished.")

        cursor.close()
        mydb.close()
    except Exception as e:
        print(e)
        print("Database connection failed!")
        exit()
