# Lola's Kusina - Customer Web Module

A web-based food ordering and menu customization system for Lola's Kusina using PHP MVC architecture and Tailwind CSS.

## Features

- **Package Browsing**: View pre-made meal packages with detailed information
- **Custom Package Builder**: Create your own custom packages by selecting items
- **Shopping Cart**: Add items to cart and manage orders
- **Order Management**: Place orders with delivery details and payment options
- **Responsive Design**: Mobile-first design using Tailwind CSS

## Project Structure

```
Lola'sKusina/
├── config/
│   ├── database.php       # Database configuration
│   └── schema.sql         # Database schema
├── controllers/
│   ├── PackageController.php
│   ├── MenuController.php
│   └── OrderController.php
├── models/
│   ├── Package.php
│   ├── MenuItem.php
│   └── Order.php
├── views/
│   ├── layouts/
│   │   ├── header.php     # Header with navigation
│   │   └── footer.php     # Footer with bottom nav
│   ├── index.php          # Home page (packages list)
│   ├── order_details.php  # Package details page
│   ├── build_package.php  # Custom package builder
│   └── cart.php           # Shopping cart & checkout
└── public/
    ├── css/
    ├── js/
    ├── images/
    └── index.php          # Entry point / Router
```

## Setup Instructions

### 1. Database Setup

1. Create a MySQL database named `lolas_kusina`
2. Import the schema:
```bash
mysql -u root -p lolas_kusina < config/schema.sql
```

3. Update database credentials in `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'lolas_kusina');
```

### 2. Web Server Setup

#### Using PHP Built-in Server (Development)

```bash
cd public
php -S localhost:8000
```

Then visit: `http://localhost:8000`

#### Using XAMPP/WAMP

1. Copy the project to `htdocs` or `www` folder
2. Access via: `http://localhost/Lola'sKusina/public/`

#### Using Apache (Production)

Create a `.htaccess` file in the `public` folder:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### 3. Image Assets

Add your food images to `public/images/` folder. Required images:
- logo.png
- paborito-package.jpg
- family-fiesta.jpg
- salo-salo.jpg
- pancit-canton.jpg
- lumpia.jpg
- lechon-kawali.jpg
- chicken-inasal.jpg
- kare-kare.jpg
- pata.jpg
- caldereta.jpg
- rice.jpg
- garlic-rice.jpg
- halo-halo.jpg
- buko-pandan.jpg
- leche-flan.jpg

## Pages

### 1. Home Page (`views/index.php`)
- Displays all available packages
- Category filtering (All Packages, Fiesta Trays, Desserts)
- Best sellers section
- Build your own package button

### 2. Order Details (`views/order_details.php`)
- Detailed view of a specific package
- Package contents with images
- Quantity selector
- Order now button
- Rating and reviews

### 3. Build Package (`views/build_package.php`)
- Step-by-step package customization
- Select package size (6-8, 10-12, 15-20 pax)
- Choose main dishes, side dishes, and desserts
- Real-time price calculation
- Progress indicator

### 4. Cart & Checkout (`views/cart.php`)
- View cart items
- Update quantities
- Delivery details form
- Payment method selection
- Order summary with totals
- Place order button

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, Tailwind CSS
- **Architecture**: MVC (Model-View-Controller)
- **Session Management**: PHP Sessions

## Key Features

### MVC Architecture
- **Models**: Handle database operations
- **Views**: Display data to users (PHP + Tailwind CSS)
- **Controllers**: Handle business logic and route requests

### Tailwind CSS Integration
- Responsive mobile-first design
- Custom color scheme matching Lola's Kusina branding
- Modern UI components with utility classes
- No custom CSS needed

### Session Management
- Shopping cart stored in PHP sessions
- Guest checkout supported
- Order tracking

## Future Enhancements

- User authentication and registration
- Order history for logged-in users
- Real-time order tracking
- Payment gateway integration (GCash, PayMaya)
- Admin panel for managing packages and orders
- Customer reviews and ratings system
- Email notifications
- SMS notifications for order updates

## Support

For questions or issues, please contact the development team.

## License

Proprietary - Lola's Kusina © 2026
