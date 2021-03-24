# Currency converter  
En aflevering til valgfaget PHP programmering og Open Source  
Programmet er en valutaomregner til personlig brug der kører over browseren.  
Dataen kommer fra: https://api.exchangeratesapi.io
  
## Start
Inden programmet kan køres skal en fil database.sqlite laves i database mappen og så skal migrations køres samt seeds.
Den sædvanlige .env fil til Laravel skal også være der hvor DB_CONNECTION skal ændres til sqlite og hvor den absolutte sti til database.sqlite skal angives i DB_DATABASE  
Når alt dette er gjort kan php artisan serve skrives i en terminal og derefter kan du besøge localhost:8000 for at se siden.
