<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="An interactive Laravel website template">
    <meta name="author" content="Your Name">
    <title>Interactive Laravel Website</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        header {
            background: #f8f9fa;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .hero {
            background: url('https://via.placeholder.com/1500x500') no-repeat center center/cover;
            height: 500px;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }
        .features {
            padding: 60px 0;
            background: #f8f9fa;
        }
        .feature {
            text-align: center;
            margin-bottom: 30px;
        }
        .feature i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #0d6efd;
        }
        footer {
            background: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        .accordion-button:not(.collapsed) {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">Practice Website</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<section class="hero">
    <h1>Welcome to Your Laravel Website</h1>
    <p>Build amazing web applications faster and better with Laravel.</p>
    <a href="#features" class="btn btn-primary btn-lg">Learn More</a>
</section>

<section id="features" class="features">
    <div class="container">
        <h2 class="text-center mb-5">Features</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-cogs"></i>
                    <h3>Easy Setup</h3>
                    <p>Get started with Laravel quickly using Composer and Artisan CLI commands.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-lock"></i>
                    <h3>Secure</h3>
                    <p>Built-in authentication and CSRF protection for maximum security.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature">
                    <i class="fas fa-rocket"></i>
                    <h3>Scalable</h3>
                    <p>Scale your application effortlessly with Laravel's robust architecture.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="faq" class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Frequently Asked Questions</h2>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        What is Laravel?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        Laravel is a PHP framework designed to make web development easier and faster with an expressive syntax.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        How do I install Laravel?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        You can install Laravel using Composer with the command: <code>composer create-project laravel/laravel example-app</code>.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        What are the system requirements for Laravel?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        Laravel requires PHP 8.0 or higher, Composer, and a database such as MySQL, PostgreSQL, or SQLite.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contact" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Contact Us</h2>
        <form action="#" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Your Name">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Your Email">
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Your Message"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
</section>

<footer>
    <p>&copy; 2025 Laravel Interactive. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
