# SQL queries sandbox

# Project Overview:
```
Application for testing the sql queries.
```
# Technologies Used
Backend: PHP, PDO, MySQL.
Frontentd: Javascript, jQuery 3+, Bootstrap 4.3.
Authentication: no.
Data format: JSON.
Deployment: GitHub.

# Application features
- The settings for connecting to the database are stored in the .env file. ./vendor/autoload.php create array $_ENV.
- Header. It imported into each page. :hover styles, the active page is highlighted.
- Header has log-in select
![screen](https://github.com/bart-git21/JS-PHP-MySQL--sql-queries-testing/blob/main/login.jpg)
- The selected user is stored in localStorage. It is displayed when you navigate between pages. 
- Intro page show all logged user queries.
![screen](https://github.com/bart-git21/JS-PHP-MySQL--sql-queries-testing/blob/main/intro.jpg)
- Queries page show queries for logged user. Selection of another user will display the queries for that user.
- The button 'Выполнить' creates a table containing the received data. 
![screen](https://github.com/bart-git21/JS-PHP-MySQL--sql-queries-testing/blob/main/query.jpg)
- User can modify the query or create the new one.
- If the user changes the query, the list of queries will also change.
![screen](https://github.com/bart-git21/JS-PHP-MySQL--sql-queries-sandbox/blob/main/edit.jpg)

### Base api URL
localhost/api/index.php

# Endpoints

## GET /login/
Read all users. Access simple log-in process. It is used for creating list of users in the login page.
### Client request example
*User log-in as user with id = 2*
### api response example
* **Status code**: 200
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
[
    {
        id: 1,
        login: "admin",
        password: "admin",
        role: "admin",
    },
    {
        id: 2,
        login: "Item1",
        password: "123",
        role: "user",
    },
    {
        id: 3,
        login: "Item2",
        password: "456",
        role: "user",
    },
]
```

## POST /login/
Log-in. Create session and store user in localStorage.
### Client request example
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    "login": "Item1",
    "password": "123"
}
```
### api response example
* **Status code**: 200
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    "login": "Item1",
    "id": 2
}
```
### Error Handling
- 401 Unauthorized: authentication failed or missing

## POST /user/
Registration. Create new user.
### Client request example
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    "login": "New user",
    "password": "999"
}
```
### api response example
* **Status code**: 201
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    "id": 4,
    "login": "New user"
}
```
### Error Handling
- 401 Unauthorized: authentication failed or missing

## GET /query/
Read all queries for specific logged user. Admin read all queries from all users.
### Client request example
*User log-in as user with id = 2*
### api response example
* **Status code**: 200
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
[
    {
        id: 1,
        login: "Item1",
        name: "get all planets",
        query: "SELECT * FROM test_data",
        user_id: 2,
    },
    {
        id: 3,
        login: "Item1",
        name: "Get visited planets",
        query: "SELECT * FROM test_data WHERE first_visited_year IS NOT NULL",
        user_id: 2,
    }
]
```
### Error Handling
- 400 Bad Request: invalid request data or format
- 401 Unauthorized: authentication failed or missing
- 404 Not Found: query not found or does not exist
- 500 Internal Server Error: server-side error or exception

## GET /query/?id
Read a query with a specific id.
### Client request example
*User select query with id = 5*
### api response example
* **Status code**: 200
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    query: {
        id: 5,
        name: "get Mars data",
        query: "SELECT * FROM test_data WHERE id = 4",
        user_id: 3,
    };
    queryResult: [
        {
            diameter_km: 6792,
            distance_from_sun_million_km: 227.9,
            first_visited_year: 1965,
            id: 4,
            name: "Mars",
        }
    ];
}
```
### Error Handling
- 400 Bad Request: invalid request data or format
- 401 Unauthorized: authentication failed or missing
- 404 Not Found: query not found or does not exist
- 500 Internal Server Error: server-side error or exception

## POST /query/
Create new query.
### Client request example
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    "name": "New query",
    "query": "SELECT * FROM test_data WHERE id > 3 AND id < 5",
    "userId": "2"
}
```
### api response example
* **Status code**: 200
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    "newQueryId": 4
}
```
### Error Handling
- 400 Bad Request: invalid request data or format
- 401 Unauthorized: authentication failed or missing
- 500 Internal Server Error: server-side error or exception

## PUT /query/?id
Update a query with a specific id.
### Client request example
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    "id": "3",
    "name": "get Earth data",
    "query": "SELECT * FROM test_data WHERE id = 3",
    "userId": "3"
}
```
### api response example
* **Status code**: 200
* **Headers**: 'Content-Type': 'application/json'
* **Body**:
```
{
    "success": "query successfully updated"
}
```
### Error Handling
- 400 Bad Request: invalid request data or format
- 401 Unauthorized: authentication failed or missing
- 404 Not Found: query not found or does not exist
- 500 Internal Server Error: server-side error or exception
