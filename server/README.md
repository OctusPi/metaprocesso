# Gestor Contratos

## Testing

All the tests are runned on a in-memory SQLite database, migrating on setUp and dropping on tearDown. 

According to Laravel official documentation, for executing the tests on a async approach, run:

```zsh

php artisan test --parallel

```