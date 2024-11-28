# webDev-LibraryProject  

This project was developed as part of the Web Development module in Year 2 of my Computer Science degree. The goal was to create a functional book reservation system using **PHP** and a **MySQL** database. The application allows users to perform essential library management tasks, including searching for books and reserving them.  

The key features of this web application include:  
- **User Registration and Login:** Secure account creation and authentication.  
- **Book Search Functionality:** Users can search for books based on various criteria, including title, author, and category.  
- **Book Reservation:** Users can reserve available books and manage their reservations.  
- **Reservation Management:** A personalized dashboard for users to view and unreserve their currently reserved books.  

This document provides a detailed overview of each PHP page used in the application, explaining their roles, how they interact with one another, and how they contribute to the overall functionality of the system.

---

# Descriptions for PHP Pages for Luke’s Library

## 1. **index.html**  
**Description:**  
The homepage of the library website where users are greeted with a login form. This page consists entirely of HTML with a small JavaScript snippet to provide dynamic feedback for incorrect login attempts. Users can also find a link here to register a new account, which redirects them to the registration page. Once the form is submitted, it directs users to `login.php` for backend validation and processing.  

**Image:**  
![image](https://github.com/user-attachments/assets/2bf26a1e-24b9-454a-b49d-404cf7fed309)
 
---

## 2. **registration.html**  
**Description:**  
This page is the entry point for new users to create an account. It features an HTML form requiring details like username, password, and address. A JavaScript function monitors the URL for error parameters and displays appropriate error messages, such as prompts for a six-digit password. However, actual validation and account creation are handled by `register_user.php`, making this page purely responsible for user input and front-end interactions.  

**Image:**  
![image](https://github.com/user-attachments/assets/caceee86-bc12-42ef-9adb-c3963c5ce943)
 
---

## 3. **login.php**  
**Description:**  
The backend script that processes the login form from `index.html`. It checks if the form has been submitted using the POST method. Upon submission, it connects to the database to verify the provided username and password. If the credentials match, a session variable is created for the username, and the user is redirected to `reserved_list.php`. If login fails, the user is sent back to `index.html` with a query parameter `?wrong=true`, which triggers a JavaScript alert about incorrect credentials.  

---

## 4. **logout.php**  
**Description:**  
A simple script that logs the user out by calling `session_unset()` and `session_destroy()`, effectively clearing all session data, including the username. After the session is cleared, the user is redirected back to `index.html`, ensuring a secure logout process.  

---

## 5. **register_user.php**  
**Description:**  
Handles the backend processing of the registration form from `registration.html`. It first validates the submitted form data using the POST method. It then verifies if the password is six characters long and if the mobile number is 10 digits. If these conditions fail, the user is redirected back to `registration.html` with appropriate query parameters (`?password=true` or `?mobile=true`), which trigger error messages through JavaScript. If all validations pass, the script inserts the user’s details into the database and redirects them to the login page.  

---

## 6. **reserved_list.php**  
**Description:**  
This page displays a list of books reserved by the logged-in user. The script first checks if a user is logged in by verifying the existence of a session variable. If no session is found, it redirects to `index.html`. Upon successful authentication, it establishes a database connection and fetches data by joining three tables: `books`, `reservations`, and `category`. The resulting query retrieves book details such as ISBN, title, author, edition, year, reserved status, and category. The books are displayed in a table format, with an "Unreserve" link for each item, linking to `unreserve.php`. If no books are reserved, a message notifies the user.  

**Image:**  
![image](https://github.com/user-attachments/assets/bc86ba7f-4332-4e59-8cc3-524ad1e7a1a8)
  
---

## 7. **search_page.php**  
**Description:**  
This page provides a search interface for users to find books. It fetches a list of categories from the database and populates a dropdown menu. Users can search by title, author, or category. When the form is submitted, the input is sent to `search_function.php` for processing. The script dynamically builds the HTML form, ensuring that categories are up-to-date, and prepares the search criteria.  

**Image:**  
![image](https://github.com/user-attachments/assets/a4207728-30f7-4a46-8b71-bb10227fe80c)
  
---

## 8. **search_function.php**  
**Description:**  
Processes the search requests from `search_page.php`. It retrieves search parameters (title, author, and category) using the GET method and dynamically constructs an SQL query to match the user’s criteria. The script supports flexible search options by appending conditions to the query based on available inputs. The results are displayed in a paginated table, showing relevant book details like ISBN, title, author, and availability. Each result includes an action link (Reserve or Unavailable), depending on the book’s status. If no matches are found, a message is displayed.  

**Image:**  
![image](https://github.com/user-attachments/assets/c76f9a2d-dad2-47a1-a98f-5e0e18d757e6)
  
---

## 9. **reserve_book.php**  
**Description:**  
Handles the reservation of books. The script retrieves the book’s ISBN from the GET parameters and updates the database by marking the book as reserved (`Y`). It also inserts a new record into the `reservations` table with the username, book ISBN, and reservation date. Finally, a confirmation message is displayed, and a small bit of JavaScript is used to redirect you to the `reserved_list.php` page after a few seconds.  

**Image:**  
![image](https://github.com/user-attachments/assets/d396f300-e1b5-4cbd-8a12-32fc5a231892)
  
---

## 10. **unreserve.php**  
**Description:**  
This script reverses the book reservation process initiated by `reserve_book.php`. It retrieves the ISBN of the book and updates the database to mark it as unreserved (`N`). The corresponding entry in the `reservations` table is also deleted. Once the process is complete, you are redirected back to `reserved_list.php`.  

---

## Summary of Page Interactions:  
- **index.html** interacts with **login.php** for user authentication and **registration.html** for account creation.  
- **registration.html** sends data to **register_user.php** for processing.  
- **reserved_list.php** relies on **reserve_book.php** and **unreserve.php** to manage book reservations.  
- **search_page.php** and **search_function.php** work together to handle user searches and display results. **reserve_book.php** is then used to reserve specific books.  
- **logout.php** ensures session management and secure logout.

---
