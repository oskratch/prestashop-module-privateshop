# Private Shop - Store Access Control
 
[![PrestaShop](https://img.shields.io/badge/PrestaShop-1.7.x%20%7C%201.8.x-blue)](https://www.prestashop.com/)
[![PHP](https://img.shields.io/badge/PHP-7.2%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0-green.svg)](LICENSE)

## Description
Transform your PrestaShop store into a private, invitation-only shop with complete access control. This module is perfect for B2B stores, exclusive retailers, or any business requiring customer pre-approval before allowing purchases.

## How It Works

### Customer Registration Flow
1. **Customer registers** through the standard registration form with additional DNI field
2. **Account created but restricted** - customer cannot browse or purchase yet
3. **Admin receives notification** email about the new registration
4. **Admin reviews and approves** the customer in the backoffice
5. **Customer receives approval email** and can now access the full store

### Access Control
- **Unapproved customers:** Redirected to login page with approval pending message
- **Non-logged users:** Can only access login, registration, and password recovery pages
- **Approved customers:** Full store access with optional shipping restrictions

### Admin Management
Navigate to **Customers > Private Shop** in your backoffice to:
- View all pending customer approvals
- Approve or reject customer accounts
- Manage individual shipping permissions (enable/disable home delivery per customer)
- Configure notification emails and carrier restrictions

## Key Features
- **Secure Registration:** Enhanced registration form with DNI validation and anti-spam protection
- **Email Notifications:** Automated emails to admins and customers during the approval process
- **Flexible Shipping Control:** Disable home delivery by default, enable per customer as needed
- **Anti-Spam Protection:** Built-in honeypot system blocks automated bot registrations
- **Seamless Integration:** Works with existing PrestaShop themes and doesn't affect approved customer experience

## Installation
1. Download the latest release from GitHub
2. In your PrestaShop admin, go to **Modules > Module Manager**
3. Click **Upload a Module** and select the downloaded ZIP file
4. Install and configure the module through the **Configure** button

## Configuration
1. **Module Settings:** Configure admin notification emails and select carriers for shipping restrictions
2. **Customer Management:** Use **Customers > Private Shop** to manage approvals and permissions
3. **Email Templates:** Customize notification emails in **Design > Email Themes** if needed

## Perfect For
- **B2B Stores:** Require business verification before allowing access
- **Exclusive Retailers:** Create VIP or member-only shopping experiences  
- **Wholesale Businesses:** Control who can see products and pricing
- **Regional Stores:** Restrict access based on location or delivery areas

## Support
For technical support or questions, contact `oskratch@gmail.com` or create an issue on the GitHub repository.

## License
Licensed under GPL-2.0. See [LICENSE](LICENSE) file for details.

