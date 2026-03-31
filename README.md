# Library_Management_System
### This system simply makes it easy for libraries to keep track of all their books and who borrows, reserve, and pay for the fines.

![image alt](https://github.com/mtxmln-devs/Library_Management_System/blob/122145a1187272b17d53e798ab5c3ef57aa0f969/Screenshot%202025-06-02%20122000.png)

# 📚 Library Management System



<div align="center">
   
<img src="https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E" />
<img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" />
<img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" />  

> A comprehensive digital library management platform designed to streamline library operations, book management, and user interactions with modern web technologies.

</div>

--- 

## 📖 Overview

This Library Management System is a full-featured web application that digitizes traditional library operations. It provides librarians and users with an intuitive platform to manage books, track borrowing activities, handle reservations, and maintain user accounts. The system features a modern purple-themed interface with comprehensive user management, search functionality, and administrative tools.

## ✨ Features

### 🔐 Authentication & User Management
- **User Registration**: Complete sign-up process with personal information
- **Secure Login**: Email-based authentication system
- **User Profiles**: Detailed user information and account management

### 📖 Book Management
- **Comprehensive Book Database**: Store detailed book information (ISBN, title, author, category, publisher)
- **Search**: Allows search functionality for easy locating the specified books
- **Book Catalog**: Organized display of available books with sorting options
- **Inventory Tracking**: Real-time availability status and copy management

### 💳 Digital Wallet System
- **Account Balance**: Track user wallet balance for fines and fees
- **Payment Processing**: Handle library fines and service charges
- **Balance Management**: Add funds and view spending history

### 📊 Dashboard & Analytics
- **Statistics Overview**: Visual dashboard with key metrics
- **Borrowed Books Tracking**: Monitor currently borrowed items
- **Reserved Books Management**: Handle book reservations and holds
- **Overdue Notifications**: Track and manage overdue items

### 🎨 User Interface Features
- **Modern Design**: Clean, professional purple-themed interface
- **Responsive Layout**: Optimized for desktop, tablet, and mobile devices
- **Interactive Elements**: Hover effects and smooth transitions
- **Data Tables**: Organized display of information with pagination
- **Search Integration**: Quick search functionality across all modules

## 🛠️ Tech Stack
### Frontend Technologies
- **HTML5**: Semantic markup and structure
- **CSS3**: Modern styling with custom design system and responsive layouts
- **JavaScript (ES6+)**: Interactive functionality and client-side logic
- **Bootstrap/Custom Framework**: Responsive grid system and UI components

### Backend Technologies
- **Server-Side Language**: PHP/Node.js/Python (based on implementation)
- **Database**: MySQL/PostgreSQL for data storage
- **Session Management**: Secure user session handling
- **API Integration**: RESTful API endpoints for data communication

### Database Schema
- **Users Table**: User accounts and profile information
- **Books Table**: Complete book catalog with metadata
- **Transactions Table**: Borrowing and return records
- **Reservations Table**: Book reservation management
- **Wallet Table**: Financial transaction records

### Development Tools
- **Version Control**: Git for source code management
- **Database Management**: phpMyAdmin/pgAdmin for database operations
- **Code Editor**: Compatible with VS Code, PHPStorm, or preferred IDE
- **Testing Tools**: Unit testing and integration testing frameworks

## 🎯 Project Goals

### 📋 Primary Objectives
1. **Digital Transformation**: Modernize traditional library operations
2. **User Experience**: Provide intuitive interface for library users
3. **Operational Efficiency**: Streamline librarian workflows and tasks
4. **Data Management**: Centralized and organized information system

### 🔧 Secondary Objectives
1. **Scalability**: Support growing library collections and user base
2. **Security**: Protect user data and system integrity
3. **Reporting**: Generate comprehensive reports and analytics
4. **Integration**: Support for future system integrations

### 🚀 Long-term Vision
- Mobile application development
- RFID integration for automated check-in/check-out
- AI-powered book recommendations
- Integration with external library systems
- Advanced analytics and reporting dashboard

## 🚀 Setup Guide

### 📋 Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4+ or Node.js 14+
- MySQL 5.7+ or PostgreSQL 12+
- Modern web browser
- Text editor or IDE

### 💻 Installation Steps

#### Method 1: Local Development Setup
1. **Clone the Repository**
   ```bash
   git clone https://github.com/mtxmln-devs/Library_Management_System.git
   cd Library_Management_System
   ```

2. **Database Setup**
   ```sql
   -- Create database
   CREATE DATABASE book_management;
   
   -- Import database schema
   mysql -u username -p book_management < database/book_management.sql
   ```

3. **Configuration**
   ```php
   // config/database.php
   $host = 'localhost';
   $dbname = 'book_management';
   $username = 'your_username';
   $password = 'your_password';
   ```

4. **Start Local Server**
   ```bash
   # Using PHP built-in server
   php -S localhost:8000
   
   # Or configure Apache/Nginx virtual host
   ```

#### Method 2: XAMPP/WAMP Setup
1. **Install XAMPP/WAMP**
   - Download and install XAMPP or WAMP
   - Start Apache and MySQL services

2. **Clone Project**
   ```bash
   cd htdocs  # or www folder
   git clone https://github.com/mtxmln-devs/Library_Management_System.git
   ```

3. **Database Configuration**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create new database: `book_management`
   - Import SQL file from `/database` folder

4. **Access Application**
   - Navigate to `http://localhost/Library_Management_System`

### 🔧 Environment Configuration

 **Database Connection**
   ```php
   // config/config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'book_management');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```
 

## 📁 Project Structure
```bash
Library_Management_System/
├── home.php                 # First page 
|
├── includes/
│   ├── index.php            # Common header
|
├── pages/
│   ├── loginForm.php        # User authentication
│   ├── signup.php           # User registration
│   ├── index.php            # Main dashboard
│   ├── profile.php          # User profile
│   ├── wallet.php           # User Digital wallet
|   ├── borrowedBooks.php    # Borrow Books 
│   ├── borrowForm.php       # Borrow Form 
│   ├── finesBooks.php       # Fines Book 
│   ├── reservedBooks.php    # Reserve Book 
│   ├── reserveForm.php      # Reserve Form  
│   ├── returnedBooks.php    # Return Book 
|
├── operation/
|   ├──search.php            # Search functionality
|
├── assets/
│   │   └── logo.png         # First Page logo
|
├── database/
│   ├── book_management.sql  # Database structure
│   ├── add fines.sql       # Fines database structure
|
└── README.md                # Project documentation
```

## 📖 Usage Instructions
### 👤 For Users
1. **Registration**: Create account with personal information
2. **Login**: Access system with email and password
3. **Search Books**: Use advanced search to find books
4. **Borrow Books**: Request books for borrowing
5. **Manage Profile**: Update personal information
6. **Check Wallet**: Monitor balance and transactions

### 👨‍💼 For Librarians
1. **Dashboard Access**: View system statistics and activities
2. **Book Management**: Add, edit, and remove books from catalog
3. **User Management**: Handle user accounts and permissions
4. **Transaction Processing**: Approve borrowing requests and returns
5. **Report Generation**: Create usage and inventory reports

### 🔧 Administrative Functions
- **System Configuration**: Manage application settings
- **Database Maintenance**: Backup and optimize database
- **User Role Management**: Assign and modify user permissions
- **Audit Logs**: Review system access and changes

## 🔒 Security Features
### 🛡️ Data Protection
- **Password Encryption**: Secure password hashing
- **SQL Injection Prevention**: Prepared statements and input validation
- **XSS Protection**: Output sanitization and CSP headers
- **Session Security**: Secure session management and timeout

### 🔐 Access Control
- **Role-Based Permissions**: Different access levels for users
- **Authentication Required**: Protected routes and functions
- **Activity Logging**: Comprehensive audit trail
- **Data Validation**: Server-side input validation

## 🤝 Contributing
I welcome contributions to improve the library management system! Please follow these guidelines:

### 🔧 Development Guidelines
- Follow existing code style and conventions
- Write clear, commented code
- Test new features thoroughly
- Update documentation as needed
- Ensure database migrations are included

### 📝 Contribution Process
1. Fork the repository
2. Create a feature branch
3. Implement your changes
4. Test thoroughly
5. Submit a pull request with detailed description


## 🆘 Support
If you encounter any issues or have questions:
- 🐛 **Bug Reports**: Submit detailed issues on GitHub
- 💡 **Feature Requests**: Propose new features via GitHub issues
- 📧 **Technical Support**: Contact the development team
- 📚 **Documentation**: Review the user manual and API documentation

## 🔄 Updates
Stay updated with the latest features and improvements:
- ⭐ Star the repository for notifications
- 👀 Watch for new releases and updates
- 📱 Follow development progress and roadmap

## 🎓 Educational Use
This system is perfect for:
- Library science students and projects
- Educational institutions and schools
- Small to medium-sized libraries
- Software development learning and portfolio projects


**Note**: This system is designed for educational and small-scale library use. For enterprise-level implementations, additional security measures and scalability considerations may be required.
