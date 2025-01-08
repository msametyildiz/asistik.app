<?php include 'include/session_handler.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASİSTİK</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
    <!--<script src="assets/js/disable_keys.js"></script>-->
    </head>

<body>
    <div id="root">
    <?php include 'include/sidebar.php'; ?>

        <main class="dashboard">
        <?php include 'include/navbar.php'; ?>


            <section class="profile-dashboard">
                <h4 class="profile-title">Profile</h4>
                <form class="profile-form">
                    <div class="form-group">
                        <div class="form-field">
                            <label for="image">Upload Image</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name">
                        </div>
                        <div class="form-field">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" placeholder="Enter your last name">
                        </div>
                        <div class="form-field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email">
                        </div>
                        <div class="form-field">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" placeholder="Enter your location">
                        </div>
                        <div class="form-field">
                            <button type="submit" class="submit-btn" style="background-color: #17a2b8;">Submit</button>
                        </div>
                    </div>
                </form>
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