# IELTS Topic Web

Ứng dụng Laravel luyện chủ đề IELTS, từ vựng, tra từ điển và làm bài luyện tập theo cấp độ.

## Chức năng chính

- Xem danh sách chủ đề IELTS và nội dung gợi ý luyện Speaking/Writing.
- Tra cứu từ vựng IELTS, flashcard và quiz nhanh.
- Tra từ điển từ dữ liệu Open English WordNet 2025, có lưu lịch sử khi đăng nhập.
- Luyện bài theo 6 cấp độ: vocabulary, grammar, sentence role, definition, spelling, example completion, IELTS format và các dạng skill practice.
- Lưu lịch sử làm bài, điểm số, lỗi sai và từ đã tra cho tài khoản đăng nhập.

## Yêu cầu

- PHP 8.2+
- Composer
- Node.js 20+ và npm
- SQLite hoặc database khác được Laravel hỗ trợ

## Cài đặt local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Nếu dùng SQLite, tạo file database:

```bash
touch database/database.sqlite
```

Trên PowerShell có thể dùng:

```powershell
New-Item -Path database\database.sqlite -ItemType File -Force
```

Sau đó migrate và seed dữ liệu:

```bash
php artisan migrate --seed
```

Nếu muốn dùng form dịch giống Google Dịch trên trang từ điển, bật Cloud Translation API trong Google Cloud rồi điền key vào `.env`:

```env
GOOGLE_TRANSLATE_API_KEY=your-google-cloud-api-key
GOOGLE_TRANSLATE_ENDPOINT=https://translation.googleapis.com/language/translate/v2
```

Build asset hoặc chạy Vite:

```bash
npm run build
# hoặc
npm run dev
```

Chạy server Laravel:

```bash
php artisan serve
```

Mặc định app chạy tại `http://127.0.0.1:8000`.

## Chạy test

```bash
composer test
```

Hoặc chạy trực tiếp:

```bash
php artisan test
```

`phpunit.xml` đã cấu hình database SQLite in-memory cho môi trường test.

## Dữ liệu seed

- `TopicSeeder`: dữ liệu chủ đề IELTS.
- `VocabularySeeder`: bộ từ vựng chọn lọc.
- `DictionaryEntrySeeder`: đọc dữ liệu WordNet trong `database/seeders/data/oewn2025`.

Seeder từ điển có fallback nghĩa tiếng Việt từ bảng `vocabularies`; các từ chưa có bản dịch sẽ hiển thị giải thích tiếng Anh kèm nhãn loại từ.

## Lỗi thường gặp

- `vendor/autoload.php` không tồn tại: chạy `composer install`.
- `vite is not recognized`: chạy `npm install`.
- Không có SQLite database: tạo `database/database.sqlite`, sau đó chạy `php artisan migrate --seed`.
- Git báo `dubious ownership`: thêm safe directory cho repo:

```bash
git config --global --add safe.directory D:/ielts-topic-web
```

## Scripts hữu ích

```bash
composer test
npm run build
npm run dev
php artisan migrate:fresh --seed
```
