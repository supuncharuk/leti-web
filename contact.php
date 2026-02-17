<?php
$pageTitle = "Contact Us";
$currentPage = "contact";
include 'includes/header.php';
?>

<?php 
$breadcrumbActive = "Contact";
include 'includes/breadcrumb.php'; 
?>

<div class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <h2 class="mb-4">Get in Touch</h2>
                <p>Have questions about our courses or admissions? Send us a message or visit us.</p>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-light p-3 rounded me-3 text-primary-custom">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                    <div>
                        <h5>Our Location</h5>
                        <p class="text-muted">Light Engineering Training Institute,<br>Ahangama</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-light p-3 rounded me-3 text-primary-custom">
                        <i class="fas fa-phone fa-2x"></i>
                    </div>
                    <div>
                        <h5>Phone Number</h5>
                        <p class="text-muted">+94 91 228 1202</p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="bg-light p-3 rounded me-3 text-primary-custom">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                    <div>
                        <h5>Email Address</h5>
                        <p class="text-muted">lightengineering612@gmail.com</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="bg-light p-4 rounded shadow-sm">
                    <h3 class="mb-4">Send us a Message</h3>
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" placeholder="John Doe">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" placeholder="john@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" placeholder="Inquiry about...">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5"
                                placeholder="Your message here..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="rounded overflow-hidden shadow-sm" style="height: 400px;">
                    <!-- Google Map Placeholder -->
                    <!-- <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15873.666986567!2d80.3666667!3d6.0000000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae16f6b5b5b5b5b%3A0x5b5b5b5b5b5b5b5b!2sAhangama%2C%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1620000000000!5m2!1sen!2slk"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe> -->

                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3968.1297016990893!2d80.35551939999999!3d5.9768719!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae113b9825ac903%3A0x4f3681f31dadeede!2sLETI%20Ahangama!5e0!3m2!1sen!2slk!4v1771347766383!5m2!1sen!2slk"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>