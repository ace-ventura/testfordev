# Тестовое задание spotix

Как его выполнить.

0. Делаем используя git.
1. Клонируйте репозиторий со всем содержимым к себе.
2. По вопросам в файле README.md под каждым из вопросов напишите свой ответ.
      Для каждого ответа делайте отдельный коммит
3. По блоку Разбор кода там написано где предлагается делать записи.

Результат выполнения тестового задания - ссылка на ваш репозиторий на github.

***

Задание состоит из трех частей
1. Вопросы общего характера по нашим типичным задачам
2. Несколько вопросов по php, mysql
3. Разбор типичного кода из crm

## Вопросы

1. Как бы вы решали проблему, если бы пользователи пожаловались, что какой-то раздел в crm "стал очень сильно тормозить". Опишите ваши шаги.
2. Как бы вы решали задачу "показывать на веб странице меняющиеся в реальном времени данные с температурных датчиков". Исходные данные: мы можем запустить на сервере
приложение на любом языке (php, nodejs, python) в котором в функции onSensorReceivedData() мы получаем данные о названии датчика и текущей температуре. Вопрос как сделать
следующие этапы доставки и отображения данных на веб странице.

### PHP и MySQL

1. Есть таблица товаров **tovar**, поля (id, name). Есть таблица значений свойств товара **tovar_prop** с полями (tovar_id, prop_id, prop_value). prop_id идентификатор свойств, список свойств (характеристик) товара с их названиями хранится в другой таблице и она нам не понадобится.

Задача: мы хотим сделать поиск товаров, которые содержат заданную подстроку в своем названии или в любом из свойств. Напишите SQL запрос. Можно использовать подзапросы.

1.1. Усложненный вариант (необязательно)
Учет минус-слов. Напишите запрос учитывающий желание пользователя исключить из выдачи товары, содержащие в своем названии или в любом из свойств заданное минус-слово. Можно использовать подзапросы.

1.2. Очень усложненный вариант (необязательно)
Учет сложного поиска с разбиением по словам. Поисковая фраза такова "гайка 43". Нужно найти товары у которых каждое слово из поисковой фразы содержится либо в названии товара либо в значении любого из свойств. Можно использовать подзапросы.

2. Оказалось, что две таблицы в базе **table1** (поля: id, somedata, ref_table2_id) и **table2** (поля: id, data, ref_table1_id) ссылаются друг на друга (связь один к одному). В системе есть два раздела, для первого основной является таблица **table1** для второго - **table2**.
В каждом из этих разделов подгружаются данные из дополнительной таблицы используя одну логику построения связи: из основной таблицы берется значение id записи в дополнительной и в дополнительной таблице ищется запись с этим id.
Оператор обнаружил, что нарушена логика связи данных. Для определенной записи **table1** есть ссылка на запись в **table2**. А в **table2** нет ссылки на эту запись из table1.
Как бы вы исправляли эту ситуацию?


3. Что не так в этом коде, как бы вы его переписали.
```php
$data = get_data_from_tovar_table($some_condition);  					// внутри функции SQL запрос к таблице товаров для выборки
foreach($data as $item) {
    $tovar_data[$item['id']] = get_tovar_properties_data($item['id']);	// внутри функции SQL запрос к таблице свойств товаров
}
```

## Разбор кода

В папке code есть несколько файлов из нашей crm. Они работают как составные части одного функционального блока. Просмотрите их. 
1. Предположите для чего нужен каждый из них. Напишите это в начале каждого файла.
2. Если есть части кода, которые вам не нравятся, напишите прямо в коде коментарий что бы вы там поменяли.
3. Если у вас на основе просмотра наших типичных файлов появились предложения что можно сделать в целом во всей системе, чтобы сделать ее лучше, напишите это здесь ниже.


