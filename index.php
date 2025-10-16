<!DOCTYPE html>
<html>
<head>
    <title>Library Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #fff;
            color: #000;
        }

        /* Header */
        header {
            background: #000;
            color: white;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Hero Section */
        .hero {
            position: relative;
            width: 100%;
            height: 800px;
            overflow: hidden;
        }

        .hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
        }

        .hero-content h1 {
            font-size: 42px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.7);
        }

        .hero-content a {
            background: white;
            color: black;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }

        .hero-content a:hover {
            background: #ccc;
        }

        /* Sections */
        section {
            padding: 60px 20px;
        }

        section:nth-child(even) {
            background: #f2f2f2; /* light grey */
        }

        section h2 {
            font-size: 36px;
            text-align: center;
            margin-bottom: 40px;
        }

        /* Carousel */
        .carousel {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            scroll-behavior: smooth;
        }

        .carousel-item {
            min-width: 250px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            text-align: center;
            padding: 20px;
        }

        .carousel-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }

        .carousel-item h3, .carousel-item p {
            color: black;
        }

        /* About Section */
        .about {
            padding: 60px 20px;
            background: #f2f2f2; /* light grey background for contrast */
        }

        .about-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            max-width: 1200px;
            margin: auto;
            flex-wrap: wrap; /* allows stacking on small screens */
        }

        .about-text {
            flex: 1;
            text-align: justify;
            min-width: 280px;
        }

        .about-text h2 {
            font-size: 36px;
            margin-bottom: 20px;
            text-align: left;
        }

        .about-text p {
            font-size: 18px;
            margin-bottom: 20px;
            line-height: 1.6;
            color: #000;
        }

        .about-image {
            flex: 1;
            min-width: 280px;
            text-align: center;
        }

        .about-image img {
            width: 100%;
            max-width: 500px;
            height: auto;
            border-radius: 10px;
        }


        /* Popular Books */
        .books {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .book-item {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            width: 200px;
            padding: 15px;
            text-align: center;
        }

        .book-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }

        .book-item h4, .book-item p {
            color: black;
        }

        /* Events & Services */
        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .card {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 250px;
            text-align: center;
        }

        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }


        /* Footer */
        footer {
            background: #000;
            color: white;
            padding: 40px 30px 20px 30px;
        }

        .footer-container {
            display: flex;
            justify-content: space-between; /* keeps left on left, right on right */
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .footer-left, .footer-right {
            flex: 1; /* allows them to take up available space */
        }

        .footer-left p {
            margin: 20px 0;
            margin-left: 80px;
        }

        .footer-right {
            text-align: right; /* pushes content to the very right */
            margin-right: 80px;
        }

        .footer-right a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 20px 0;
        }

        .footer-right a:hover {
            text-decoration: underline;
            color: #ccc;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.3);
            padding-top: 10px;
            font-size: 14px;
        }

    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="logo">Library</div>
        <nav>
            <a href="#">Home</a>
            <a href="#new-arrivals">Catalog</a>
            <a href="#events">Events</a>
            <a href="#services">Services</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <div class="hero">
        <img src="./images/library-hero.jpg" alt="Library Hero">
        <div class="hero-content">
            <h1>Welcome to the Library</h1>
            <a href="login.php">Login to Your Account</a>
        </div>
    </div>

    <!-- New Arrivals Carousel -->
    <section id="new-arrivals">
        <h2>New Arrivals</h2>
        <div class="carousel">
            <div class="carousel-item">
                <img src="./images/book1.jpeg" alt="Book 1">
                <h3>River Sing Me Home</h3>
                <p>Eleanor Shearer</p>
            </div>
            <div class="carousel-item">
                <img src="./images/book2.png" alt="Book 2">
                <h3>The Beauty Within</h3>
                <p>Samantha Donald</p>
            </div>
            <div class="carousel-item">
                <img src="./images/book3.jpeg" alt="Book 3">
                <h3>Tales Under a Purple Sky</h3>
                <p>Namika Hadid</p>
            </div>
            <div class="carousel-item">
                <img src="./images/book4.jpeg" alt="Book 3">
                <h3>Soul</h3>
                <p>Olivia Wilson</p>
            </div>
            <div class="carousel-item">
                <img src="./images/book5.jpeg" alt="Book 3">
                <h3>Memory</h3>
                <p>Angelina Aludo</p>
            </div>
            <div class="carousel-item">
                <img src="./images/book6.png" alt="Book 3">
                <h3>Echos of Tomorrow</h3>
                <p>Jane Doe</p>
            </div>
            <div class="carousel-item">
                <img src="./images/book7.jpeg" alt="Book 3">
                <h3>Truly Sadly</h3>
                <p>Betsy English</p>
            </div>
        </div>
    </section>

    <!-- About Us -->
    <section class="about">
        <div class="about-container">
            <div class="about-text">
                <h2>About Us</h2>
                <p>Our library is dedicated to fostering a love for reading and learning in the community. We offer a wide variety of books across genres, including fiction, non-fiction, academic texts, and rare collections, catering to readers of all ages and interests. Our aim is to create a welcoming environment where knowledge and creativity can thrive.</p>
                <p>Beyond our extensive collection, we provide access to digital resources such as e-books, audiobooks, and online journals, ensuring that learning is accessible anytime and anywhere. We also organize regular events, workshops, and reading programs to engage and inspire our members, making the library a hub of education, culture, and community interaction.</p>
            </div>
            <div class="about-image">
                <img src="./images/library-about.png" alt="Library Image">
            </div>
        </div>
    </section>

    <!-- Popular Books of the Week -->
    <section>
        <h2>Most Popular Books of the Week</h2>
        <div class="books">
            <div class="book-item">
                <img src="./images/popular1.jpg" alt="Popular Book 1">
                <h4>Harry Potter and The Prisoner of Azkaban</h4>
                <p>J. K. Rowling</p>
            </div>
            <div class="book-item">
                <img src="./images/popular2.jpg" alt="Popular Book 2">
                <h4>The Alchemist</h4>
                <p>Paulo Coelho</p>
            </div>
            <div class="book-item">
                <img src="./images/popular3.jpg" alt="Popular Book 3">
                <h4>The Fault In Our Stars</h4>
                <p>John Green</p>
            </div>
            <div class="book-item">
                <img src="./images/popular4.jpg" alt="Popular Book 3">
                <h4>The Little Prince</h4>
                <p>Antoine de Saint-Exupéry</p>
            </div>
            <div class="book-item">
                <img src="./images/popular5.jpg" alt="Popular Book 3">
                <h4>The Let Them Theory</h4>
                <p>Mel Robbins</p>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events">
        <h2>Upcoming Events</h2>
        <div class="cards">
            <div class="card">
                <img src="images/storytelling.jpg" alt="Storytelling for Kids">
                <h3>Storytelling for Kids</h3>
                <p>Date: 20th Oct</p>
            </div>
            <div class="card">
                <img src="images/author_meet.jpg" alt="Author Meet & Greet">
                <h3>Author Meet & Greet</h3>
                <p>Date: 25th Oct</p>
            </div>
            <div class="card">
                <img src="images/book_club.jpeg" alt="Book Club Discussion">
                <h3>Book Club Discussion</h3>
                <p>Date: 30th Oct</p>
            </div>
        </div>
    </section>      

    <!-- Services Section -->
    <section id="services">
        <h2>Our Services</h2>
        <div class="cards">
            <div class="card">
                <h3>Borrow Books</h3>
                <p>Borrow books with your membership and return at your convenience.</p>
            </div>
            <div class="card">
                <h3>Digital Library</h3>
                <p>Access e-books and audiobooks from our online portal.</p>
            </div>
            <div class="card">
                <h3>Study Rooms</h3>
                <p>Reserve private study rooms for focused learning sessions.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <p><strong>Library Address:</strong></p>
                <p>123 Library Street</p>
                <p>City, State, ZIP</p>
                <p>Email: info@library.com</p>
                <p>Phone: +1 234 567 890</p>
            </div>
            <div class="footer-right">
                <a href="#">Home</a>
                <a href="#new-arrivals">Catalog</a>
                <a href="#events">Events</a>
                <a href="#services">Services</a>
                <a href="login.php">Login</a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 Library Management System
        </div>
    </footer>

</body>
</html>
