# URL Shortener Service

A Laravel-based URL shortener service with multi-tenant company support and role-based access control.

## Features

- Multi-company support with isolated data
- Role-based access control (SuperAdmin, Admin, Member)
- Invitation system for user onboarding with token-based registration
- Short URL creation with access restrictions
- Authentication and authorization
- Professional UI with navigation menu bar
- Role-based navigation menus

## Requirements

- PHP 8.4 or higher
- Composer
- SQLite or MySQL database

## Setup Instructions

### 1. Clone the Repository

```bash
git clone <repository-url>
cd urlshortTest
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Update the `.env` file with your database configuration. For SQLite:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=urlshortener
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Create Database (SQLite only)

```bash
touch database/database.sqlite
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed the Database

This will create all roles and a SuperAdmin account:

```bash
php artisan db:seed
```

The SuperAdmin credentials are:
- Email: `superadmin@example.com`
- Password: `password`

**Note:** To completely reset the database and run fresh migrations with seeding, use:
```bash
php artisan migrate:fresh --seed
```

### 8. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Usage

### Login

Navigate to `http://localhost:8000/login` and log in with the appropriate credentials:

**SuperAdmin:**
- Email: `superadmin@example.com`
- Password: `password`

**Invited Users (Admin/Member):**
- Email: As specified in invitation
- Password: `password123` (default - should be changed after first login)

### Creating Companies

Only SuperAdmin can create companies. After logging in as SuperAdmin:
1. Go to Dashboard
2. Click on "Companies"
3. Click "Create New Company"
4. Enter company name and submit

### Inviting Users

SuperAdmin and Admin can invite users:

**Step 1: Send Invitation**
1. Go to "Invitations"
2. Click "Send Invitation"
3. Enter email, select role (Admin/Member for SuperAdmin, Admin/Member for Admin), and optionally select an existing company
4. Submit the form

**Step 2: Get Invitation Token**
1. Go to "Invitations"
2. Find the newly created invitation with status "pending"
3. Copy the token from the "Token" column

**Step 3: Share Invitation Link**
1. Create the invitation URL: `http://localhost:8000/invite/{token}`
2. Replace `{token}` with the actual token copied from the previous step
3. Share this link with the invited user via email, chat, or any other method

**Step 4: User Accepts Invitation**
1. The invited user clicks on the shared link
2. They will be automatically registered in the system
3. Their account will be created with:
   - Email: As specified in the invitation
   - Password: `password123` (default)
   - Role: As specified in the invitation
   - Company: As specified in the invitation
4. They will be automatically logged in and redirected to the dashboard

**Restrictions:**
- SuperAdmin can invite Admin to a new company
- Admin can invite Admin or Member to their own company

### Creating Short URLs

Admin and Member roles can create short URLs:
1. Go to "Short URLs"
2. Click "Create Short URL"
3. Enter the original URL
4. Submit

### Access Restrictions

- **SuperAdmin**: Cannot create short URLs, can view all short URLs from every company
- **Admin**: Can create short URLs, can only view short URLs created in their own company
- **Member**: Can create short URLs, can only view short URLs created by themselves

### Short URL Access

Short URLs are **publicly accessible**. Anyone can access short URLs without authentication, and they will redirect to the original URL.

**Viewing Short URLs:**
- **SuperAdmin**: Can view all short URLs from all companies
- **Admin**: Can view short URLs created in their own company
- **Member**: Can view short URLs created by themselves

**Redirecting:**
Short URLs can be accessed publicly via their short code (e.g., `http://localhost:8000/abc123`) and will redirect to the original URL.

## Complete Workflow Example

### Creating First Admin

1. **Login as SuperAdmin**
   - Email: `superadmin@example.com`
   - Password: `password`

2. **Create Company (Optional)**
   - Dashboard → Companies → Create Company
   - Enter company name: "Tech Company"
   - Submit

3. **Send Invitation to Admin**
   - Dashboard → Invitations → Send Invitation
   - Email: `admin@example.com`
   - Role: Admin
   - Company: Select "Tech Company" (or leave blank to auto-create)
   - Submit

4. **Get Invitation Token**
   - Dashboard → Invitations
   - Find the invitation with status "pending"
   - Copy the token from the "Token" column

5. **Share Invitation Link**
   - Create URL: `http://localhost:8000/invite/{copied_token}`
   - Share this link with the admin

6. **Admin Accepts Invitation**
   - Admin clicks the shared link
   - Account automatically created
   - Automatically logged in
   - Default password: `password123`

### Creating Member

1. **Login as Admin**
   - Email: `admin@example.com`
   - Password: `password123`

2. **Send Invitation to Member**
   - Dashboard → Invitations → Send Invitation
   - Email: `member@example.com`
   - Role: Member
   - Company: Automatically set to Admin's company
   - Submit

3. **Share Invitation Link**
   - Copy token from Invitations page
   - Create URL: `http://localhost:8000/invite/{token}`
   - Share with member

4. **Member Accepts Invitation**
   - Member clicks the link
   - Account automatically created
   - Automatically logged in

## Running Tests

```bash
php artisan test
```

The test suite covers:
- Admin can create short URLs
- Member can create short URLs
- SuperAdmin cannot create short URLs
- SuperAdmin can see all short URLs from every company
- Admin can only see short URLs created in their own company
- Member can only see short URLs created by themselves
- Short URLs are publicly resolvable and redirect to original URL

## AI Tools Used

- **ChatGPT** — Used for the following purposes:
  - Understanding Laravel 13 authentication approaches (manual controller vs Fortify vs Breeze) and evaluating trade-offs.
  - Reviewing permission logic for correctness.
  - Generating test cases for authentication, role-based access control, permission management, and multi-company access scenarios.
  - Debugging runtime errors and failures during development, such as:
    - Migration order conflicts between Spatie permission tables and the `companies` table.
    - Spatie Teams setup issues (team context not being set, incorrect role scoping).
    - Route and middleware errors (e.g., redirect loops, missing route names, CSRF issues on logout).

Reviewing permission logic for correctness.