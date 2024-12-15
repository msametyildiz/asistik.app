<?php include 'include/session_handler.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASİSTİK</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/all_job_style.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
    <script src="assets/js/disable_keys.js"></script>
</head>

<body>
    <div id="root">
    <?php include 'include/sidebar.php'; ?>

        <main class="dashboard">
        <?php include 'include/navbar.php'; ?>

            <section class="filter-form">
                <h4 class="filter-title">Search Form</h4>
                <form id="search-form">
                    <div class="form-row">
                        <label for="search">Search</label>
                        <input type="text" id="search" name="search" placeholder="Enter keyword">
                    </div>
                    <div class="form-row">
                        <label for="job-status">Job Status</label>
                        <select id="job-status" name="jobStatus">
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="job-type">Job Type</label>
                        <select id="job-type" name="jobType">
                            <option value="full-time">Full-Time</option>
                            <option value="part-time">Part-Time</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="sort">Sort</label>
                        <select id="sort" name="sort">
                            <option value="newest">Newest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>
                    <button type="reset" class="reset-btn">Reset Search Values</button>
                </form>
            </section>

            <section class="dashboard-content">
                <h4 class="dashboard-title">3 İş Bulundu</h4>
                <div class="job-cards-container">
                    <div class="job-card">
                        <div class="job-card-header">
                            <div class="job-icon">T</div>
                            <div class="job-info">
                                <h5 class="job-title">Test3</h5>
                                <p class="job-subtitle">Test3</p>
                            </div>
                        </div>
                        <div class="job-card-body">
                            <p><span>📍</span> Test3</p>
                            <p><span>📅</span> Dec 4th, 2024</p>
                            <p><span>💼</span> Full-Time</p>
                            <p class="job-status pending">Pending</p>
                        </div>
                        <div class="job-card-footer">
                            <button class="btn edit-btn">Edit</button>
                            <button class="btn delete-btn">Delete</button>
                        </div>
                    </div>

                    <!-- Aynı yapıyı diğer iş kartları için kopyala -->
                    <div class="job-card">
                        <div class="job-card-header">
                            <div class="job-icon">T</div>
                            <div class="job-info">
                                <h5 class="job-title">Test2</h5>
                                <p class="job-subtitle">Test2</p>
                            </div>
                        </div>
                        <div class="job-card-body">
                            <p><span>📍</span> Test2</p>
                            <p><span>📅</span> Dec 4th, 2024</p>
                            <p><span>💼</span> Full-Time</p>
                            <p class="job-status pending">Pending</p>
                        </div>
                        <div class="job-card-footer">
                            <button class="btn edit-btn">Edit</button>
                            <button class="btn delete-btn">Delete</button>
                        </div>
                    </div>
                    <div class="job-card">
                        <div class="job-card-header">
                            <div class="job-icon">T</div>
                            <div class="job-info">
                                <h5 class="job-title">Test1</h5>
                                <p class="job-subtitle">Test1</p>
                            </div>
                        </div>
                        <div class="job-card-body">
                            <p><span>📍</span> Test 1</p>
                            <p><span>📅</span> Dec 4th, 2024</p>
                            <p><span>💼</span> Full-Time</p>
                            <p class="job-status pending">Pending</p>
                        </div>
                        <div class="job-card-footer">
                            <button class="btn edit-btn">Edit</button>
                            <button class="btn delete-btn">Delete</button>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>
    <?php include 'include/footer.php'; ?>

    <script src="assets/js/script.js"></script>
    <script>
        function showAlert(event) {
            event.preventDefault();
            alert('Üzerinde çalışılıyor!');
        }
        document.querySelectorAll('.alert-section').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                const sectionName = this.getAttribute('data-section');
                alert(`${sectionName} kısmı üzerinde çalışmalarımız devam ediyor.`);
            });
        });
    </script>
</body>

</html>