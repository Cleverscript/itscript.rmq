# 1С-Bitrix модуль интеграции с брокером сообщений RabbitMQ

---

Пример класса консольной комманды, читающей очередь:
```
local/modules/itscript.rmq/lib/Commands/SomeQueueListenCommand.php
```
Константы:
- *EXCHANGE_NAME* - содержит наименование обменника
- *QUEUE_NAME* - содержит наименование очереди (она должна быть привязана к обменнику)
