# Hein
Build a REST API for a ecommerce website with Symfony 6 and MySQL

# STEPS TO SETUP

- Clone the app repository
- Execute `composer install` to install dependencies
- Configuring the Database in file .env and Launch your localhost
- Create database with commande `php bin/console doctrine:database:create`
- Run SQL migrations : `php bin/console doctrine:migrations:migrate`
- Generate SSL Keys (Install OpenSSL require) : `php bin/console lexik:jwt:generate-keypair`
- Run symfony server : `symfony server:start`
- Test Api with Postman or other way by following the doc bellow 


# Documentation of api

## Entities
- Category
- Product
- Cart
- User

-------------------------------------------------------------------

### Category

<details>
 <summary><code>GET</code><code><b> / </b></code> <code>(get all categories)</code></summary>

##### url

> /api/v1/category

##### Parameters

> None

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        | JSON                                                       |


</details>

<details>
 <summary><code>POST</code> <code><b>/</b></code> <code>(add new category)</code></summary>

 ##### url

> /api/v1/category

##### Parameters

> None


##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `201`         | `text/plain;charset=UTF-8`        |  `{"Created new category successfully with id $id"`}   

##### Exemple data
```javascript
{ 
    "name" : "electronics" 
}
```
</details>


<details>
 <summary><code>PUT</code> <code><b>/</b></code> <code>(update a category)</code></summary>

  ##### url

> /api/v1/category/{id}

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | string (255)   | The id of category you want to update       |


##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `401`         | `text/plain;charset=UTF-8`        |  `{"No category found for id $id"}`  
> | `201`         | `text/plain;charset=UTF-8`        |  `{ "id" : "id" ,"name" : "new_name" }`
##### Exemple data
```javascript
{ 
    "name" : "clothes" 
}
```
</details>
<details>
 <summary><code>DELETE</code> <code><b>/</b></code> <code>(delete a category)</code></summary>

  ##### url

> /api/v1/category/{id}

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | string (255)   | The id of category you want to delete       |


##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `401`         | `text/plain;charset=UTF-8`        |  `{"No category found for id $id"}`  
> | `200`         | `text/plain;charset=UTF-8`        |  `{"Deleted a Category successfully with id $id"}`

</details>

<details>
 <summary><code>GET</code><code><b> / </b></code> <code>(get all categories with there products)</code></summary>

##### url

> /api/v1/category/product

##### Parameters

> None

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        | `{"id":"","name":"","products":[]}`                                                       |

</details>


<details>
 <summary><code>GET</code><code><b> / </b></code> <code>(get all products of one category)</code></summary>

##### url

> /api/v1/category/{id}/product

##### Parameters

> None

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        | `"array of products":[]`                                                       |

</details>

-------------------------------------------------------------------

### User

<details>
 <summary><code>POST</code><code><b> /register </b></code> <code>(client register)</code></summary>

##### url

> /api/v1/register

##### Parameters

> None

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        | `{"message": "Registered Successfully"}` |

##### Exemple data
```javascript
{
  "name":"test",
  "email":"test@gmail.com",
  "password":"123123"
}
```

</details>


<details>
 <summary><code>POST</code><code><b> /login_check </b></code> <code>(client login)</code></summary>

##### url

> /api/v1/login_check

##### Parameters

> None

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        | `{"token": "xxxxx.yyyyy.zzzzz"}` |
> | `404`         | `text/plain;charset=UTF-8`        | `{"message": "Invalid credentials"}` |

##### Exemple data
```javascript
{
  "username":"test@gmail.com",
  "password":"123123"
}
```

</details>

-------------------------------------------------------------------

### Product

<details>
 <summary><code>GET</code><code><b> / </b></code> <code>(get all products)</code></summary>

##### url

> /api/v1/product

##### Parameters

> None

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        | JSON                                                       |


</details>


<details>
 <summary><code>GET</code><code><b> / </b></code> <code>(get one products)</code></summary>

##### url

> /api/v1/product/{id}

##### Parameters

> None

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        | JSON                                                       |


</details>

<details>
 <summary><code>POST</code> <code><b>/</b></code> <code>(add new product)</code></summary>

 ##### url

> /api/v1/product

##### Parameters

> None


##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `201`         | `text/plain;charset=UTF-8`        |  `{"Created new product successfully with id $id"`}   

##### Exemple data
```javascript
{
  "name":"samsung",
  "price":200.00,
  "description":"phone 4 gb RAM 32 gb storage",
  "image":FILE,
  "category_id":1,
  "user_id":1
}
```
</details>


<details>
 <summary><code>PUT</code> <code><b>/</b></code> <code>(update a product)</code></summary>

  ##### url

> /api/v1/product/{id}

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | string (255)   | The id of category you want to update       |


##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `401`         | `text/plain;charset=UTF-8`        |  `{"No product found for id $id"}`  
> | `201`         | `text/plain;charset=UTF-8`        |  `{"name":"","price":,"description":"","image":""category_id":"","user_id":""}`
##### Exemple data
```javascript
{
  "name":"samsung",
  "price":200.00,
  "description":"phone 4 gb RAM 32 gb storage",
  "image":FILE,
  "category_id":1,
  "user_id":1
}
```
</details>
<details>
 <summary><code>DELETE</code> <code><b>/</b></code> <code>(delete a product)</code></summary>

  ##### url

> /api/v1/category/{id}

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | string (255)   | The id of category you want to delete       |


##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `401`         | `text/plain;charset=UTF-8`        |  `{"No product found for id $id"}`  
> | `200`         | `text/plain;charset=UTF-8`        |  `{"Deleted a Product successfully with id $id"}`

</details>

-------------------------------------------------------------------

### Cart

<details>
 <summary><code>GET</code><code><b> / </b></code> <code>(get cart items of user)</code></summary>

##### url

> /api/v1/cart/{id}

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | string (255)   | The id of user u want to get these items cart      |

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        | `"Cart empty for user with id = {id}"`  
> | `200`         | `text/plain;charset=UTF-8`        | `[array of products]`                                                    |


</details>

<details>
 <summary><code>POST</code> <code><b>/</b></code> <code>(add new product to a user cart)</code></summary>

 ##### url

> /api/v1/cart

##### Parameters

> None


##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `203`         | `text/plain;charset=UTF-8`        |  `"This product is already in your cart with id $product_id"` 
> | `200`         | `text/plain;charset=UTF-8`        |  `{added product information}` 

##### Exemple data
```javascript
{ 
  "user_id":2,
  "product_id":2
}
```
</details>


<details>
 <summary><code>DELETE</code> <code><b>/</b></code> <code>(delete product from user cart)</code></summary>

  ##### url

> /api/v1/cart

##### Parameters

> None


##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `text/plain;charset=UTF-8`        |  `{"Deleted a cart successfully with id {cart_id}"}`
> | `404`         | `text/plain;charset=UTF-8`        |  `{"'No Product found for id {product_id}"}`  
> | `405`         | `text/plain;charset=UTF-8`        |  `{"This Product not associate to any cart {product_id}"}`
> | `406`         | `text/plain;charset=UTF-8`        |  `{"This Product not found in cart of user id{user_id}"}`

##### Exemple data
```javascript
{
  "user_id":2,
  "product_id":1
}
```
</details>


# Copyright Notice

**© 2023 Mohcine Boudenjal and Hind El Ouahabi. All rights reserved.**


