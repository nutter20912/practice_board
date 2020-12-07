### 留言板

---

#### Setup

##### step 1: install docker & clone project
```
# 1.install docker
# https://docs.docker.com/docker-for-mac/install

# 2.clone project
git clone http://gitlab.infinity/training/paul_chou.git
```

##### step 2 : build & run docker
```
# 1.build docker image
cd /paul_chou/server
cp env-example .env
docker-compose build <services>

# 2.run container，-d 背景執行
docker-composer up -d

# 3.查詢 php-fpm container id
docker ps

# 4.進入php容器
docker exec -it <container ID> bash
```

##### step 3:install project
```
# 1.install vendor
composer install

# 2.database migratie
cp env-example .env
php bin/console doctrine:migrations:migrate
```

#### Remark

```
#phpmyadmin
http://localhost:8081
root/root

#redis-webui
http://localhost:63790
root/root

# nginx logs
docker logs -f <container ID>
```
