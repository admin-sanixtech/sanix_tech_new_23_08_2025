<?php
// email-config.php
// Email configuration settings

return [
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls', // or 'ssl'
        'username' => 'info@sanixtech.in',
        'password' => 'your-app-password', // Use App Password for Gmail
        'from_email' => 'info@sanixtech.in',
        'from_name' => 'Sanix Technology',
    ],
    
    'verification' => [
        'expiry_hours' => 24,
        'max_attempts_per_hour' => 3,
        'pin_length' => 6,
    ],
    
    'site' => [
        'name' => 'Sanix Technology',
        'url' => 'https://yourdomain.com', // Replace with your actual domain
        'support_email' => 'support@sanixtech.in',
    ]
];

/*
GMAIL SETUP INSTRUCTIONS:
1. Enable 2-Factor Authentication on your Gmail account
2. Go to Google Account settings > Security > App passwords
3. Generate an app password for "Mail"
4. Use this app password in the 'password' field above (not your regular Gmail password)
5. Replace 'your-app-password' with the generated app password

ALTERNATIVE SMTP SERVICES:
- SendGrid: smtp.sendgrid.net (Port: 587)
- Mailgun: smtp.mailgun.org (Port: 587)  
- Amazon SES: email-smtp.region.amazonaws.com (Port: 587)
- Mailtrap (for testing): smtp.mailtrap.io (Port: 587)

For production, consider using a dedicated email service like SendGrid or Mailgun
for better deliverability and reliability.
*/
?>