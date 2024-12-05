<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<header class="header">
            <div class="container header-content">
                <a class="logo">
                    CWebS
                    <span>🏫</span>
                </a>
                <nav class="nav-links">
                    <button class="theme-toggle" id="themeToggle">🌓</button>
                    <div id="guestNav">
                        <a href="login.php?"><button class="btn btn-primary"  >Login</button></a>
                    </div>
                    <div id="userNav" class="hidden">
                        <button class="btn btn-secondary" data-action="logout">Log Out</button>
                    </div>
                </nav>
            </div>
        </header>
<div id="index">
            <section class="hero">
                <div class="wave-container">
                    <svg class="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill-opacity="1" d="M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,90.7C672,85,768,107,864,122.7C960,139,1056,149,1152,138.7C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                    </svg>
                </div>
                <div class="container">
                    <h1 class="hero-title">Welcome to CWebS</h1>
                    <p class="hero-description">Your simple website builder and content management solution for schools. Create and manage your school website with ease.</p>
                    <div class="hero-buttons">
                        <a href="login.php?"><button class="btn btn-primary"  >Get Started</button></a>
                        <button class="btn btn-secondary" id="learnMoreBtn">Learn More</button>
                    </div>
                </div>
            </section>

            <section class="features">
                <div class="container">
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">🚀</div>
                            <h3>Easy Setup</h3>
                            <p>Get your school website up and running in minutes with our intuitive setup process.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🎨</div>
                            <h3>Customizable Templates</h3>
                            <p>Choose from a variety of professional templates designed specifically for schools.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">📝</div>
                            <h3>Content Management</h3>
                            <p>Easily update and manage your website content with our user-friendly CMS.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <script>
    // JavaScript code to smooth scroll to the features section when button is clicked
    const learnMoreBtn = document.getElementById("learnMoreBtn");
    learnMoreBtn.addEventListener("click", () => {
        document.querySelector(".features").scrollIntoView({ behavior: "smooth" });
    });
</script>
</body>
</html>