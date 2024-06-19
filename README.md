# AzumoFootgear

AzumoFootgear is a web application designed for managing users, orders, and products for an online footwear store. Customers can browse products, add items to their cart, apply coupons, and place orders. Administrators can manage users, view and manage all orders, and access detailed order information.

To run this application, you need to have a local server environment such as XAMPP or MAMP. After installing the local server, start the Apache and MySQL services. Create a database called **AzumoFootgear** in phpMyAdmin and import the **azumofootgear.sql** file.

Update the **db/dbaccess.php** file with the following database connection parameters:
```php
private $host = "localhost";
private $username = "web1user";
private $password = "MomoAzur7";
private $dbName = "AzumoFootgear";
```

Place the project folder in the appropriate directory (htdocs), and navigate to http://localhost:8888/AzumoFootgear in your web browser. The application is now ready for use.
