# PHP class для работы с [Яндекс.Фотки](http://fotki.yandex.ru), version 0.2 (release)

[Демка](http://orloffv.ru/fotki/)

Возможности:
------------

получение последних фотографий `get_all_photos`

получение всех альбомов у пользователя `get_albums`

получение фотографий в альбоме `get_album_photos($album_id)`

получение всех альбомов с обложками new! `get_albums_with_preview`

прослойка кэширования

возможность залогиниться и получить приватные фотографии/альбомы

Пример:
------------

> $fotki = new YaFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS'), 'cache' => TRUE));

> var_dump($fotki->get_albums()); 

или

> var_dump(YaFotki::instance(array('login' => 'vitaly.orloff', 'sizes' => array('XXS'), 'cache' => TRUE))->get_albums());

что бы залогиниться и получить приватные данные нужно в конструкторе указать пароль и параметр 'protected' - TRUE

> $fotki = new YaFotki(array('login' => 'vitaly.orloff', 'password' => 'my_pass', 'protected' => TRUE));