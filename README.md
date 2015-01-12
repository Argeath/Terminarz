# Terminarz

Wykonanie przez Dominik Kinal (kinaldominik@gmail.com) dla Zespół Szkół Łączności w Gdańsku ( Technikum nr 4 w Gdańsku )

Instalacja:
 Schema bazy danych MySQL znajduje się w terminarz.sql.
 W db.php trzeba ustawić dane do bazy danych.

TODO:
- lepszy system błędów (np. przy dodawaniu wpisów lub zastępstw)
- lepsze wspomaganie dla starszych przeglądarek ( ehh, ten Internet Explorer ;c )
- przenieść przedmioty do bazy danych( z plików dodaj.php i nowezast.php )
- czyszczenie bazy danych(wspisów, zastępstw i numerków), bo się dość szybko rozrastają
- limity nowego hasła przy zmianie (niepuste, min. 3 znaki itd.)

INFO:
- nie ma kategorii Sprawdzian ( ma nie być )
- Konto "usunięte" ma pole `dostep` = 0
- Konto administratora ma pole `dostep` = 2
- W przypadku zmiany listy przedmiotów, trzeba to zrobić w plikach dodaj.php i nowezast.php
- lista przedmiotów w nowezast.php ma dodatkowy przedmiot NI - Nauczanie indywidualne
- po zmianie hasła przez użytkownika, stare hasło zostaje zapisane do pola `starehaslo`, gdyby trzeba było przywrócić hasło, po przypadkowej zmianie
- Szczęśliwy numerek działa w ten sposób, że generuje losowo numery ( 1 - 30 (reszta ma pecha ;c )) dla całego przyszłego tygodnia,
wyświetla numerki tylko na "dzisiaj",
dany numer jest raz na miesiąc ( po zużyciu puli numerów, ustawia wszystkim pole `reset` na 1 i leci pula od początku)
- Łączenia klas ( dla zastępstw, np. na specjalizacjach łączone grupy z klas 3A i 3B) robimy w ten sposób:
- dodajemy nową klasę (np. 3AB) z polem `laczona` = 1 ( Dzięki temu nie wyświetla się w liście klas terminarza)
- Administrator ma możliwość dodania powiadomienia w terminarzu dla wszystkich klas poprzez kategorie "Powiadomienie"
