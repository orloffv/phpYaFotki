# PHP class для работы с [Яндекс.Фотки](http://fotki.yandex.ru), version 0.1 (release)

Рабочая [демка](http://orloffv.ru/fotki/)

Пример:

> $fotki = new yFotki(array('login' => 'vitaly.orloff', 'sizes' => array('XXS')));

> var_dump($fotki->get_albums()); 