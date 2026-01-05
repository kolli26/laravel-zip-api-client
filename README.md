# Laravel Zip API Client Frontend

A modern Laravel frontend application for consuming a REST API that manages counties and cities with postal codes. Built with Laravel Breeze for authentication and Tailwind CSS for styling.

## Features

- **User Authentication**: Secure login and registration using Laravel Breeze
- **County Management**: 
  - View all counties
  - Create, edit, and delete counties (authenticated users only)
  - Search counties by name
- **City Management**:
  - View all cities
  - Create, edit, and delete cities (authenticated users only)
  - Alphabetical filtering: Select a county, then filter cities by their first letter
  - Display cities with their county and postal code
- **Data Export**:
  - Export counties and cities to CSV
  - Export counties and cities to PDF with formatted headers and footers
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js and npm
- A running REST API server at `http://localhost:8000/api` (or configure via `.env`)

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd laravel-zip-api-client
git checkout develop
```

### 2. Install Dependencies
```bash
composer install
npm install
npm run build
```

### 3. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Update the `.env` file:
- Set `API_URL=http://localhost:8000/api` (or your API server address)
- Configure session driver: `SESSION_DRIVER=database` (recommended)
- Configure database: `DB_CONNECTION=sqlite` (uses SQLite by default)

### 4. Database Setup
```bash
php artisan migrate
```

### 5. Start the Application
```bash
php artisan serve
npm run dev  # In another terminal for asset compilation
```

Visit `http://localhost:8000` in your browser.

## Usage

### Registration and Login
1. Navigate to the Registration page
2. Create an account with your email and password
3. Login with your credentials
4. Access the dashboard and data management features

### Managing Counties
- **View**: Navigate to "Megyék" (Counties) in the menu
- **Create**: Click "Új megye" (New County) button
- **Edit**: Click "Szerkesztés" (Edit) on any county
- **Delete**: Click "Törlés" (Delete) on any county
- **Search**: Use the search field to filter by name
- **Export**: Use CSV or PDF export buttons (authenticated users only)

### Managing Cities
- **View**: Navigate to "Városok" (Cities) in the menu
- **Filter by Letter**: 
  1. Select a county from the dropdown
  2. Click on the desired first letter to see cities starting with that letter
- **Create**: Click "Új város" (New City) button and select county, name, and postal code
- **Edit**: Click "Szerkesztés" (Edit) on any city
- **Delete**: Click "Törlés" (Delete) on any city
- **Export**: Use CSV or PDF export buttons (authenticated users only)

## API Endpoints Expected

The application expects the following API endpoints:

### Counties
- `GET /api/counties` - List all counties
- `GET /api/counties?needle=search` - Search counties
- `GET /api/counties/{id}` - Get single county
- `POST /api/counties` - Create county (requires token)
- `PUT /api/counties/{id}` - Update county (requires token)
- `DELETE /api/counties/{id}` - Delete county (requires token)

### Cities
- `GET /api/cities` - List all cities
- `GET /api/cities?county_id={id}` - Get cities by county
- `GET /api/cities?county_id={id}&letter={letter}` - Get cities by county and first letter
- `GET /api/cities/letters?county_id={id}` - Get available first letters for county
- `GET /api/cities/{id}` - Get single city
- `POST /api/cities` - Create city (requires token)
- `PUT /api/cities/{id}` - Update city (requires token)
- `DELETE /api/cities/{id}` - Delete city (requires token)

## Project Structure

```
laravel-zip-api-client/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CountyController.php
│   │   │   └── CityController.php
│   │   └── Requests/
│   │       ├── CountyRequest.php
│   │       └── CityRequest.php
│   └── Providers/
│       └── AppServiceProvider.php
├── config/
│   └── services.php
├── resources/
│   └── views/
│       ├── counties/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── show.blade.php
│       │   └── pdf.blade.php
│       └── cities/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           ├── show.blade.php
│           └── pdf.blade.php
├── routes/
│   └── web.php
└── .env.example
```

## Development

### Creating New Features
1. Switch to develop branch: `git checkout develop`
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Make your changes
4. Commit: `git commit -m "Add your feature"`
5. Push and create a pull request

### Git Workflow
- **develop**: Main development branch
- **main/master**: Production-ready code
- All features merge to develop before final merge to main

## Security Notes

- API token is stored in the session for authenticated requests
- Form submission requires CSRF token validation
- Sensitive operations (create, update, delete) require authentication
- Use HTTPS in production

## Troubleshooting

### API Connection Issues
- Verify the API server is running at the configured URL
- Check the `API_URL` in `.env`
- Ensure the API returns proper JSON responses

### Authentication Issues
- Clear session: `php artisan cache:clear`
- Check session driver configuration
- Verify database migrations have run

### Export Issues
- Ensure DomPDF is properly installed: `composer require barryvdh/laravel-dompdf`
- Check file permissions in `storage/` directory

## License

This project is licensed under the MIT License. See LICENSE file for details.

## Support

For issues, questions, or feature requests, please contact the development team.


We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
