# Rozetka Parser

Система парсингу даних з Rozetka з використанням Laravel та Filament Admin.

## Передумови
- PHP 8.1+
- Composer 2.2+
- MySQL 5.7+
- Розширення PHP: cURL, DOM, XML
- Веб-сервер (Nginx/Apache)

## Інструкція зі встановлення

### 1. Клонування репозиторію
```bash
cd /шлях/до/кореневого/каталогу
git clone https://github.com/lestercitycrom/rztk.git .
```

### 2. Налаштування середовища

**Варіант 1 - Ручне налаштування:**
1. Створіть файл конфігурації:
```bash
cp .env.example .env
```

2. Встановіть безпечні права доступу:
```bash
chmod 640 .env  # Власник: читання+запис, Група: читання, Інші: доступ закрито
```

3. Відкрийте файл у зручному редакторі:
```bash
nano .env  # або використовуйте Sublime Text/VSCode/Notepad++
```

**Варіант 2 - Консольне налаштування:**
```bash
cp .env.example .env && \
chmod 640 .env && \
sed -i "s|APP_URL=.*|APP_URL=https://rozetka.dmcp.online|g" .env && \
sed -i "s/DB_HOST=.*/DB_HOST=json.mysql.tools/g" .env && \
sed -i "s/DB_DATABASE=.*/DB_DATABASE=json_rozetka/g" .env && \
sed -i "s/DB_USERNAME=.*/DB_USERNAME=json_db/g" .env && \
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=h3hmN92N9tBU/g" .env
```

**Обов'язкові налаштування у `.env`:**
```ini
APP_ENV=production
APP_DEBUG=false
APP_KEY=  # Буде згенеровано на наступному кроці
```

> 💡 Порада: Ніколи не залишайте файл `.env` з правами доступу 777 або доступним для всіх користувачів!

```

### 3. Встановлення залежностей
```bash
composer install --optimize-autoloader --no-dev
```

### 4. Генерація ключа додатку
```bash
php artisan key:generate
```

### 5. Міграція бази даних
```bash
php artisan migrate --force
```

### 6. Створення адміністратора
```bash
php artisan make:filament-user
```
➔ Введіть логін, email та пароль для адмін-панелі

---

## Налаштування Cron-завдань

### Для автоматичного парсингу додайте у cron:
```bash
* * * * * /usr/bin/wget -O - -q -t 1 'https://ваш-домен/cron/rozetka'
```

**Як додати через crontab:**
1. Відкрийте редактор cron:
```bash
crontab -e
```
2. Вставте рядок з командою
3. Збережіть та закрийте редактор (`Ctrl+X` → `Y` → Enter)

---

## Додаткова інформація

### Права доступу
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Безпека
1. Видаліть файл `.env.example` після налаштування
2. Забороніть доступ до каталогу `/vendor` через веб-сервер
3. Налаштуйте HTTPS для всіх запитів
