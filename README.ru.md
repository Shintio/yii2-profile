Профиль с дополнительными атрибутами для модели ActiveRecord
===================================================
Создание, редактирование, удаление дополнительных атрибутов модели без изменения кода модели.

This instruction is available [in English](./README.md).

Установка
------------

Предпочтительный способ установки- через [composer](http://getcomposer.org/download/).

Выполните команду

```
php composer.phar require shintio/yii2-profile "*"
```

или добавьте

```
"shintio/yii2-profile": "*"
```

в ваш файл `composer.json`.


Перед началом использования
-----

Перед началом использования вам нужно создать таблицы в вашей базе данных. Вы можете найти SQL запрос в конце данного файла.
После создания таблиц вам нужно для этих таблиц создать или сгенерировать через gii модели ActiveRecord.

Использование
-----

Для добавления дополнительных полей вам нужно вставить строку в таблицу '*enitity_profile_field*'.
- **code**- строка по которой вы можете обращаться и работать с полем.
- **name**- string, Человекопонятное название поля. Используйте это для отображения названия поля пользователям.
- **type**- json, тип поля. Эта версия расширения поддерживает только *`{"type":"text"}`*. В последующих обновлениях будет добавлено больше типов.

Пространство имён:

```php
use shintio\profile\Profile;
```

Создание объекта Profile и модели ActiveRecord по названию класса:

```php
// User- модель ActiveRecord
$profile=new Profile(User::className());
```

Создание объекта Profile из существующей модели ActiveRecord:

```php
// Мы можем создать новый экземпляр модели User;
$user=new User();
// Так же мы можем использовать существующий экземпляр модели User;
//$user=User::find()->one();

$profile=new Profile($user);
```

Поиск Profile по имени модели ActiveRecord:

```php
$query=Profile::find(User::className());

// В функции where мы можем использовать:
// Ассоциированный массив для условия '='
$query->where(['username'=>'admin']); // username- атрибут модели User
// Нумерованный массив для условия '%LIKE%'
$query->andWhere(['LIKE','firstName','Adm']); // firstName- дополнительное поле из профиля
$query->orWhere(['lastName'=>'Great']); // lastName- дополнительное поле из профиля

$profile=$query->one();
//$profiles=$query->all();
```

Работа с полями:

```php
echo $profile->getField('firstName'); // Admin

$profile->setField('firstName','Moder');
echo $profile->getField('firstName'); // Moder

$profile->setField('username','moder');

echo '<pre>';
// Получение профиля массивом объектов Field
var_dump($profile->getProfile());
// Получение профиля в ассоциорованном массиве
// var_dump($profile->getProfile(true));
// Тоже самое, что и getProfile(true)
// var_dump($profile->getProfileInArray());
echo '</pre>';

$profile->save();
// username: moder
// firstName: Moder
// lastName: Great
```

SQL запрос для генерации таблиц из примера вы можете найти в файле **example.sql**.
Также вы можете использовать шаблон ниже для создания таблиц любых ваших сущностей.
Перед выполнением этого запроса замените все '*entity*' на название вашей сущности.

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
