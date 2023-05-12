# Lumen Backend Project

This is a Lumen backend project that provides an API for searching and filtering server data based on various criteria.


## Requirements

- PHP >= 8.1
- Lumen 10.0
- Composer

Please make sure you have PHP version 8.1 or higher installed on your system.

## Installation

1. Clone the repository
2. `cd` to project root directory   
3. Run `composer install` to install the dependencies
4. Copy `.env.example` to `.env` and configure the environment variables as needed

If the `composer install` command throws an error, please ensure that the following extensions are enabled in your `php.ini` file located in your PHP installation folder:

- extension=openssl
- extension=fileinfo
- extension=gd

## Running Test Cases

To run the test cases, execute the following command:

`vendor/bin/phpunit`


## Usage

Start the Lumen development server:

`php -S localhost:8000 -t public`

Access the API endpoint for filtered server data:

http://localhost:8000/api/servers

Replace the query parameters with your desired filter values.


## Data Setup

- Place the Excel data in `app/server-data/`. This `servers.xlsx` file is pushed into the repository for easy project setup.

- The backend application converts Excel data into a JSON file to improve maintainability and scalability. Please refer to the Project document "System Design" section for more details.

- To convert Excel data to a JSON file, navigate to `http://[YOUR_BACKEND_URL]/api/servers/json-file-generate` in your browser.

- This URL generates a JSON file and stores it under `app/server-data-json/master.json`.

- Generate JSON from multiple Excel files:
  - To generate a master JSON file from multiple Excel files, place all your Excel files into `app/server-data/` folder and delete the `app/server-data-json/master.json` file.
  - Navigate to `http://[YOUR_BACKEND_URL]/api/servers/json-file-generate` in your browser. 
  - This will compile all Excel files data from `app/server-data/`  and create a new `master.json` file.

- Appending new Excel file data to existing JSON file:
  - To add a new Excel file data to the existing master JSON file, navigate to `http://[YOUR_BACKEND_URL]/api/servers/json-file-generate/[EXCEL_FILE_NAME]` and replace `[EXCEL_FILE_NAME]` with the new file name. This will fetch data from that particular Excel file and append it to the `master.json`.

- To check if the data is fetched properly, navigate to `http://[YOUR_BACKEND_URL]/api/servers` in your browser.

- Replace `[YOUR_BACKEND_URL]` with your backend URL.


## CORS URL Setup

- Please ensure to add your frontend URL in the `.env` file in `FRONTEND_APP_URL=[YOUR_FRONTEND_BASE_URL]`. This variable is used to set up the CORS in the `app/Http/Middleware/CorsMiddleware.php`.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

