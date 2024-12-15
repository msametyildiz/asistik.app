const express = require('express');
const cors = require('cors');
const app = express();

app.use(cors());
app.use(express.json());

// Mock veri
const jobs = [
    { title: "Test1", status: "pending", type: "full-time", date: "2024-12-05" },
    { title: "Test2", status: "approved", type: "part-time", date: "2024-12-04" },
    { title: "Test3", status: "pending", type: "internship", date: "2024-12-03" }
];

// GET Endpoint
app.get('/jobs', (req, res) => {
    res.json(jobs);
});

// Sunucuyu başlat
const PORT = 5000;
app.listen(PORT, () => console.log(`Server is running on http://localhost:${PORT}`));


app.post('/jobs', (req, res) => {
    const newJob = req.body;
    jobs.push(newJob);
    res.status(201).json(newJob);
});

app.get('/jobs', (req, res) => {
    const { status, type, sort } = req.query;
    let filteredJobs = jobs;

    if (status) filteredJobs = filteredJobs.filter(job => job.status === status);
    if (type) filteredJobs = filteredJobs.filter(job => job.type === type);

    if (sort === "newest") {
        filteredJobs.sort((a, b) => new Date(b.date) - new Date(a.date));
    } else if (sort === "oldest") {
        filteredJobs.sort((a, b) => new Date(a.date) - new Date(b.date));
    }

    res.json(filteredJobs);
});



