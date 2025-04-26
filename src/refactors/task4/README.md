### Testing

Задача:

- Выделить отдельные классы: User, UserRepository, Validator, Mailer
- Добавить интерфейсы для зависимостей
- Сделать класс UserManager тонким фасадом (или Use Case)

```bash
vendor/bin/phpunit -c src/refactors/task4/phpunit.xml
```
