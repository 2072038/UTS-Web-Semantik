const express = require('express');
const bodyParser = require('body-parser');
const Parser = require('rss-parser');
const parser = new Parser();
const app = express();
const port = 3000;

// Membaca RSS feed dan menampilkan dalam bentuk paging
app.get('/news', async (req, res) => {
    try {
        const feed = await parser.parseURL('https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml');

        const page = parseInt(req.query.page) || 1;
        const pageSize = parseInt(req.query.pageSize) || 10;
        const startIndex = (page - 1) * pageSize;
        const endIndex = page * pageSize;

        // Mengambil berita sesuai dengan halaman dan ukuran halaman
        const news = feed.items.slice(startIndex, endIndex);

        const response = {
            totalItems: feed.items.length,
            totalPages: Math.ceil(feed.items.length / pageSize),
            currentPage: page,
            pageSize: pageSize,
            news: news
        };

        res.json(response);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

// Menyimpan list RSS feeds
let rssFeeds = [];

// Menambahkan RSS feed
app.post('/add_feeds', async (req, res) => {
    const { url } = req.body;
    try {
        const feed = await parser.parseURL(url);
        rssFeeds.push(feed);
        res.status(201).json(feed);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Menghapus RSS feed dari URL
app.delete('/delete_feeds/:url', (req, res) => {
    const url = req.params.url;
    const index = rssFeeds.findIndex(feed => feed.feedUrl === url);
    if (index !== -1) {
        rssFeeds.splice(index, 1);
        res.status(204).send();
    } else {
        res.status(404).json({ error: 'RSS feed tidak ditemukan' });
    }
});

// Mengubah RSS feed dari URL
app.put('/update_feeds/:url', async (req, res) => {
    const url = req.params.url;
    const { newUrl } = req.body;
    try {
        const feed = await parser.parseURL(newUrl);
        const index = rssFeeds.findIndex(feed => feed.feedUrl === url);
        if (index !== -1) {
            rssFeeds[index] = feed;
            res.status(200).json(feed);
        } else {
            res.status(404).json({ error: 'RSS feed tidak ditemukan' });
        }
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.listen(port, () => {
    console.log(`Server running on port ${port}`);
});
