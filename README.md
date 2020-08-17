# docker_test
Деплой 
docker-compose up 
docker-compose exec php bash
  - composer update
  - php /app/bin/console doctrine:migrations:migrate
  
 
 Контроллер админки:
 
 PUT http://localhost:8150/admin/article - добавление новости
     {"title": "677werwerwererwerrer76", "publishedAt" : "2020-08-10 03:54:43", "short_description": "короткое описание", "description": "текст новости", 
     "isActive": true, "isHide": false}
     
 POST http://localhost:8150/admin/article/{id}/edit -  обновление новости
     {"title": "677werwerwererwerrer76", "publishedAt" : "2020-08-10 03:54:43", "short_description": "короткое описание", "description": "текст новости", 
     "isActive": true, "isHide": false}
     
 DELETE http://localhost:8150/admin/article/{id} -  удаление новости
    
    
    Контроллер Фронт-части:
    
GET http://localhost:8150/admin/article/{page}/{limit} - добавление новости
      RESPONSE {status: (int), 'page' => (int), 'articles' => (array) } 
     
POST http://localhost:8150/admin/article/{slug} -  получение новости
  RESPONSE {status: (int), 'article' => (array)}
     
    Карта сайта генерируется через крон. Флаг необходимости перегенерации карты выставляется в подписчике событий. Для передачи информации в крон используется редис.
 
