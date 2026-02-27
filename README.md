# Első lépés

Cloneozd a github repot.

# Clone után másold át az .env.examplet a .envbe

```bash
 cp .env.example .env 
```

# Ezután módpsítsd a .env fájlt

Sseréld ki a "DB_CONNECTION" részt erre:

```bash 
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=schoolBackend
DB_USERNAME=sail
DB_PASSWORD=123
```

végül a .env végére illeszd be ezt:
```bash
WWWGROUP=1000
WWWUSER=1000
```

# Composer install

a vscodeban a mappa rootjában addki ezt a parancsot:
```bash
docker run --rm `
  -v ${PWD}:/app `
  -w /app `
  composer install
```

# Buildeld a containereket
Ez elég sok időt igénybe vehet, ne lepődj meg (10-30p)
szintén vscode-ban:
```bash
docker compose up -d --build
```
# Project setup
Ha a fenti pont sikeres volt, futtasd ezt a két parancsot vscode-ban:
```bash
docker compose exec schoolBackend1 php artisan key:generate

docker compose exec schoolBackend1 php artisan migrate 
```

# Ha minden sikeres volt 2 elérésed lesz:

phpMyAdmin: http://localhost:8081/
backend: http://localhost:8080/api/endpoint