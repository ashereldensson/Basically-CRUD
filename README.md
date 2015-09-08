# Basically-CRUD
Basically CRUD is a class that helps creating basic CRUD system/application, and making it maintainable and secure against SQL injection.

To start using it, create an object passing the path to DB.ini file, and you're done. Now you can execute SQL queries. However, I created it with these two concepts in mind:
 * DI (Dependency Injection)
 * Law of Demeter (a.k.a tell, don't ask.) 

So make sure to use it this way:
In your class, declare a dependency of this class in the constructor (or using set method...etc) and start using methods of this class in your class.

For more information checkout the example.
