<?php
/**
 * Contact Form 7 Setup and Configuration Guide
 * 
 * This file contains the recommended configuration for your Contact Form 7
 * Place this content in your CF7 form editor in WordPress admin
 */

// IMPORTANT: This is a reference file - copy the content below into your WordPress admin

/**
 * STEP 1: Contact Form 7 Form Tab Configuration
 * Go to WordPress Admin > Contact > Contact Forms > Edit your form
 * Replace the Form tab content with:
 */
?>

[text* first-name placeholder "First Name"]
[text* last-name placeholder "Last Name"]
[tel* phone placeholder "Phone"]
[email* your-email class:cf7-email placeholder "Email"]
[textarea* your-message placeholder "Your Message"]
[submit "Submit"]

<?php
/**
 * STEP 2: Mail Tab Configuration
 * In the Mail tab, use this configuration:
 */
?>

To: ethanede@gmail.com
From: [first-name] [last-name] <ethanede@gmail.com>
Subject: New Contact Form Submission from [first-name] [last-name]

Message Body:
New contact form submission received:

Name: [first-name] [last-name]
Phone: [phone]
Email: [your-email]

Message:
[your-message]

---
This message was sent from the contact form on your local site.

Additional Headers:
Reply-To: [your-email]

<?php
/**
 * STEP 3: Mail (2) Tab Configuration (Auto-reply to visitor)
 * Check "Use Mail (2)" and configure:
 */
?>

To: [your-email]
From: "Ethan Ede" <ethanede@gmail.com>
Subject: Thank you for contacting me!

Message Body:
Hi [first-name],

Thank you for reaching out! I've received your message and will get back to you within 24 hours.

Here's a copy of what you sent:
[your-message]

I'm looking forward to discussing how I can help you.

Best regards,
Ethan Ede

---
This is an automated response from ethanede.com

<?php
/**
 * STEP 4: Messages Tab Configuration
 * The messages are already customized in functions.php, but you can also set them here
 */

/**
 * STEP 5: Additional Settings Tab
 * Add this to enable additional features:
 */
?>

demo_mode: off
subscribers_only: off
skip_mail: off
html_class: contact-form-ethan
id: contact-form-main

<?php
/**
 * TROUBLESHOOTING TIPS:
 * 
 * 1. Make sure Contact Form 7 plugin is installed and activated
 * 2. Check that your hosting provider allows wp_mail() function
 * 3. Test email functionality by adding this to functions.php temporarily:
 *    add_action('wp_loaded', 'ee_test_email');
 * 4. Check WordPress error logs for email sending issues
 * 5. Consider using an SMTP plugin if default PHP mail() doesn't work
 * 
 * SMTP CONFIGURATION (if needed):
 * Install "WP Mail SMTP" plugin and configure with:
 * - Gmail, SendGrid, Mailgun, or your hosting provider's SMTP
 * 
 * SPAM PROTECTION:
 * - Enable Akismet plugin
 * - Add reCAPTCHA v3 (Contact Form 7 has built-in support)
 * - Use honeypot field (Contact Form 7 extension)
 */

/**
 * TESTING CHECKLIST:
 * 
 * □ Contact Form 7 plugin installed and activated
 * □ Form created with ID "eb95201" (or update the ID in page-home.php)
 * □ Email settings configured in Mail tab
 * □ Auto-reply configured in Mail (2) tab
 * □ Test submission from frontend
 * □ Check if emails are received
 * □ Verify auto-reply is sent to users
 * □ Test form validation (try submitting empty form)
 * □ Test overlay close functionality
 * □ Test responsive design on mobile
 */
?> 