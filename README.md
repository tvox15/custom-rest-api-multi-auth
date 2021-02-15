# custom-rest-api-multi-auth
Custom REST API w/ multi-auth (PHP, AJAX, Javascript, HTML, CSS), email verification, and more

# Features:
1. Full-fledged authentication system (Pages: login, logout, register)

2. Email verification 

3. Password reset by sending through email & password change when logged in

4. Admin dashboard where permissions are set through AJAX

5. Multi-table search function

6. Sub-admins have permission to add entries to tables they have been approved for


# Multi-auth
1. 3 types of users: regular, sub-admin, and admin (A, B, and C)

2. Different permissions/privileges for different users

3. Admin user dashboard to grant/change permissions for other users

When a user registers, they enter in email and password, then a checkbox for whether they want adding privileges. If they select the checkbox, they will be added as userType B in the users table. If they don’t select it, they will be added as userType A.


When you log in as a C user, you will see an admin dashboard. This dashboard shows a list of all type B users and a list of tables that you can select or deselect to add/remove a user’s permissions for a specific table. This way B users can have adding access all tables, some, or none at all. By default B users have no permission for any tables, so you will have to choose one. (When you click a checkbox here, it will automatically update the permission instantly, so there is no save button)


When a B user logs in and goes to the add page, they will only see buttons for the tables that they are approved to add to.


When a type A user logs in, they will not see any Add button on the search page.
Both add and search pages require logins to use.


All users have a profile page which includes a tab for password reset and personal info

# Email verification & Login/Registration
Pages for login, logout, register, forgot password, confirmation email, reset password. Users must confirm the link through email to be able to log in. Password reset requests are also sent through email. It has all the standard validations (valid email, length, passwords matching, etc.)

# Search Box
This is where the user types in the ID number and hits enter.
It will show errors if it is not 20 characters or if it is empty.

# Results Page
This is where the results are shown in a table along with another search box. It also has built in server-side error handling.

# Javascript file
This file is used for validating the data client side.

# Security notes
I added both client-side and server-side data validation and use prepared statements for the database query so no malicious code can be injected.
