<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Strathmore Food App - Splash</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <div id="loader-wrapper">
    <div class="loader"></div>
  </div>

  <div class="splash-content">
    <h1>Strathmore Food Application</h1>
  </div>

  <script>
    window.addEventListener('load', () => {
      const loader = document.getElementById('loader-wrapper');
      loader.style.opacity = '0';
      loader.style.transition = 'opacity 0.5s ease';
      setTimeout(() => {
        loader.style.display = 'none';
        window.location.href = 'index.php';
      }, 2000); // Give a brief pause before redirect
    });
  </script>

</body>
</html>
