<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | TD Motor</title>
    <link rel="icon" href="image/TDicon.png" type="image/x-icon">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Lightbox2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/detail.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .gallery-title {
            text-align: center;
            font-size: 36px;
            margin-top: 30px;
            margin-bottom: 50px;
            color: red;
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card {
            transition: transform 0.3s;
            border: none;
        }

        .card:hover {
            transform: scale(1.05);
        }

        video {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

<?php include_once 'header.php'; ?>
<br><br><br><br>

<div class="container">
    <h1 class="gallery-title">Gallery</h1>
    <div class="row">
        <?php
        // Danh sách ảnh [filename, caption]
        $images = [
            ["BMWHP4", "BMW HP4"],
            ["CBR10th1.png", "Honda CBR1000RR-R 10th Anniversary"],
            ["KawasakiH2", "Kawasaki Ninja H2R"],
            ["DucatiV4", "Ducati Superleggera V4"],
            ["RC213V", "Honda RC213"],
            ["YamahaR1M", "Yamaha R1M"]
        ];

        foreach ($images as $img) {
            $src = "uploads/" . $img[0];
            $caption = $img[1];
            echo "
            <div class='col-lg-4 col-md-6 mb-4'>
                <div class='card'>
                    <a href='$src' data-lightbox='motor-gallery' data-title='$caption'>
                        <img src='$src' class='card-img-top' alt='$caption'>
                    </a>
                    <p class='card-text text-center mt-2'>$caption</p>
                </div>
            </div>";
        }
        ?>
    </div>

    <!-- Video Section -->
    <div class="container my-5 text-center">
        <h2 class="mb-4" style="color: red;">Motorbike Cinematic Video</h2>
        <video controls width="100%" class="shadow rounded">
            <source src="uploads/motorvideo.mp4" type="video/mp4">
        </video>
    </div>
</div>

<!-- Lightbox2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox-plus-jquery.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include_once 'footer.php'; ?>
</body>
</html>
