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

## TODO
- TODO scooter, change url to no required. Created date idem?
- TODO Implement all application and API classes
- TODO Implement auth and roles permissions
- TODO Implement RabbitMq event, write and read

### Current Progress

ESTO ESTA STASH: ScooterSearchController he añadido que acepte price pero faltaria añadir que acepte currency, y despues adaptar el servicio y el repo y todos sus tests...

