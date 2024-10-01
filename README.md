# Simple-web-pdns-page
Web interface for pdns server

pdns-gui/
│
├── config.php           # Файл конфигурации с логином и паролем
├── index.php            # Основной файл (главная страница с статусом работы сервера)
├── add_zone.php         # Добавление новой зоны
├── test_zone.php        # Тестирование зон
├── stats.php            # Статистика работы сервера
├── login.php            # Страница авторизации
├── logout.php           # Выход из системы
├── style.css            # Стили для веб-интерфейса
└── templates/
    ├── header.php       # Шапка сайта и меню (проверяет авторизацию)
    └── footer.php       # Подвал сайта