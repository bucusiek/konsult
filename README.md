# Konsultacje

## Założenia aplikacji

- Aplikacja napisana przy pomocy frameworka PHP Slim (warunek konieczny)
- Konsultacje odbywają się 1-2/tydzień (ustalone z góry przez administratora)
- Użytkownik po wejściu na stronę wybiera określony termin, przybliżony czas trwania konsultacji oraz przedmiot, wpisuje swoje imię oraz nazwisko (bez logowania)
- W przypadku gdy sprawa nie dotyczy przedmiotu należy wybrać opcję "inne"
- Przybliżony czas ustalamy z dokładnością do 10 minut i jest to minimalny czas trwania konsultacji dla studenta
- Po wysłaniu formularza wysyłany jest mail do administratora z prośbą o zalogowanie się i potwierdzenie terminu konsultacji
- Administrator po zalogowaniu może zaakceptować odmówić lub zaproponować inny termin
- Po zaakceptowaniu informacja o zajętości pojawia się na stronie głównej
- Planowanie możliwe jest tylko na określony miesiąc

## Uruchamianie aplikacji

### Serwer
```
php -S localhost:1111 -t public public/index.php
```

### Klient
```
php -S localhost:8080 -t public
```
