# R2 Calendar

## ⚡一句話說明⚡:使用React 與 Custom Post Type建立一個行事曆系統（日記），用戶可以自行發文以及設定狀態公開與私人

1. 如何使用<br>
   使用短碼[r2_calendar_shortcode]即可在頁面上顯示行事曆
<img src="https://github.com/s0985514623/R2-Calendar/assets/35906564/3424a705-7f12-4c3a-b374-5899cc9e371a">


2. 單擊日期開啟編輯畫面<br>
時間會自動取得你點擊的日期，也可以再自行點開更改日期，狀態預設有私人及公開兩種狀態
<img src="https://github.com/s0985514623/R2-Calendar/assets/35906564/e56fcf21-7200-40df-8ad3-fee92de9026e">

3. 查看使用者日記<br>
只能查看自己與公開的日記，私人日記為紅色標記，公開日記為綠色標記
<img src="https://github.com/s0985514623/R2-Calendar/assets/35906564/ac959c8d-c5b4-47d7-b5f0-186dea6e20e1">

4. 更新日記<br>
再次點擊日記即可展開彈窗進行編輯，只能編輯自己的日記，其他使用者的日記只能查看
<img src="https://github.com/s0985514623/R2-Calendar/assets/35906564/1f4877f1-5bca-4a23-bd89-11ecd6936675">
<img src="https://github.com/s0985514623/R2-Calendar/assets/35906564/e42feddc-7f38-4b54-8175-fd64b8724187">

#### 如果有安裝WordFence 會有無法取得日記的問題，請依下方步驟執行
1. 前往WordFence>Firewall 設定頁面<br>
2. 找到Brute Force Protection頁籤並展開<br>
3. 找到Additional Options裡面的Prevent discovery of usernames through '/?author=N' scans, the oEmbed API, the WordPress REST API, and WordPress XML Sitemaps關閉<br>
<img src="https://github.com/s0985514623/R2-Calendar/assets/35906564/623ec232-cf9f-427e-a5fe-02740ebdc3a5">


## Reference

1. Inspired by [Vite for WP](https://github.com/kucrut/vite-for-wp)
2. Inspired by [J7](https://github.com/j7-dev)
2. API design Inspired by [Refine](https://refine.dev/)
3. [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/reference/)
