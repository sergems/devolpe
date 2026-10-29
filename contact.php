<?php
session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = '';
$error_message = '';

if ($_POST && isset($_POST['submit'])) {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = 'Security token mismatch. Please try again.';
    } else {
        // Honeypot spam protection
        if (!empty($_POST['website'])) {
            $error_message = 'Spam detected. Your submission was blocked.';
        } else {
            // Enhanced input validation and sanitization
            $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
            $event_date = filter_var(trim($_POST['event_date']), FILTER_SANITIZE_STRING);
            $guest_count = filter_var(trim($_POST['guest_count']), FILTER_SANITIZE_STRING);
            $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
            
            // Comprehensive validation
            if (empty($name) || strlen($name) < 2 || strlen($name) > 50) {
                $error_message = 'Please enter a valid name (2-50 characters).';
            } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = 'Please enter a valid email address.';
            } elseif (empty($message) || strlen($message) < 10 || strlen($message) > 1000) {
                $error_message = 'Please enter a message between 10-1000 characters.';
            } elseif (!empty($phone) && !preg_match('/^[\+]?[0-9\-\(\)\s]{7,15}$/', $phone)) {
                $error_message = 'Please enter a valid phone number.';
            } else {
                // Prevent email header injection
                $clean_email = str_replace(["\r", "\n", "\0"], '', $email);
                $clean_name = str_replace(["\r", "\n", "\0"], '', $name);
                $clean_phone = str_replace(["\r", "\n", "\0"], '', $phone);
                $clean_event_date = str_replace(["\r", "\n", "\0"], '', $event_date);
                $clean_guest_count = str_replace(["\r", "\n", "\0"], '', $guest_count);
                $clean_message = str_replace(["\r", "\n", "\0"], '', $message);
                
                $to = "info@devolpe.co.za";
                $subject = "Wedding Inquiry from DE VOLPE Website - " . substr($clean_name, 0, 30);
                $email_content = sprintf(
                    "New wedding inquiry from DE VOLPE website:\n\n" .
                    "Name: %s\n" .
                    "Email: %s\n" .
                    "Phone: %s\n" .
                    "Event Date: %s\n" .
                    "Expected Guest Count: %s\n\n" .
                    "Message:\n%s\n\n" .
                    "Sent from DE VOLPE website contact form.\n" .
                    "IP Address: %s\n" .
                    "User Agent: %s\n" .
                    "Timestamp: %s",
                    $clean_name, $clean_email, $clean_phone, $clean_event_date, $clean_guest_count, $clean_message,
                    $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                    substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 100),
                    date('Y-m-d H:i:s')
                );
                
                // Secure headers with proper formatting
                $headers = "From: DE VOLPE Contact Form <noreply@devolpe.co.za>\r\n";
                $headers .= "Reply-To: " . $clean_name . " <" . $clean_email . ">\r\n";
                $headers .= "Return-Path: info@devolpe.co.za\r\n";
                $headers .= "X-Mailer: DE VOLPE Contact Form\r\n";
                $headers .= "X-Priority: 1\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                
                // Attempt to send email
                $mail_sent = @mail($to, $subject, $email_content, $headers);
                
                if ($mail_sent) {
                    $success_message = 'Thank you for your inquiry! We will contact you within 24 hours.';
                    // Clear form data on success
                    $_POST = array();
                    // Regenerate CSRF token for security
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                } else {
                    error_log("DE VOLPE Contact Form Error: Failed to send email for " . $clean_email . " - Timestamp: " . date('Y-m-d H:i:s'));
                    $error_message = 'Sorry, there was an error sending your message. Please call us directly at +27 64 820 0293 or email us at info@devolpe.co.za.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - DE VOLPE Wedding & Events | Book Your Dream Wedding</title>
    <meta name="description" content="Contact DE VOLPE wedding venue in eMondlo, KwaZulu-Natal. Schedule a visit, get quotes, and plan your perfect wedding celebration. Call +27 64 820 0293.">
    <meta name="keywords" content="contact DE VOLPE, wedding venue inquiry, book wedding venue, KwaZulu-Natal wedding, wedding quote, venue visit, event booking">
    <meta name="author" content="Serge Mugisho">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.devolpe.co.za/contact.php">
    <meta property="og:title" content="Contact Us - DE VOLPE Wedding & Events">
    <meta property="og:description" content="Contact DE VOLPE wedding venue in eMondlo, KwaZulu-Natal. Schedule a visit, get quotes, and plan your perfect wedding celebration.">
    <meta property="og:image" content="https://www.devolpe.co.za/assets/images/ceremony_wedding_vows.jpg">
    <meta property="og:site_name" content="DE VOLPE Wedding & Events">
    <meta property="og:locale" content="en_ZA">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://www.devolpe.co.za/contact.php">
    <meta name="twitter:title" content="Contact Us - DE VOLPE Wedding & Events">
    <meta name="twitter:description" content="Contact DE VOLPE wedding venue in eMondlo, KwaZulu-Natal. Schedule a visit, get quotes, and plan your perfect wedding celebration.">
    <meta name="twitter:image" content="https://www.devolpe.co.za/assets/images/ceremony_wedding_vows.jpg">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://www.devolpe.co.za/contact.php">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/devolpe-logo.png">
    
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "name": "Contact DE VOLPE Wedding & Events",
        "description": "Contact DE VOLPE wedding venue in eMondlo, KwaZulu-Natal. Schedule a visit, get quotes, and plan your perfect wedding celebration.",
        "url": "https://www.devolpe.co.za/contact.php",
        "mainEntity": {
            "@type": "WeddingVenue",
            "name": "DE VOLPE Wedding & Events",
            "telephone": "+27648200293",
            "email": "info@devolpe.co.za",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "R69 Mondlo/Vryheid Road",
                "addressLocality": "eMondlo",
                "addressRegion": "KwaZulu-Natal",
                "postalCode": "3105",
                "addressCountry": "ZA"
            }
        }
    }
    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <img src="assets/images/devolpe-logo.png" alt="DE VOLPE Wedding & Events" class="logo">
            <ul class="nav-menu" id="nav-menu">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="about.php" class="nav-link">About Us</a></li>
                <li><a href="https://thespaceguesthouse.co.za" class="nav-link" target="_blank" rel="noopener">Book Accommodation</a></li>
                <li><a href="contact.php" class="nav-link active">Contact Us</a></li>
            </ul>
            <div class="menu-toggle" id="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <div class="breadcrumb-container">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item">
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" style="background: linear-gradient(rgba(26, 26, 26, 0.6), rgba(26, 26, 26, 0.6)), url('assets/images/ceremony_wedding_vows.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="hero-content">
            <h1 class="fade-in-up">Contact DE VOLPE</h1>
            <p class="subtitle fade-in-up">Let's Plan Your Perfect Day</p>
            <p class="fade-in-up">Ready to begin your wedding journey? We're here to help make your dreams come true</p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Get In Touch</h2>
                <p>We're excited to hear about your wedding plans and discuss how DE VOLPE can make your day extraordinary</p>
            </div>
            <div class="grid grid-2">
                <div class="contact-info fade-in-up">
                    <h3 style="margin-bottom: 2rem; color: var(--gold-primary);">Contact Information</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4>Address</h4>
                            <p>R69 Mondlo/Vryheid Road<br>eMondlo 3105<br>KwaZulu-Natal, South Africa</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4>Phone</h4>
                            <p><a href="tel:+27648200293" style="color: var(--grey-dark); text-decoration: none;">+27 64 820 0293</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4>Email</h4>
                            <p><a href="mailto:info@devolpe.co.za" style="color: var(--grey-dark); text-decoration: none;">info@devolpe.co.za</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h4>Office Hours</h4>
                            <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 4:00 PM<br>Sunday: By appointment only</p>
                        </div>
                    </div>
                    
                    <!-- Social Media -->
                    <div style="margin-top: 2rem;">
                        <h4 style="margin-bottom: 1rem;">Follow Us</h4>
                        <div class="social-links" style="justify-content: flex-start;">
                            <a href="#" class="social-link" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://wa.me/27648200293" class="social-link" title="WhatsApp" target="_blank">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="card fade-in-up">
                    <div class="card-content">
                        <h3 style="color: var(--gold-primary); margin-bottom: 1.5rem;">Send Us a Message</h3>
                        
                        <?php
                        if ($success_message) {
                            echo '<div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
                                    <i class="fas fa-check-circle"></i> ' . htmlspecialchars($success_message) . '
                                  </div>';
                        }
                        
                        if ($error_message) {
                            echo '<div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
                                    <i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($error_message) . '
                                  </div>';
                        }
                        ?>
                        
                        <form method="POST" action="contact.php" id="contact-form">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            
                            <!-- Honeypot field (hidden from users) -->
                            <div style="position: absolute; left: -9999px; visibility: hidden; display: none;">
                                <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($_POST['name']) && $error_message ? htmlspecialchars($_POST['name']) : ''; ?>" required maxlength="50">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) && $error_message ? htmlspecialchars($_POST['email']) : ''; ?>" required maxlength="100">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) && $error_message ? htmlspecialchars($_POST['phone']) : ''; ?>" maxlength="15">
                            </div>
                            
                            <div class="form-group">
                                <label for="event_date">Preferred Event Date</label>
                                <input type="date" id="event_date" name="event_date" class="form-control" value="<?php echo isset($_POST['event_date']) && $error_message ? htmlspecialchars($_POST['event_date']) : ''; ?>" min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="guest_count">Expected Number of Guests</label>
                                <select id="guest_count" name="guest_count" class="form-control">
                                    <option value="">Select guest count</option>
                                    <option value="1-50" <?php echo (isset($_POST['guest_count']) && $_POST['guest_count'] == '1-50') ? 'selected' : ''; ?>>1-50 guests</option>
                                    <option value="51-100" <?php echo (isset($_POST['guest_count']) && $_POST['guest_count'] == '51-100') ? 'selected' : ''; ?>>51-100 guests</option>
                                    <option value="101-150" <?php echo (isset($_POST['guest_count']) && $_POST['guest_count'] == '101-150') ? 'selected' : ''; ?>>101-150 guests</option>
                                    <option value="151-200" <?php echo (isset($_POST['guest_count']) && $_POST['guest_count'] == '151-200') ? 'selected' : ''; ?>>151-200 guests</option>
                                    <option value="200+" <?php echo (isset($_POST['guest_count']) && $_POST['guest_count'] == '200+') ? 'selected' : ''; ?>>200+ guests</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" name="message" class="form-control" rows="5" placeholder="Tell us about your dream wedding, special requirements, or any questions you have..." required maxlength="1000"><?php echo isset($_POST['message']) && $error_message ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="section" style="background-color: var(--cream);">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Find Us</h2>
                <p>Located in the beautiful eMondlo area along the R69 Mondlo/Vryheid Road</p>
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <!-- Google Maps Embed -->
                <div style="border-radius: 10px; overflow: hidden; box-shadow: 0 5px 20px var(--shadow-medium);">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.2!2d30.8!3d-27.8!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2seMondlo%2C%20South%20Africa!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s"
                        width="100%" 
                        height="400" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <p style="margin-top: 1rem; color: var(--grey-dark);">
                    <i class="fas fa-car"></i> Ample parking available on-site<br>
                    <i class="fas fa-road"></i> Easy access from R69 Mondlo/Vryheid Road
                </p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section" style="background: linear-gradient(rgba(212, 175, 55, 0.1), rgba(212, 175, 55, 0.1));">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Schedule Your Venue Tour</h2>
                <p>See the beauty of DE VOLPE in person and envision your perfect wedding day</p>
                <div style="margin-top: 2rem;">
                    <a href="tel:+27648200293" class="btn btn-primary" style="margin-right: 1rem;">Call Now</a>
                    <a href="mailto:info@devolpe.co.za" class="btn btn-secondary">Email Us</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <img src="assets/images/devolpe-logo.png" alt="DE VOLPE" class="footer-logo">
                <p><strong>DE VOLPE Wedding & Events</strong></p>
                <p>R69 Mondlo/Vryheid Road, eMondlo 3105, KwaZulu-Natal</p>
                <p>Email: info@devolpe.co.za | Tel: +27 64 820 0293</p>
                
                <!-- Social Media Links -->
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/27648200293" class="social-link" title="WhatsApp" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="mailto:info@devolpe.co.za" class="social-link" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> DE VOLPE Wedding & Events. All rights reserved. | Designed with love with <a href="https://watchusworkafrica.co.za" target="_blank" style="color: inherit; text-decoration: underline;">watchusworkafrica.co.za</a></p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
</body>
</html>