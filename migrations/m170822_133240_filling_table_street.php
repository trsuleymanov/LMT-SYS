<?php

use yii\db\Migration;
use app\models\City;
use yii\base\ErrorException;

class m170822_133240_filling_table_street extends Migration
{
    public function up()
    {
        $al_city = City::find()->where(['name' => 'Альметьевск'])->one();
        if($al_city == null) {
            throw new ErrorException('Не найден город Альметьевск');
        }

        $aCityes = [
            '0 - Трасса'
            ,'1 - Город'
            ,'Авзала'
            ,'Автодорожная '
            ,'Автомобилистов'
            ,'Аграрная'
            ,'Агропоселок район'
            ,'Агрохимиков'
            ,'Акмуллы'
            ,'Алиша'
            ,'Амбулаторная'
            ,'Аминова'
            ,'Атласи'
            ,'Ахмадиева'
            ,'Ахунова'
            ,'Базовая'
            ,'Балакиной'
            ,'Банковский пер'
            ,'Баруди'
            ,'Батырши'
            ,'Бахорина'
            ,'Белоглазова'
            ,'Березовая'
            ,'Бигаш'
            ,'Бигиева'
            ,'Булгакова'
            ,'Валиханова'
            ,'Гагарина'
            ,'Газовая'
            ,'Галеева'
            ,'Гали'
            ,'Галиуллина'
            ,'Гамирова'
            ,'Гарифуллина'
            ,'Гатина'
            ,'Гафиатуллина'
            ,'Гафури'
            ,'Гвардейская'
            ,'Геофизическая'
            ,'Герцена'
            ,'Гильмутдинова'
            ,'Губайдуллиной'
            ,'Гумилева'
            ,'Гыйльми'
            ,'Девятаева'
            ,'Державина'
            ,'Джалиля'
            ,'ДОСААФ район'
            ,'ДСРК район'
            ,'Елховская'
            ,'Ершова'
            ,'Жукова'
            ,'Жуковского'
            ,'Заводская'
            ,'Загитова'
            ,'Загородная'
            ,'Зай пер'
            ,'Зайный пер'
            ,'Заозерная'
            ,'Западная'
            ,'Заречная'
            ,'Зарипова пр'
            ,'Заря'
            ,'Заслонова'
            ,'Зая'
            ,'Зеленая'
            ,'Ибрагимова'
            ,'Индустриальная'
            ,'Интернациональная'
            ,'Исхаки'
            ,'Калинина'
            ,'Кальметова'
            ,'Кандалы'
            ,'Кандалыя'
            ,'Карима'
            ,'Кашаева'
            ,'Кошевого'
            ,'Лазо'
            ,'Ленина пл'
            ,'Ленина'
            ,'Лермонтова'
            ,'Лесная'
            ,'Лесной пер'
            ,'Лобачевского'
            ,'Ломоносова'
            ,'Луговая'
            ,'Магистральная'
            ,'Макаренко'
            ,'Максуди'
            ,'Малая'
            ,'Малые Пруды'
            ,'Марджани'
            ,'Мусаева'
            ,'Мухаметзянова'
            ,'Назми'
            ,'Нижняя Мактама пгт.'
            ,'Новикова'
            ,'Объездная'
            ,'Объездной тракт'
            ,'Овражная'
            ,'Окольная'
            ,'Октябрьская'
            ,'Октябрьский пер'
            ,'Островского'
            ,'Первомайская'
            ,'Песочная'
            ,'Петуховка район'
            ,'Пионерская'
            ,'Победы'
            ,'Поворотная'
            ,'Подгорная'
            ,'Подлесная'
            ,'Подлужная'
            ,'Подстанция'
            ,'Полевая'
            ,'Попова'
            ,'Поселковая'
            ,'Почтовая'
            ,'Прибрежная'
            ,'Пригородная'
            ,'Пригородный пер'
            ,'Производственная'
            ,'Пролетарская'
            ,'Промзона'
            ,'Промышленная'
            ,'Пугачева'
            ,'Пушкина'
            ,'Радио'
            ,'Радищева'
            ,'Рахимова'
            ,'Репина'
            ,'Речная'
            ,'Роз пр'
            ,'РТС район'
            ,'Рябиновая'
            ,'Сабирова'
            ,'Садовая'
            ,'Садовый пер'
            ,'Садыковой'
            ,'Сайдашева'
            ,'Саттарова'
            ,'Свердлова'
            ,'Свободы'
            ,'Севастопольская'
            ,'Северная'
            ,'Сиреневая'
            ,'Советов'
            ,'Советская'
            ,'Советский пер'
            ,'Солнечная'
            ,'Спартака'
            ,'Спортивная'
            ,'Станичная'
            ,'Степная'
            ,'Столбовая'
            ,'Строителей пр'
            ,'Строителей'
            ,'СУ-2 район'
            ,'Суворова'
            ,'Сулеймановой'
            ,'Сургутская'
            ,'Сыртлановой'
            ,'Сююмбике'
            ,'Тагирова пер'
            ,'Тагирова'
            ,'Такташ'
            ,'Татарстан'
            ,'Тельмана'
            ,'Техническая'
            ,'Тимергалиева'
            ,'Тимирязева'
            ,'Тимяшева'
            ,'Тинчурина'
            ,'Тихоновка с.'
            ,'Товарищеская'
            ,'Токарликова пер'
            ,'Токарликова'
            ,'Толстого'
            ,'Торцевая'
            ,'Труда'
            ,'Трудовая'
            ,'Тукая пл'
            ,'Тукая пр'
            ,'Тукая'
            ,'Тупиковая'
            ,'Тургенева пер'
            ,'Тургенева'
            ,'Туфана'
            ,'Тухватуллина'
            ,'Тюленина'
            ,'Уральская'
            ,'Урманче'
            ,'Урожайная'
            ,'Усманова'
            ,'Успенского пер'
            ,'Успенского'
            ,'Утыз Имяни'
            ,'Фаезханова аллея'
            ,'Фахретдина'
            ,'Фрунзе'
            ,'Фурманова'
            ,'Хамидуллина'
            ,'Худякова'
            ,'Хузангая'
            ,'Цветочная'
            ,'Центральная'
            ,'Цеткин'
            ,'Цеховая'
            ,'Чайкиной'
            ,'Чайковкого'
            ,'Чапаева'
            ,'Чернышевского'
            ,'Чехова'
            ,'Чкалова пер'
            ,'Чкалова'
            ,'Чулпан'
            ,'Шевченко'
            ,'Шишкина'
            ,'Школьная'
            ,'Шоссейная'
            ,'Шоссейный пер'
            ,'Энгельса'
            ,'Энергетиков'
            ,'Энтузиастов'
            ,'Эрьзи'
            ,'Южная'
            ,'Юсупова'
            ,'Юсупова'
            ,'Ямашева'
        ];

        $insertRows = [];
        foreach($aCityes as $city) {
            $insertRows[] = [
                $al_city->id,
                $city
            ];
        }

        $this->BatchInsert('street', ['city_id', 'name'], $insertRows);
    }

    public function down()
    {
        $this->truncateTable('street');
    }
}