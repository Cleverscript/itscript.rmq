# 1С-Bitrix модуль интеграции с брокером сообщений RabbitMQ

---

### 1. Установка модуля
- Загрузите модуль в /local/modules и установите стандартным образом из админки

---

### 2. Установка Supervisor на BitrixVM 9 через pip

Для того что бы модуль мог считывать сообщения из очереди и обрабатывать их мгновенно и в несколько потоков, требуется 
держать поднятыми несколько PHP процессов, читающих очередь. Для этого понадобится установить Supervisor.

2.1. Установи Python и pip (если ещё не установлены):
```bash
sudo dnf install python3 python3-pip -y
```
2.2. Установи Supervisor:
```bash
sudo pip3 install supervisor
```
После установки supervisord и supervisorctl будут расположены обычно в /usr/local/bin.

2.3. Создай конфигурационный файл:
```bash
echo_supervisord_conf | sudo tee /etc/supervisord.conf
```

2.4. Добавь поддержку директорий с .ini-файлами:
Открой конфиг:
```bash
vim /etc/supervisord.conf
```
В самый конец добавь:
```code
[include]
files = /etc/supervisord.d/*.ini
```
Создай директорию, если её нет:
```bash
sudo mkdir -p /etc/supervisord.d
```

2.5. Запуск Supervisor (в ручную)
```bash
sudo supervisord -c /etc/supervisord.conf
```
Если вылетает ошибка:
Способ 1: Указать полный путь
```bash
sudo /usr/local/bin/supervisord -c /etc/supervisord.conf
```
И так же для supervisorctl:
```bash
sudo /usr/local/bin/supervisorctl
```
Способ 2: Добавить /usr/local/bin в PATH для sudo
```bash
sudo visudo
```
Найди или добавь строку /usr/local/bin (обычно ближе к началу или концу):
```code
Defaults secure_path = /usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
```
Сохрани и выйди. Теперь sudo supervisord будет работать

2.6. Проверка запущен ли супервизор с помощью supervisorctl
```bash
sudo /usr/local/bin/supervisorctl status
```
Если не запущен — будет ошибка вроде:
```bash
unix:///tmp/supervisor.sock no such file
```

---

### 3. Конфигурация для сервиса который будет запускать Supervisor

3.1. Создаем файл конфига
```bash
sudo vim /etc/supervisord.d/itscript-rmq-console-worker.ini
```
И вставляем код
```bash
[program:itscript-rmq-console-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/bitrix/www/local/modules/itscript.rmq/lib/Tools/console.php queue:listen
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=5
redirect_stderr=true
stderr_logfile=/var/log/itscript-rmq-console-workererr.log
stdout_logfile=/var/log/itscript-rmq-console-worker.log
user=bitrix
```
Параметр `numprocs` отвечает за количество одновременно запущенных процессов скрипта console.php
3.2. После добавления конфигурации перезапустите Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
```
или
```bash
sudo supervisorctl reload
```

После этого в htop можно увидеть запущенные процессы

---

### 4. Создаем свой класс-комманду

Пример класса консольной комманды, читающей очередь:
```
local/modules/itscript.rmq/lib/Commands/SomeQueueListenCommand.php
```
Константы:
- *EXCHANGE_NAME* - содержит наименование обменника
- *QUEUE_NAME* - содержит наименование очереди (она должна быть привязана к обменнику)
