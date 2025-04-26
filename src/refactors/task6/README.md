### Testing

Задача:

- Отдели логику корзины от хранения (сессия → CartStorageInterface)
- Вынеси доступ к товарам в ProductRepository
- Сделай отдельную сущность CartItem и CartService
- Добавь DI и абстракции для тестируемости

```bash
vendor/bin/phpunit -c src/refactors/task6/phpunit.xml
```
