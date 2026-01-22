# SaaS Core

A modern, production-ready Laravel 12 SaaS starter template with invitation-based authentication, role management, and a clean architecture using Brain processes.

## Features

- **🔐 Invitation-Based Authentication**
  - Slack OAuth integration
  - Secure invitation system with tokens and PINs
  - Email-based user invitations

- **👥 User Management**
  - Complete CRUD for users
  - Role and permission system
  - User profiles with avatars
  - Account settings (profile, password, theme)

- **🏗️ Brain Architecture**
  - Process-driven business logic
  - Clean separation of concerns
  - Reusable tasks for complex operations

- **🎨 Modern UI Stack**
  - Livewire 4 (Full-stack reactive components)
  - Mary UI components
  - Tailwind CSS 4
  - Dark mode support

- **🔧 Developer Experience**
  - GitHub Actions CI/CD (Tests + Code Quality)
  - Laravel Pint (Code formatting)
  - Larastan (Static analysis - Level 5)
  - Pest 4 for testing (153 tests)
  - Laravel Boost MCP server support

## Tech Stack

| Technology | Version | Purpose |
|-----------|---------|---------|
| PHP | 8.3 | Runtime |
| Laravel | 12.x | Framework |
| Livewire | 4.x | Full-stack framework |
| Mary UI | 2.x | UI Components |
| Tailwind CSS | 4.x | Styling |
| Pest | 4.x | Testing framework |
| SQLite | - | Default database (dev/test) |

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- SQLite3

## Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd saas-core
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
```bash
touch database/database.sqlite
php artisan migrate --seed
```

5. **Build assets**
```bash
npm run build
# or for development
npm run dev
```

6. **Start the server**
```bash
php artisan serve
```

Visit `http://localhost:8000`

## Configuration

### Slack OAuth Setup

1. Create a Slack App at https://api.slack.com/apps
2. Add OAuth redirect URL: `{APP_URL}/auth/slack/callback`
3. Add required scopes: `users:read`, `users:read.email`
4. Update `.env`:

```env
SLACK_CLIENT_ID=your-client-id
SLACK_CLIENT_SECRET=your-client-secret
SLACK_REDIRECT_URI="${APP_URL}/auth/slack/callback"
```

## Development

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=SlackAuthTest

# Run with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Format code with Pint
vendor/bin/pint

# Run static analysis
vendor/bin/phpstan analyse

# Check code without fixing
vendor/bin/pint --test
```

## Architecture

### Brain Processes & Tasks

This project uses the Brain architecture pattern for business logic:

```
app/Brain/
├── Auth/
│   ├── Processes/
│   │   └── SlackAuthProcess.php
│   └── Tasks/
│       ├── FindOrCreateUserFromSlackTask.php
│       ├── LoginTask.php
│       └── LogLoginTask.php
└── User/
    ├── Processes/
    └── Tasks/
```

**Example Usage:**
```php
use App\Brain\Auth\Processes\SlackAuthProcess;

SlackAuthProcess::dispatchSync([
    'slackId' => 'U12345',
    'email' => 'user@example.com',
    // ...
]);
```

### Livewire Components

Multi-File Components (MFC) are located in `resources/views/pages/`:
- Each component has a `.php` file (logic) and `.blade.php` file (template)
- Uses Mary UI components for consistent design
- Supports reactive properties with validation

## CI/CD

GitHub Actions workflows are configured for:

- **Tests** - Runs Pest test suite with SQLite
- **Code Quality** - Validates Pint formatting and Larastan analysis

Workflows run on push and pull requests to `main` and `develop` branches.

## Default Credentials

After running seeders, you can use:

- Check the `UsersSeeder` for default users
- Invitations can be created through the UI or console

## Security

- All authentication requires valid invitations
- Session security with regeneration on login
- CSRF protection enabled
- Password hashing with bcrypt
- SQL injection protection via Eloquent ORM
