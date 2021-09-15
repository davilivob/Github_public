import requests as req
from bs4 import BeautifulSoup as beauty
import os
import json
import urllib.request as ureq

# class page:
#     page = 2
#     p = str(page)
class yy:
    y = 1


title = input("請輸入欲搜尋圖片之標題 : ")
page = input("請輸入頁數(一頁20張) : ")
p = int(page)


def imagedown(url, folder):
    # try:
    #     os.mkdir(os.path.join(os.getcwd(), folder))
    # except:
    #     print("not work")
    #     page += 1
    #     pass
    # os.chdir(os.path.join(os.getcwd(), folder))
    # r = req.get(url)
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
            with open(folder + "/" + "ABC000" + str(yy.y) + ".jpg", "wb") as f:
                im = req.get(image)
                f.write(im.content)
                print(str(run_page - 1) + "_" + "downloading :", x, " : ", name)
                # name.replace(' ', '-').replace('/', '')
                yy.y += 1
    # for img in images:
    #     # strimg = str(img)
    #     image = img["urls"]["regular"]
    #     name = img["alt_description"]
    #     print(image)
    # soup = beauty(r.text, 'html.parser')

    # images = soup.find_all('img')
    # print(images)
    # x = 0
    # for image in images:

    #     # link = image['src']
    #     # print(name)
    # xx = str(x)


# def firstpage(url, folder):
#     r = req.get(url)
#     soup = beauty(r.text, 'html.parser')

#     images = soup.find_all('img')

#     x = 0
#     # print(images)
#     for image in images:

#         link = image['src']
#         # names = image['alt']
#         # print(names)
#         xx = str(x)
#         with open(xx + '.jpg', 'wb') as f:
#             im = req.get(image)
#             f.write(im.content)
#             x += 1
#             print('writing :', xx)


for run_page in range(2, 2 + p):
    imagedown(
        "https://unsplash.com/napi/search/photos?query="
        + title
        + "&per_page=20&page="
        + str(run_page)
        + "&xp=",
        "F:/20210401_finaltest",
    )
    # firstpage("https://unsplash.com/s/photos/" + title, "F:/imagestest5")
