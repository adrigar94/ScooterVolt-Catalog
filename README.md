[![Test](https://github.com/adrigar94/ScooterVolt-Catalog/actions/workflows/test.yml/badge.svg)](https://github.com/adrigar94/ScooterVolt-Catalog/actions/workflows/test.yml)

# ScooterVolt-Catalog
The Catalog service is a microservice in the ScooterVolt platform, for storing, listing, and filtering the ads posted by users. This service is built using Symfony.

## API Reference

Is avaible in ```https://localhost:8000/api/doc```


## Commands to be executed periodically 

| Command | Command Description | recommended frequency |
| ------- | ------------------- | --------------------- |
| ./bin/console event-domain:scooter:upsert | Listen to scooter updates and creations. Execute asynchronous actions.| Continually. Listen to events |
| ./bin/console event-domain:scooter:generate-events-update-price-exchanges | generate domain events for updating price exchanges on scooters | Daily |
| ./bin/console event-domain:scooter:update-price-exchange | Listen to requests for price exchange updates for scooter events | Continually. Listen to events |

## Testing

## Working
- [TODO] Scooter Upsert Controller tests
- [TODO] Implement auth and roles permissions
- [TODO] scooter, change url to no required. Created date idem? (quiza el creator service que no sea obligatorio pero en la clase scooter si)


