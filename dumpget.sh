#!/bin/bash

php artisan iseed data_rows,data_types,menus,menu_items,translations,permissions,roles,permission_role,settings --force
# php artisan iseed users --force

LINENUM=$(cat database/seeders/PermissionRoleTableSeeder.php| wc -l)
ENABLENUM=`expr $LINENUM - 3 + 1`

sed -i -e $ENABLENUM"a \\\\\Schema::enableForeignKeyConstraints();" database/seeders/PermissionRoleTableSeeder.php
sed -i -e "17a \\\\\Schema::disableForeignKeyConstraints();" database/seeders/PermissionRoleTableSeeder.php
echo -e "\e[32m Fixed foreign key error \e[00m"

LINENUM=$(cat database/seeders/RolesTableSeeder.php| wc -l)
ENABLENUM=`expr $LINENUM - 3 + 1`

sed -i -e $ENABLENUM"a \\\\\Schema::enableForeignKeyConstraints();" database/seeders/RolesTableSeeder.php
sed -i -e "17a \\\\\Schema::disableForeignKeyConstraints();" database/seeders/RolesTableSeeder.php

echo -e "\e[32mPermissionRoleTable/RolesTable foreign key resolved \e[00m"