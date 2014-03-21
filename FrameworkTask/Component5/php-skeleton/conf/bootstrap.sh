#!/usr/bin/env bash

apt-get update

# some sysutils
apt-get install debconf-utils mailutils

# if apache2 does no exist
if [ ! -f /etc/apache2/apache2.conf ];
then

        # Install MySQL
        echo 'mysql-server-5.5 mysql-server/root_password password toor' | debconf-set-selections
        echo 'mysql-server-5.5 mysql-server/root_password_again password toor' | debconf-set-selections
        apt-get -y install mysql-client mysql-server-5.5

        # Install Apache
        apt-get -y install apache2

        # Install Git
        apt-get -y install git

        # Install PHP 
        apt-get -y install php5 libapache2-mod-php5 php-apc php5-mysql php5-dev curl

        # Install OpenSSL
        apt-get -y install openssl

        # Install PHP pear
        apt-get -y install php-pear

        # Install sendmail
        cat /vagrant/conf/postfix.preseed | debconf-set-selections
        apt-get -y install postfix

        # Install CURL dev package
        apt-get -y install libcurl4-openssl-dev

        # Install libssl, needed for zendbugger
        apt-get install libssl0.9.8

        # Install PECL HTTP (depends on php-pear, php5-dev, libcurl4-openssl-dev)
        apt-get -y install make

        # Enable mod_rewrite    
        a2enmod rewrite

        # Enable SSL
        a2enmod ssl

        # Add www-data to vagrant group
        usermod -a -G vagrant www-data

        apt-get -y install imagemagick

        apt-get -y install ant
        apt-get -y install php5-xsl
        apt-get -y install php5-curl
        apt-get -y install php5-xdebug
        apt-get -y install php-http

        echo "xdebug.remote_enable=1" >> /etc/php5/conf.d/xdebug.ini
        echo "xdebug.remote_connect_back = On" >> /etc/php5/conf.d/xdebug.ini

        #pear config-set auto_discover 1
        #pear install pear.phpqatools.org/phpqatools
        #pear install -f pear.netpirates.net/phpDox
        #pear install -f pear.netpirates.net/Autoload


        # pear packages only needed for dev
        pear config-set auto_discover 1
        pear install --alldeps pear.phpunit.de/PHPUnit
        pear install --alldeps pear.phpunit.de/PHP_CodeSniffer
        pear install --alldeps pear.phpunit.de/phpcpd
        pear install --alldeps pear.phpunit.de/phploc
        pear install --alldeps pear.phpunit.de/PHP_CodeBrowser
        pear install --alldeps pear.pdepend.org/PHP_Depend
        pear install --alldeps pear.phpmd.org/PHP_PMD
        pear install --alldeps  -f pear.netpirates.net/phpDox
        pear install --alldeps -f pear.netpirates.net/Autoload



        # Add www-data to vagrant group
        usermod -a -G vagrant www-data
        usermod -a -G www-data vagrant

         # Copy all the conf files present in the conf/apache2 folder to the host's etc/apache2 folder
        rsync -av /vagrant/conf/apache2/* /etc/apache2/
        # Do the same for the PHP ini files as well
        rsync -av /vagrant/conf/php5/* /etc/php5/

        # Finally restart apache to apply our changes
        /etc/init.d/apache2 restart
         # And mysql as well
        service mysql restart

         # And clean up apt-get packages
        apt-get -y clean

        cd /var/www
        curl -s https://getcomposer.org/installer | php
        php composer.phar -n create-project symfony/framework-standard-edition /var/www/symfony 2.3.7

fi

