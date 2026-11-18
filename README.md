# Kurzy a přihlašování účastníků

Webová aplikace v Laravelu pro správu kurzů a účastníků a pro přihlašování účastníků do kurzů. Přihláška propojuje účastníka s kurzem a má vlastní stav.

## Funkce

- Vytváření, úprava a mazání kurzů (název, kód, kapacita, začátek).
- Vytváření, úprava a mazání účastníků (jméno, e-mail).
- Přihlášení účastníka do kurzu a správa stavů přihlášky.
- Zobrazení obsazenosti kurzu (například 3 / 10, volno 7) a seznamu přihlášených.
- Filtr kurzů podle data začátku a podle volných míst.

## Datový model

- **courses**: name, code (unikátní), capacity, starts_at
- **participants**: name, email (unikátní)
- **enrollments**: course_id, participant_id, enrolled_at, status

Dvojice course_id + participant_id je v tabulce enrollments unikátní.

## Pravidla a validace

- Kód kurzu je jedinečný (ukládá se velkými písmeny).
- Kapacita je celé číslo větší než 0 a nelze ji snížit pod počet aktivních přihlášek.
- E-mail účastníka musí být platný a jedinečný (ukládá se malými písmeny).
- Účastník se nemůže přihlásit do stejného kurzu dvakrát.
- Počet aktivních přihlášek nesmí překročit kapacitu. Kontrola běží v databázové transakci se zámkem řádku kurzu, takže ani souběžné požadavky kapacitu nepřekročí.
- Smazání kurzu nebo účastníka smaže i navázané přihlášky.

## Stavy přihlášky

- **enrolled**: aktivní přihláška, zabírá místo v kurzu. Lze ji změnit na cancelled nebo completed.
- **cancelled**: zrušená přihláška, místo je uvolněné. Lze ji obnovit na enrolled, ale jen pokud je v kurzu volné místo.
- **completed**: dokončený kurz. Je to konečný stav, další změna není možná.

Kapacitu zabírají pouze přihlášky ve stavu enrolled. Když se účastník po zrušení přihlásí znovu, původní přihláška se obnoví a nevznikne nový záznam.

## Požadavky

- PHP 8.2 nebo novější
- Composer
- SQLite (výchozí) nebo MySQL/MariaDB

## Instalace a spuštění

1. Naklonujte repozitář a vstupte do složky:

```bash
git clone https://github.com/Luke3F/kurzy_a_prihlasovani.git
cd kurzy_a_prihlasovani
```

2. Nainstalujte závislosti:

```bash
composer install
```

3. Vytvořte konfigurační soubor a klíč aplikace:

```bash
cp .env.example .env
php artisan key:generate
```

Na Windows v PowerShellu použijte místo `cp` příkaz `copy .env.example .env`.

4. Vytvořte prázdný soubor SQLite databáze:

```bash
touch database/database.sqlite
```

Na Windows v PowerShellu: `New-Item database/database.sqlite`.

5. Spusťte migrace a server:

```bash
php artisan migrate
php artisan serve
```

Aplikace poběží na http://127.0.0.1:8000 a hlavní stránka přesměruje na seznam kurzů.

### Jiná databáze (MySQL)

V souboru `.env` upravte řádky DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME a DB_PASSWORD, vytvořte prázdnou databázi a spusťte `php artisan migrate`.

## Automatická kontrola (testy)

```bash
php artisan test --filter=CourseEnrollmentTest
```

Test používá dočasnou databázi, takže vaše data nijak neovlivní. Vytvoří kurz, ověří jeho uložení v databázi a zkontroluje:

- jedinečnost kódu kurzu a neplatnou kapacitu (0 a desetinná čísla),
- platnost a jedinečnost e-mailu,
- zákaz dvojího přihlášení,
- hlídání kapacity a uvolnění místa po zrušení,
- povolené a zakázané změny stavů.

Všechny testy spustíte příkazem `php artisan test`.

## Struktura projektu

- `app/Models/`: Course, Participant, Enrollment (logika přihlašování a stavů)
- `app/Http/Controllers/`: CourseController, ParticipantController, EnrollmentController
- `database/migrations/`: migrace tabulek kurzů, účastníků a přihlášek
- `resources/views/`: šablony Blade
- `routes/web.php`: trasy aplikace
- `tests/Feature/`: CourseEnrollmentTest
