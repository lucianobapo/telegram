# telegram

***First time run***
```shell
ssh telegram.ilhanet.com
cd ~/code/ && git clone https://github.com/lucianobapo/telegram.git && exit
rsync -rvztPhe ssh /home/luciano/code/telegram/.env.production telegram.ilhanet.com:code/telegram/.env
sudo ./permissions.sh
php artisan migrate
```

***Updating commits***
```shell
cd ~/code/erpnet-v5/packages/erpnet-delivery && git cmt
cd ~/code/erpnet-v5/packages/erpnet-migrates && git cmt
cd ~/code/erpnet-v5/packages/erpnet-models && git cmt
cd ~/code/erpnet-v5 && composer update

git cmt && ssh telegram.ilhanet.com
cd ~/code/erpnet-v5/ && git pull && composer install && exit
```