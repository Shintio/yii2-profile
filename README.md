Profile with additional attributes for ActiveRecord
===================================================
Create, update, delete additional attributes of model without change model code.

Это руководство также доступно [на русском языке](./README.ru.md).

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require shintio/yii2-profile "*"
```

or add

```
"shintio/yii2-profile": "*"
```

to the require section of your `composer.json` file.


Before start
-----

Before start you should create tables in your db. You can find SQL query in the end of this file.
After create tables you should create or generate with gii ActiveRecord models for these tables.


Usage
-----

For adding additional fields you should insert row to table '*enitity_profile_field*'.
- **code**- string on which you can use this field.
- **name**- string, Human-Understandable name of this field. Use this for display field name to users.
- **type**- json, type of this field. This version only supports *`{"type":"text"}`*. More types in future updates.

namespace:

```php
use shintio\profile\Profile;
```

Create Profile object and ActiveRecord model by class name:

```php
// User- ActiveRecord model
$profile=new Profile(User::className());
```

Create Profile object by existing ActiveRecord:

```php
// We can create new User;
$user=new User();
// Also we can use existing User;
//$user=User::find()->one();

$profile=new Profile($user);
```

Find Profile:

```php
$query=Profile::find(User::className());

// In where function we can use:
// Assoc array for '=' condition
$query->where(['username'=>'admin']); // username- User's attribute
// Indexed array for '%LIKE%' condition
$query->andWhere(['LIKE','firstName','Adm']); // firstName- additional profile field
$query->orWhere(['lastName'=>'Great']); // lastName- additional profile field

$profile=$query->one();
//$profiles=$query->all();
```

Work with fields:

```php
echo $profile->getField('firstName'); // Admin

$profile->setField('firstName','Moder');
echo $profile->getField('firstName'); // Moder

$profile->setField('username','moder');

echo '<pre>';
// Get profile in array of Field ojbects
var_dump($profile->getProfile());
// Get profile in assoc array
// var_dump($profile->getProfile(true));
// Same as getProfile(true)
// var_dump($profile->getProfileInArray());
echo '</pre>';

$profile->save();
// username: moder
// firstName: Moder
// lastName: Great
```

SQL for generate example tables you can find in file **example.sql**
Also you can you this template for create table for any entities.
Before executing this query replace all '*entity*' to your entity name.

```sql
CREATE TABLE `entity` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `entity_profile` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `entity_profile_field` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `entity`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `entity_profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entity_id` (`entity_id`),
  ADD KEY `field_id` (`field_id`);

ALTER TABLE `entity_profile_field`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `entity_profile`
  ADD CONSTRAINT `entity_profile_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `entity_profile_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `entity_profile_ibfk_2` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
```
