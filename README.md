# Radio-Nice API

Все ответы возвращаются в JSON с такими общими полями, как: message, status. При успешном выполнении status = "success", при неуспешном status = "error". В поле message можно получить подробное сообщение об ошибке и об успешном выполнении запроса.

В некоторых запросах необходимо передавать токен аутентификации в хедере (Authorization: Bearer {token}) - такие запросы будут помечены ключевым словом **AUTH**.

Также могут встречаться адреса такого вида - **/api/something/{param_id}**. Вместо квадратных скобок необходимо подставить значение. Если в квадратных скобках есть знак вопроса - **{param_id?}** - это значит, что параметр необязательный


## Аутентификация
  ### Регистрация 
  * POST /api/register 
 
  **Аргументы:**
  * email
  * password (минимум 8 символов)
  * password_again
  * name (максимум 20 символов)
  * surname (максимум 32 символа)
  
  **Возвращает:**
  * user - объект с данными пользователя
  * token - токен пользователя для передачи в хедере Authorization
  
  <br/>
  
  ### Вход 
  * POST /api/login
  
  
  **Аргументы:**
  * email
  * password
  
  **Возвращает:**
  * user - объект с данными пользователя
  * token - токен пользователя для передачи в хедере Authorization
  
  <br/>
  
  ### Выход из аккаунта 
  * POST /api/logout
  * Возвращает только сообщение об успешном удалении токена пользователя. (Делается для безопасности, чтобы токеном больше нельзя было воспользоваться)
  
  <br/>
  
  ### Сменить пароль
  * POST /api/password/forgot 
  
  **Аргументы:**
  * email
  * password
  * password_confirmation
  
  Дальше система отправляет письмо с ссылкой на активацию нового пароля
___

## Пользователи
  ### Получить данные текущего пользователя 
  * GET /api/user/current (**AUTH**). 
  
  **Возвращает:**
  * user - объект с данными текущего пользователя
  
  <br/>
  
  ### Получить данные всех пользователей/определенного пользователя - 
  * GET /api/user/{user_id?} (**AUTH, ADMIN**). 
  
  **Возвращает:**
  * user - объект с данными пользователя(-ей)
  
  <br/>
  
  ### Отредактировать данные пользователя
  * POST /api/user/current/edit (**AUTH**)
  
  **Аргументы (все параметры необязательны):**
  * name
  * surname
  * email
  * avatar - изображение
  
  **Возвращает**
  * user - объект с данными пользователя

  <br/>

  ### Получить id для соц сетей
  * GET /api/user/current/socials (**AUTH**)

  **Возвращает:**
  * socials
___

## Реклама
  ### Получить рекламные баннеры музыкантов
  * GET /api/banner/artist/{banner_id?}. 
  
  **Возвращает:**
  * artist_adverts - объект с данными баннера(-ов)
  
  <br/>
  
  ### Создать новый баннер музыканта
  * POST /api/banner/artist/save (**AUTH, ADMIN**). 
  
  **Аргументы:**
  * banner - изображение
  * artist - имя музыканта
  * genre - жанр
  * description - описание (необязательный)
  * url - ссылка, на которую будет вести баннер
  
  **Возвращает:**
  * message
  
  <br/>
  
  ### Удалить баннер музыканта
  * DELETE /api/banner/artist/delete/{banner_id} (**AUTH, ADMIN**). 
  
  **Возвращает:**
  * message
  
  <br/>
  
  ### Редактировать баннер
  * POST /api/banner/artist/edit/{banner_id}
  
  **Аргументы (все необязательные):**
  * banner - картинка
  * artist
  * genre
  * description
  * url
  
  **Возвращает:**
  * message
  
___

## Трансляции
  ### Получить информацию о трансляциях
  * GET /api/stream/{stream_id?}. 
  
  **Возвращает:**
  * streams - информация о траснляции(-ях)
  
  <br/>
  
  ### Получить URL трансляции
  * GET /api/stream/url/{stream_id}
  
  **Возвращает:**
  * stream_url
  
  <br/>
  
  ### Получить текущий трек для трансляции
  * GET /api/stream/track/{stream_id} (**AUTH**)
  
  **Возвращает:**
  * current_track - объект с информацией о текущем треке
  
  <br/>
  
  ### Получить последние 10 треков для трансляции
  * GET /api/stream/history/{stream_id} (**AUTH**)
  
  **Возвращает:**
  * tracks
  
  <br/>
  
  ### Создать новую трансляцию
  * POST /api/stream/save (**AUTH, ADMIN**). 
  
  **Аргументы:**
  * server_id - id сервера из админки радио
  * video_stream_link - ссылка на видео-трансляцию (необязательный параметр)
  * title - название трансляции
  * genre - жанр
  * description - описание (необязательный параметр)
  * main_image - главное изображение
  * thumbnail - иконка
  
  **Возвращает:**
  * message
  
  <br/>
  
  ### Удалить трансляцию
  * DELETE /api/stream/delete/{stream_id}. 
  
  **Возвращает:**
  * message
  
  <br/>
  
  ### Отредактировать трансляцию
  * POST /api/stream/edit/{stream_id}
  
  **Аргументы (все параметры необязательны):**
  * server_id - id сервера из админки радио
  * video_stream_link - ссылка на видео-трансляцию
  * title - название трансляции
  * genre - жанр
  * description - описание
  * main_image - главное изображение
  * thumbnail - иконка 
  
  **Возвращает:**
  * message
  
  <br>
  
  ### Поставить лайк/дизлайк
  * POST /api/stream/track/(dis)like/{track_id} (**AUTH**)
  * track_id - поле all_music_id из запроса текущего трека
  
  **Возвращает:**
  * up - количество лайков
  * down - количество дизлайков
  * result - сообщение о результате запроса
  
  
___

## Премиум подписки
  ### Получить все существующие подписки
  * GET /api/subscription/get
  
  **Возвращает:**
  * subscriptions
  
  <br>
  
  ### Создать новую подписку
  * POST /api/subscription/save (**AUTH, ADMIN**)
  
  **Аргументы:**
  * period_in_months - период действия подписки в месяцах
  * price
  
  **Возвращает:**
  * message
  
  <br>
  
  ### Редактировать подписку
  * POST /api/subscription/edit/{subscription_id}
  
  **Аргументы (все необязательные):**
  * period_in_months
  * price
  
  **Возвращает:**
  * message
  
  <br>
  
  ### Удалить подписку
  * DELETE /api/subscription/delete/{subscription_id}
  
  **Возвращает:**
  * message
  
  <br>
  
  ### Купить подписку
  * GET /api/subscription/purchase/{subscription_id} (**AUTH**)
  
  **Возвращает:**
  * redirect_url - урл, на который нужно отправить пользователя для оплаты
___

## Слушатели
   ### Получить геолокацию слушателей
   * GET /api/listeners/geo

   **Возвращает:**
   * listeners

___
    
## Почта
   ### Форма обратной связи
   * POST /api/email/feedback

   **Аргументы:**
   * email
   * name
   * message

   **Возвращает:**
   * message

   <br/>

   ### Отправить трек
   * POST /api/email/offer/track

   **Аргументы:**
   * file - аудиофайл
   
   **Возвращает:**
   * message

___

### Тайм трекер
   ## Проверить наличие допуска к прослушиванию и обновить прошедшее время
   * POST /api/time-tracker/update

   **Аргументы:**
   * seconds - прошедшее время в секундах, для обновления времени в бд (Необязательный аргумент)

   **Возвращает:**
   * blocked - TRUE или FALSE
   * started - время начала прослушивания радио

___

### Вход через соц сети
  **Возможные значения переменной driver:**
  * vkontakte
  * facebook
  * google
  * yandex
  * mailru

  ## Перенаправить пользователя на страницу входа в соц сети
  Необходимо просто создать ссылку вида href=/api/auth/social/{driver}, где driver - это одно из указанных выше значений
  
  После входа пользователь будет перенаправлен на страницу https://radio-nice.ru/#/auth-social, со следующими GET параметрами: 
  * driver
  * token
  
  Далее с этой страницы можно будет отправить такие запросы:
  * POST /api/auth/social/{driver}/execute - для выполнения входа на сайт
  * POST /api/auth/social/{driver}/link-account (**AUTH**) - для привязки аккаунта к соц сети

  В обоих случаях необходимо передать в теле запроса token, полученный из GET параметра
  
  Оба запрос вернут объект с данными пользователя
___
