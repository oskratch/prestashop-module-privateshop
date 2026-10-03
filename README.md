# Private Shop

[![PrestaShop](https://img.shields.io/badge/PrestaShop-1.7.x%20%7C%201.8.x-blue)](https://www.prestashop.com/)
[![PHP](https://img.shields.io/badge/PHP-7.2%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0-green.svg)](LICENSE)

PrestaShop module that turns a store into a private shop: customers can only browse and buy once an administrator has approved their account. Useful for B2B, wholesale and members-only stores.

## How it works

### Registration

1. The customer registers with the standard form, which gets an extra DNI field.
2. The account is created but restricted: the customer can't browse or buy yet.
3. The administrator gets an email about the new registration.
4. The administrator approves (or rejects) the customer in the back office.
5. The customer gets an approval email and can use the store.

### Access

- **Visitors who aren't logged in** only see the login, registration and password recovery pages.
- **Customers pending approval** are sent to the login page with a message saying their account is under review.
- **Approved customers** have full access, with optional shipping restrictions.

### Back office

Under **Customers** → **Private Shop** you can:

- See customers waiting for approval
- Approve or reject accounts
- Enable or disable home delivery per customer
- Set the notification emails and the carriers affected by shipping restrictions

## Features

- **Registration with DNI validation** and a honeypot field that blocks bot sign-ups.
- **Emails** to the administrator and the customer at each step of the approval.
- **Shipping control**: home delivery off by default, enabled per customer.
- Works with existing themes; approved customers see the store as usual.

## Installation

1. Download the latest release from GitHub.
2. In the back office, go to **Modules** → **Module Manager** → **Upload a Module** and select the ZIP.
3. Install it and open **Configure**.

## Configuration

1. **Module settings**: notification emails and the carriers used for shipping restrictions.
2. **Customers**: approvals and shipping permissions under **Customers** → **Private Shop**.
3. **Email templates**: editable in **Design** → **Email Themes** if needed.

## Support

Contact `oskratch@gmail.com` or open an issue on GitHub.

## License

GPL-2.0. See [LICENSE](LICENSE).
