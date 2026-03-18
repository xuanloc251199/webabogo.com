# install

## 1. Install the package
```bash
composer install
```

## 2. Migration
```bash
php artisan migrate
```

## 3. Seed
```bash
php artisan db:seed
```

## 4. Change permission for storage
```bash
chmod -R 777 storage/ bootstrap/ public/storage/
```

## 5. Change permission for AutoDeploy
```bash
chmod +x .scripts/deploy.sh
```

## 6. Run the server
