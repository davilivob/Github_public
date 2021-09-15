import requests as req
from bs4 import BeautifulSoup as beauty
import os
import json
import urllib.request as ureq


class Y:
    y = 1


title = input("請輸入欲搜尋圖片之標題 : ")
page = input("請輸入頁數(一頁20張) : ")
p = int(page)


def imagedown(url, folder):
    null = 0
    request = ureq.Request(
        url,
        headers={
            "Content-Type": "application/json",
            "User-Agent": "Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1",
        },
    )
    with ureq.urlopen(request) as response:
        result = response.read().decode("utf-8")
    result = json.loads(result)
    for x in range(20):

        image = result["results"][x]["urls"]["regular"]
        h = result["results"][x]["height"]
        w = result["results"][x]["width"]
        if result["results"][x]["alt_description"] != None:
            name = result["results"][x]["alt_description"]
        else:
            name = "notitle" "_" + str(null)
            null += 1
        if h / w > 1.2:
            with open(folder + "/" + "ABC000" + str(Y.y) + ".jpg", "wb") as f:
                im = req.get(image)
                f.write(im.content)
                print(str(run_page - 1) + "_" +
                      "downloading :", x, " : ", name)
                # name.replace(' ', '-').replace('/', '')
                Y.y += 1


for run_page in range(2, 2 + p):
    imagedown(
        "https://unsplash.com/napi/search/photos?query="
        + title
        + "&per_page=20&page="
        + str(run_page)
        + "&xp=",
        "F:/20210401_finaltest",
    )
