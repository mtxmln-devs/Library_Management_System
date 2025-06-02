<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Library Management System</title>
  <link rel="icon" href="logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }

    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
    }

    #particles-js {
      position: absolute;
      width: 100%;
      height: 100%;
      background-color: #3b0a50;
      z-index: -1;
    }

    .welcome-container {
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      position: relative;
      z-index: 1;
    }

    .logo {
      width: 100px;
      height: 100px;
      margin-bottom: 20px;
      animation: bounce 2s infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    h1 {
      font-size: 42px;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .typed-text {
      font-size: 24px;
      color: #e1bee7;
      min-height: 30px;
      font-weight: 400;
    }

    .get-started-btn {
      margin-top: 30px;
      padding: 15px 35px;
      font-size: 18px;
      font-weight: 600;
      background-color: white;
      color: #3b0a50;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }

    .get-started-btn:hover {
      background-color: #f3e5f5;
      transform: scale(1.08);
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
    }
  </style>
</head>
<body>
  <div id="particles-js"></div>

  <div class="welcome-container">
    <img src="logo.png" alt="Library Logo" class="logo" />
    <h1>Library Management System</h1>
    <div class="typed-text" id="typed-text"></div>
    <a href="signup.php" class="get-started-btn">Get Started</a>
  </div>

  <audio autoplay loop>
    <source src="background.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
  </audio>

  <!-- Typed.js -->
  <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
  <script>
    const typed = new Typed('#typed-text', {
      strings: [
        'Manage your books with ease.',
        'Track borrowing and reservations.',
        'A smarter way to read.'
      ],
      typeSpeed: 50,
      backSpeed: 30,
      backDelay: 2000,
      loop: true
    });
  </script>

  <!-- Particles.js -->
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
  <script>
    particlesJS("particles-js", {
      "particles": {
        "number": { "value": 60, "density": { "enable": true, "value_area": 800 }},
        "color": { "value": "#ffffff" },
        "shape": {
          "type": "circle",
          "stroke": { "width": 0, "color": "#000000" },
          "polygon": { "nb_sides": 5 }
        },
        "opacity": { "value": 0.5 },
        "size": { "value": 3, "random": true },
        "line_linked": {
          "enable": true,
          "distance": 150,
          "color": "#ffffff",
          "opacity": 0.4,
          "width": 1
        },
        "move": {
          "enable": true,
          "speed": 2,
          "direction": "none",
          "random": false,
          "straight": false,
          "out_mode": "out"
        }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": { "enable": true, "mode": "repulse" },
          "onclick": { "enable": true, "mode": "push" },
          "resize": true
        },
        "modes": {
          "repulse": { "distance": 100 },
          "push": { "particles_nb": 4 }
        }
      },
      "retina_detect": true
    });
  </script>
</body>
</html>
