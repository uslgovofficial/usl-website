# PHPBank - Version 1.0b

**NOTE:** This is a beta version. It wouldn’t be very wise to start running a real economy on it just yet; you never know what could go wrong. Don’t say I didn’t warn you!

### WHAT'S NEW:
- **Input Validation & Security:** Added `filter_input` and `htmlspecialchars` to sanitize user input and prevent XSS and SQL injection attacks.
- **Prepared Statements:** SQL queries now use prepared statements with parameterized queries to prevent SQL injection.
- **Improved Error Handling:** Replaced `die` statements with user-friendly error messages, and wrapped critical sections in `try-catch` blocks for better exception handling.
- **Database Configuration:** Users can now configure the database host, username, password, and other settings directly through the `/install.php` interface, eliminating the need to manually edit `config.php` as in previous versions.
- **Code Refactoring:** Improved code readability with added whitespace, better formatting, and the use of more descriptive variable names.
- **Database Connection Management:** Closed database connections after each transaction to prevent resource leaks.

**IMPORTANT:**
PHPBank is maintained and updated by the República Livre do Embaú. Updates include security improvements and updates for deprecated functions. The project is currently at version 1.0b. Many features are still non-functional, and further enhancements and polishing will be necessary. The original repository cannot be found. I am not the original developer; I simply took the existing files and updated them, as the project was previously stagnant and insecure for over 14 years.

###################

1. **REQUIREMENTS**
   PHPBank requires:
   - PHP 8.3.6. It was originally programmed in PHP 4.2.3 but should work with PHP 8.x.
   - MySQL 8.3.0. It was initially programmed in MySQL 3.23.32, but this updated version should work fine.

### 2. INSTALLATION
The installation of PHPBank is now easier and can be done directly via the `/install.php` script. Follow the steps below:

1. **Prepare the Environment:**
   - Ensure your server meets the PHP and MySQL requirements listed above.
   - Download and extract the PHPBank files to your desired folder on the server.
   
2. **Access the Installer:**
   - Once the files are uploaded, open your web browser and navigate to the installation script at `/install.php`. 
     Example: `http://www.yourdomain.com/phpbank/install.php`

3. **Database Configuration:**
   - During installation, you will be prompted to enter the following database details:
     - **MySQL Username**
     - **MySQL Password**
     - **MySQL Database Name**
     - **Host (usually `localhost`)**
   - The installer will automatically configure these details, eliminating the need to manually edit `config.php`.

4. **Complete the Installation:**
   - Follow the on-screen instructions to complete the setup. If you encounter any MySQL connection errors, check that the database credentials entered during the setup are correct.
   - After installation, the installer will finalize the setup, and PHPBank will be ready to use.

5. **Post-Installation:**
   - After the installation is complete, for security reasons, it is recommended to delete the `/install.php` script from your server.


3. **ADMIN CP**
   The admin control panel has 4 different sections:
   - **Layout**: Here you can edit the layout of PHPBank. Be careful; if you manage to make everything unreadable, the admin control panel will be affected as well! The colors are HTML hex values. A decent graphics editing program (e.g., Paint Shop Pro) will show the hex value for each color you select somewhere.
   - **Other Information**: This is where you edit the bank information, like the nation’s name, currency symbol, etc., things you entered during installation. You can also edit the URL for the top image.
   - **Account Activation**: Accounts awaiting activation by you will be listed here. You can either activate or delete them.
   - **Force a Transaction**: A forced transaction does not have to be accepted by either of the participants. This is useful for fines or paying wages. Try not to make mistakes with it though, as some people might not appreciate that ;)
