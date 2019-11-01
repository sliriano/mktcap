#!/bin/bash
while true
do
        php7 forex_table_db_update.php
        php7 crypto_table_db_update.php
        php7 stock_table_db_update_fallback.php
        sleep 60
done
