# Private Shop - Store Access Control

**Version:** 1.0.0  
**Compatible with PrestaShop:** 1.6.x, 1.7.x, and 1.8.x  
**Author:** Oscar Periche (4funkies)  
**License:** GPLv2 or later  

## Description
The Private Shop module enhances your PrestaShop store's security by restricting access to only pre-approved customers. This is ideal for B2B stores, exclusive shops, or any scenario where manual customer validation is required. In addition to access control, it includes anti-spam measures and flexible shipping options.

## Requirements
- PrestaShop 1.6.x, 1.7.x, or 1.8.x
- PHP 5.6 or higher
- MySQL 5.0 or higher

## Main Features
- **Customer Registration:** Allows customers to register with additional fields like DNI.
- **Admin Notifications:** Admins receive emails for new registrations.
- **Approval Workflow:** Admins can approve or reject customers via the backoffice.
- **Customer Notifications:** Approved customers get confirmation emails.
- **Access Restrictions:** Unapproved users are limited to login, registration, and password recovery pages.
- **Anti-Spam Honeypot:** Hidden form fields detect and block bots during registration.
- **Shipping Management:** Home delivery can be disabled by default and enabled per customer.

### How the Honeypot Works
The honeypot uses invisible form fields that humans ignore but bots fill automatically. If a field is completed or the form is submitted too quickly (< 2 seconds), the registration is blocked with a generic error message to avoid alerting spammers.

## Installation
1. Download or clone the module repository.
2. Compress the `privateshop/` folder into a `.zip` file.
3. Log in to your PrestaShop admin panel.
4. Navigate to **Modules > Module Manager > Upload a Module**.
5. Upload the `.zip` file and click **Install**.
6. Activate the module if not done automatically.

## Configuration
1. Go to **Modules > Module Manager** and find "Private Shop".
2. Click **Configure** to set admin notification emails and carrier restrictions.
3. In the backoffice, under **Customers > Private Shop**, manage customer approvals and shipping permissions.

## Uninstallation
1. Go to **Modules > Module Manager**.
2. Find "Private Shop" and click **Uninstall**.
3. Confirm; note that customer data in `privateshop_customers` is preserved.

## Troubleshooting
- **Honeypot not working:** Ensure the module is reset after updates.
- **Emails not sending:** Check PrestaShop email settings.
- **Access issues:** Verify user approval status in the admin panel.
- **500 Errors:** Clear PrestaShop cache and check server logs.

## Changelog
- **v1.0.0:** Initial release with access control, approvals, and honeypot anti-spam.

## Support
For issues or questions, contact `oskratch@gmail.com` or open an issue on GitHub.

## License
This module is licensed under the GPLv2 or later. See [LICENSE](LICENSE) for details.

