# DataVault

DataVault is a Laravel 10 admin app for bulk business-data import, duplicate detection, merge workflows, incomplete-data review, and reporting.

## Project Overview

DataVault helps clean and manage business listing datasets with:

- bulk import from `CSV`, `XLS`, `XLSX`, and public Google Drive file URLs
- automatic duplicate detection using normalized `business_name + area + city + address`
- duplicate grouping and merge workflows
- incomplete listing detection
- dashboard cards and Chart.js reports
- import history logs
- CSV export and duplicate export
- Laravel Breeze authentication and CSRF protection

## Tech Stack

- Laravel 10
- PHP 8.1+
- MongoDB
- Tailwind CSS
- Alpine.js
- Chart.js
- Laravel Breeze
- `maatwebsite/excel`
- `mongodb/laravel-mongodb`

## Free Infrastructure Choices

DataVault is configured for free usage by default.

- Local database: MongoDB Community Server
- Free hosted database option: MongoDB Atlas free cluster
- File storage: local disk
- Queue: `sync`
- Mail: `log`

AWS is not required anywhere in the current setup.

## Features

- Dashboard
  - total records
  - unique listings
  - duplicate listings
  - incomplete listings
  - city-wise charts
  - recent imports
- Import Data
  - upload `CSV`, `XLS`, `XLSX`
  - import from public Google Drive file URL
  - progress UI
  - validation
  - import history
- All Records
  - search by business name
  - filter by city, category, and area
  - pagination
  - CSV export
  - bulk delete
- Duplicate Records
  - grouped duplicate review
  - original and duplicate visibility
  - duplicate export
- Merge Records
  - choose records to merge
  - choose the master record
  - merge mobile numbers
  - merge categories and sub-categories
  - soft-delete redundant records while preserving `merged_into`
- Incomplete Records
  - flags missing business name, mobile number, or category
- Reports
  - city-wise count
  - category plus city count
  - category plus area count
- API
  - `GET /api/businesses`
  - `GET /api/duplicates`
  - `POST /api/merge`

## Database Schema

### `businesses`

- `id`
- `business_name`
- `area`
- `city`
- `mobile_no`
- `category`
- `sub_category`
- `address`
- `normalized_business_name`
- `normalized_area`
- `normalized_city`
- `normalized_address`
- `duplicate_group`
- `is_duplicate`
- `merged_into`
- `deleted_at`
- `created_at`
- `updated_at`

### `import_logs`

- `id`
- `file_name`
- `source_type`
- `source_reference`
- `status`
- `imported_rows`
- `inserted_rows`
- `duplicate_rows`
- `invalid_rows`
- `notes`
- `meta`
- `started_at`
- `completed_at`
- `created_at`
- `updated_at`

## Installation

### 1. Clone the project

```bash
git clone <your-repo-url> DataVault
cd DataVault
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Install the MongoDB PHP extension

Laravel needs the native MongoDB PHP extension enabled before the app can connect.

- Linux or macOS:

```bash
pecl install mongodb
```

- Windows:
  - enable the matching `php_mongodb.dll` for your PHP version in `php.ini`
  - verify with:

```bash
php -m | findstr mongodb
```

### 5. Copy the environment file

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 6. Generate the app key

```bash
php artisan key:generate
```

### 7. Configure MongoDB

You have two free options.

#### Option A: Local MongoDB Community Server

Use the default `.env` values:

```env
DB_CONNECTION=mongodb
DB_URI=
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=datavault
DB_USERNAME=
DB_PASSWORD=
DB_AUTHENTICATION_DATABASE=admin
```

#### Option B: MongoDB Atlas Free

Create a free Atlas cluster, then set only the URI:

```env
DB_CONNECTION=mongodb
DB_URI="mongodb+srv://<username>:<password>@<cluster-url>/datavault?retryWrites=true&w=majority"
DB_DATABASE=datavault
DB_AUTHENTICATION_DATABASE=admin
```

When `DB_URI` is set, DataVault can use Atlas without any AWS setup.

### 8. Run migrations and seed sample data

```bash
php artisan migrate --seed
```

### 9. Build frontend assets

```bash
npm run build
```

For local development:

```bash
npm run dev
php artisan serve
```

## Default Admin Login

- Email: `admin@datavault.test`
- Password: `password`

## Import Notes

- Local upload accepts `.csv`, `.txt`, `.xls`, and `.xlsx`
- Google Drive import expects a public file link, not a folder link
- Header names are normalized automatically
- Empty rows are skipped
- Invalid rows are logged in import history metadata

## Duplicate Detection Logic

DataVault detects duplicates by:

- converting text to lowercase
- trimming extra spaces
- removing special characters
- comparing normalized `business_name + area + city + address`

The first created record in a duplicate group is treated as the current primary record. The remaining records are marked with `is_duplicate = true`.

## API Usage

API routes are protected by `auth:sanctum`.

Available endpoints:

- `GET /api/businesses`
- `GET /api/duplicates`
- `POST /api/merge`

To generate a token for testing:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::first();
$token = $user->createToken('datavault-api')->plainTextToken;
$token;
```

Then send:

```http
Authorization: Bearer <token>
Accept: application/json
```

## Folder Structure

```text
app/
|-- Http/Controllers
|-- Http/Requests
|-- Imports
|-- Models
|-- Repositories
`-- Services
```

## Verification

Verified locally with:

- `php artisan migrate:fresh --seed`
- `php artisan route:list`
- `php artisan test`

The PHPUnit environment uses a separate MongoDB test database: `datavault_test`.

## Screenshots Placeholder

- `screenshots/dashboard.png`
- `screenshots/import-data.png`
- `screenshots/all-records.png`
- `screenshots/duplicate-records.png`
- `screenshots/merge-records.png`
- `screenshots/incomplete-records.png`
- `screenshots/reports.png`

## Notes

- Registration is disabled to keep access admin-only.
- Redundant merged records are soft deleted so merge history remains traceable through `merged_into`.
- Tailwind pagination is used throughout the dashboard lists.
