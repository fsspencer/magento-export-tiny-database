Magento Tiny SQL Dump
===================


This is a simple shell script built for magento with the purpose of generate a tiny version of the database in two separated sql scripts, one for schema and the other for data.
----------


Install
-------------

Copy and paste the shell directory in your root project directory. Our shell directory contains a simple php script which generates a bash script in order to perform the dump files (one for schema and other for data).

> **Requirements:**

> - PHP should be accesible via command line using the command "php".
> - Mysqldump command should be usable from command line.

----------


Options
-------------

By default our script will ignore all the data from log tables and report tables but also offers the ability to ignore orders or flat catalog tables by using the following arguments.

    # Ignore orders 
    php -f shell/generate_tiny_dump.php -- --ignore-orders

    # Ignore URL rewrites
    php -f shell/generate_tiny_dump.php -- --ignore-url-rewrites
    
    # Ignore flat catalog tables
    php -f shell/generate_tiny_dump.php -- --ignore-flat-catalog
    
    # Ignore everything above
    php -f shell/generate_tiny_dump.php -- --ignore-all
    
    # Ignore only logs and reports
    php -f shell/generate_tiny_dump.php

After executing the php script you need to execute the resultant bash script:

    sh shell/dump_data.sh
    
This will generate a compressed file on your project root with both sql scripts on it.
