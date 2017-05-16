Welcome to StackEdit!
===================


Hey! I'm your first Markdown document in **StackEdit**[^stackedit]. Don't delete me, I'm very helpful! I can be recovered anyway in the **Utils** tab of the <i class="icon-cog"></i> **Settings** dialog.

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
    
    # Ignore flat catalog tables
    php -f shell/generate_tiny_dump.php -- --ignore-flat-catalog
    
    # Ignore only logs and reports
    php -f shell/generate_tiny_dump.php

After executing the php script you need to execute the resultant bash script:

    sh shell/dump_data.sh
    
This will generate a compressed file on your project root with both sql scripts on it.
