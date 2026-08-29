# B2B Sipariş ve Stok Yönetimi

Laravel ve MySQL kullanılarak geliştirilmiş, kategori, ürün, stok ve sipariş işlemlerinin yönetilebildiği basit bir B2B yönetim panelidir.

Proje; ilişkisel veritabanı kullanımı, Eloquent ilişkileri, görsel yükleme, arama, filtreleme, pagination, sipariş oluşturma ve stok düşürme işlemlerini öğrenmek amacıyla hazırlanmıştır.

## Kullanılan Teknolojiler

- PHP 8.3+
- Laravel 13
- MySQL
- Blade
- HTML
- CSS
- Git ve GitHub

Projede harici bir yönetim paneli, JavaScript framework’ü veya ek CRUD kütüphanesi kullanılmamıştır.

## Proje Özellikleri

### Kategori Yönetimi

- Kategori listeleme
- Yeni kategori ekleme
- Kategori düzenleme
- Kategori silme
- Kategori adından otomatik slug oluşturma
- Ürün bağlı kategorilerin yanlışlıkla silinmesini engelleme

### Ürün Yönetimi

- Ürün listeleme
- Yeni ürün ekleme
- Ürün düzenleme
- Ürün silme
- Ürünü kategoriye bağlama
- Benzersiz SKU kontrolü
- Fiyat ve stok yönetimi
- Ürün görseli yükleme
- Görsel formatı ve boyut doğrulaması
- Soft Delete ile güvenli silme

### Görsel Yükleme

Desteklenen görsel formatları:

- JPEG
- JPG
- PNG
- WEBP

Yüklenebilecek en yüksek dosya boyutu 2 MB’dır. Görseller Laravel’in `public` diski kullanılarak saklanır.

### Arama ve Filtreleme

- Ürün adına göre arama
- Kategoriye göre filtreleme
- Arama ve kategori filtresini birlikte kullanma
- Her sayfada 10 ürün gösterme
- Sayfa geçişlerinde filtreleri koruma
- Eager Loading ile N+1 sorgu problemini önleme

### Sipariş ve Stok Yönetimi

- Ürün üzerinden hızlı sipariş oluşturma
- Müşteri adı ve sipariş adedi kaydetme
- Sipariş miktarını mevcut stokla karşılaştırma
- Stoktan fazla siparişi engelleme
- Toplam tutarı sunucu tarafında otomatik hesaplama
- Sipariş sonrasında ürün stoğunu otomatik azaltma
- Stok sıfır olduğunda sipariş butonunu pasifleştirme
- Siparişleri listeleme

### Bonus Özellik

- Silinen ürünleri ayrı sayfada listeleme
- Soft Delete ile silinen ürünleri geri yükleme
- Silinen ürünün görselini koruma

## Veritabanı Yapısı

Projede üç ana tablo bulunmaktadır.

### categories

- id
- name
- slug
- created_at
- updated_at

### products

- id
- category_id
- name
- sku
- price
- stock
- image_path
- deleted_at
- created_at
- updated_at

### orders

- id
- product_id
- customer_name
- quantity
- total_price
- status
- created_at
- updated_at

## Eloquent İlişkileri

- Bir kategori birden fazla ürüne sahip olabilir.
- Her ürün bir kategoriye aittir.
- Bir ürün birden fazla siparişe sahip olabilir.
- Her sipariş bir ürüne aittir.

## Kurulum

### 1. Projeyi klonlayın

```bash
git clone https://github.com/Beratcan-Polat/b2b-stok-yonetimi.git
```

### 2. Proje klasörüne girin

```bash
cd b2b-stok-yonetimi
```

### 3. Composer paketlerini yükleyin

```bash
composer install
```

### 4. Ortam dosyasını oluşturun

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 5. Uygulama anahtarını oluşturun

```bash
php artisan key:generate
```

### 6. MySQL veritabanını oluşturun

phpMyAdmin üzerinden aşağıdaki isimle boş bir veritabanı oluşturun:

```text
b2b_stok_yonetimi
```

### 7. Veritabanı bağlantısını yapılandırın

`.env` dosyasındaki veritabanı bölümünü düzenleyin:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=b2b_stok_yonetimi
DB_USERNAME=root
DB_PASSWORD=
```

MySQL kullanıcınızın şifresi varsa `DB_PASSWORD` alanına yazın.

### 8. Tabloları ve örnek verileri oluşturun

```bash
php artisan migrate --seed
```

Seeder işlemi sonucunda örnek kategoriler ve ürünler oluşturulur.

### 9. Görsel bağlantısını oluşturun

```bash
php artisan storage:link
```

### 10. Projeyi çalıştırın

```bash
php artisan serve
```

Tarayıcıdan aşağıdaki adresi açın:

```text
http://127.0.0.1:8000
```

## Seeder İçeriği

Projede aşağıdaki Seeder dosyaları bulunmaktadır:

- `CategorySeeder`
- `ProductSeeder`

Seeder ile dört örnek kategori ve pagination testinde kullanılabilecek 12 örnek ürün oluşturulur.

## Form Doğrulamaları

Projede aşağıdaki temel doğrulamalar uygulanmaktadır:

- Zorunlu alan kontrolü
- Benzersiz kategori ve SKU kontrolü
- Fiyatın negatif olmaması
- Stok miktarının tam sayı ve negatif olmaması
- Sipariş miktarının mevcut stoktan fazla olmaması
- Görsel formatı ve 2 MB dosya boyutu kontrolü

## Temel Artisan Komutları

Migration durumunu kontrol etmek:

```bash
php artisan migrate:status
```

Seeder çalıştırmak:

```bash
php artisan db:seed
```

## Proje Notları

- `.env` dosyası güvenlik nedeniyle GitHub’a gönderilmez.
- Kullanıcı tarafından yüklenen test görselleri GitHub’a gönderilmez.
- Toplam sipariş tutarı formdan alınmaz; sunucu tarafında hesaplanır.
- Ürün silindiğinde kayıt tamamen kaldırılmaz ve `deleted_at` alanı doldurulur.
- Sipariş verildiğinde ürün stoğu otomatik olarak azaltılır.

## Geliştirici

Beratcan Polat
