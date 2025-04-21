### Testing

```bash
vendor/bin/phpunit -c src/refactors/task1/phpunit.xml
```

- Separation of Concerns: OrderItem class handles item logic, making Order class cleaner.
- Better Type Safety: float for prices ensures accuracy.
- Improved Maintainability: The findItem method replaces flag-based logic.
- Avoids References (&): No unintended modifications to elements.
