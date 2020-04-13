# php-rest
raw php rest api with mysql, for now without any structure defined.
## endpoints

 - `GET /` : Get 10 contacts
 - `GET /search/{search}` : Get contacts matching search parameter
 - *offset* works if you add `?offset={offset}` at the end of the above methods
 - `GET /id/{id}` : Get 1 contact with matching id parameter
 - `POST /` : Post 1 contact

 Provide name and phone.

```json
{
    "name": "[string min 5]",
    "phone": "[integer min 5]"
}
```
 - `PUT /` : Update 1 contact

 Provide name, phone and id.

```json
{
    "name": "[string min 5]",
    "phone": "[integer min 5]",
    "id": "[integer]"
}
```
 - `DELETE /id/{id}` : Delete 1 contact with matching id parameter
