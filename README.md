# PHP class для работы с [Яндекс.Фотки](http://fotki.yandex.ru), version 0.1 (release)

Рабочая [демка](http://orloffv.ru/fotki/)

Возможности:
------------

получение последних фотографий `get_allPhotos`

получение всех альбомов у пользователя `get_albums`

получение фотографий в альбоме `get_albumPhotos($album_id)`

получение всех альбомов с обложками new! `get_albumsWithPreview`

прослойка кэширования


Пример:
------------

> $fotki = new YaFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS'), 'cache' => TRUE));

> var_dump($fotki->get_albums()); 