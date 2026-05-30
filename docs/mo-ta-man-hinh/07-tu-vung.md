# Mô Tả Giao Diện Và Chức Năng Trang Từ Vựng

File này mô tả màn hình Từ vựng IELTS của IELTS Focus để dùng khi thiết kế lại giao diện, kiểm thử chức năng hoặc bàn giao cho người dựng UI.

## 1. Mục Tiêu Màn Hình

Trang Từ vựng giúp người học:

- Tra cứu nhanh từ vựng IELTS theo từ tiếng Anh hoặc nghĩa tiếng Việt.
- Xem nghĩa, định nghĩa, ví dụ, chủ đề và trình độ của từng từ.
- Ôn từ theo lĩnh vực/chủ đề.
- Chuyển nhanh sang flashcard hoặc quiz từ vựng.
- Khi đã đăng nhập, kết quả quiz được lưu vào lịch sử học tập.

## 2. Đường Dẫn Liên Quan

- Trang danh sách/tra từ: `/vocabulary`
- API load kết quả tra từ: `/vocabulary/search`
- Trang chi tiết từ: `/vocabulary/{word}`
- Trang flashcard: `/vocabulary/flashcards`
- Trang quiz nhanh: `/vocabulary/quiz`
- Gửi bài quiz: `POST /vocabulary/quiz`

## 3. Bố Cục Trang Từ Vựng

### 3.1. Header Giới Thiệu

Khu vực đầu trang dùng dạng hero panel.

Nội dung cần có:

- Nhãn nhỏ: `Vocabulary bank`
- Tiêu đề chính: giới thiệu việc tra từ vựng IELTS nhanh và trực tiếp.
- Mô tả ngắn: người dùng có thể gõ từ khóa để xem nghĩa tiếng Việt, định nghĩa, ví dụ, chủ đề và level mà không cần tải lại trang.

Yêu cầu giao diện:

- Tiêu đề rõ, dễ đọc.
- Không quá cao trên mobile.
- Nội dung không bị tràn dòng hoặc vỡ layout trên màn hình nhỏ.

### 3.2. Tab Chức Năng

Trang có 2 tab chính:

- `Tra từ`
- `Ôn theo lĩnh vực`

Quy tắc hoạt động:

- Mặc định mở tab `Tra từ`.
- Nếu URL có query `topic` và không có `q`, tab `Ôn theo lĩnh vực` được mở sẵn.
- Khi bấm tab, chỉ đổi nội dung trong khu vực tab, không cần chuyển trang.

## 4. Tab Tra Từ

### 4.1. Ô Nhập Tìm Kiếm

Thành phần chính:

- Label: `Từ cần tra`
- Input name: `q`
- Placeholder: `Nhập từ tiếng Anh hoặc nghĩa tiếng Việt...`
- Nút `Tra từ`
- Nút chuyển nhanh sang `Flashcard`
- Nút chuyển nhanh sang `Quiz nhanh`
- Vùng trạng thái live để thông báo đang tìm hoặc kết quả tìm kiếm.

Chức năng tìm kiếm:

- Cho phép nhập từ tiếng Anh.
- Cho phép nhập nghĩa tiếng Việt.
- Cho phép tìm theo định nghĩa tiếng Anh.
- Cho phép tìm theo ví dụ.
- Cho phép tìm theo chủ đề.
- Có xử lý chuẩn hóa từ khóa: đổi `_`, `-` thành khoảng trắng, bỏ ký tự lạ, rút gọn khoảng trắng.

### 4.2. Kết Quả Tìm Kiếm

Khi có từ khóa, kết quả ưu tiên:

- Từ trùng chính xác.
- Từ bắt đầu bằng từ khóa.
- Các kết quả liên quan còn lại.

Mỗi kết quả cần hiển thị:

- Từ tiếng Anh.
- Loại từ.
- Nghĩa tiếng Việt.
- Chủ đề hoặc thông tin phân loại nếu có.
- Nút xem chi tiết.

Nếu có từ nổi bật đầu tiên, có thể hiển thị dạng thẻ lớn hơn để người dùng xem nhanh:

- Từ.
- Phiên âm.
- Loại từ.
- Nghĩa tiếng Việt.
- Định nghĩa tiếng Anh.
- Ví dụ IELTS.
- Nút `Xem đầy đủ`.

### 4.3. Lazy Loading / Phân Trang

Danh sách từ vựng dùng phân trang 24 từ mỗi trang.

Yêu cầu giao diện:

- Có khu vực danh sách kết quả.
- Có nút hoặc cơ chế tải thêm nếu còn trang sau.
- Khi tải thêm không làm mất kết quả đã có.
- Trạng thái tải thêm phải rõ ràng, không làm nhảy layout.

## 5. Tab Ôn Theo Lĩnh Vực

### 5.1. Danh Sách Chủ Đề

Khu vực bên trái hoặc phía trên mobile hiển thị danh sách chủ đề.

Mỗi chủ đề hiển thị:

- Tên chủ đề.
- Số lượng từ thuộc chủ đề.
- Trạng thái active cho chủ đề đang chọn.

Quy tắc:

- Chủ đề được lấy từ dữ liệu từ vựng có `topic`.
- Sắp xếp ưu tiên chủ đề có nhiều từ hơn, sau đó theo tên chủ đề.
- Nếu chưa chọn chủ đề, tự chọn chủ đề đầu tiên.

### 5.2. Danh Sách Từ Theo Chủ Đề

Khi chọn một chủ đề, hiển thị toàn bộ từ thuộc chủ đề đó.

Mỗi thẻ từ cần có:

- Từ tiếng Anh.
- Phiên âm hoặc loại từ nếu có.
- Nghĩa tiếng Việt.
- Ô nhập câu trả lời luyện nhớ nghĩa.
- Nút `Kiểm tra`.
- Vùng phản hồi đúng/sai.
- Link `Xem giải thích`.

Yêu cầu UX:

- Người dùng có thể tự nhập nghĩa rồi kiểm tra ngay trên thẻ.
- Phản hồi cần hiển thị gần thẻ đang luyện.
- Không reload toàn trang khi kiểm tra tại chỗ.

## 6. Trang Chi Tiết Từ

Đường dẫn: `/vocabulary/{word}`

### 6.1. Phần Đầu Trang

Hiển thị:

- Nút `Quay lại tra từ vựng`.
- Nhãn `IELTS vocabulary`.
- Từ tiếng Anh làm tiêu đề chính.
- Phiên âm.
- Loại từ.
- Badge chủ đề.
- Badge level.

### 6.2. Nội Dung Chi Tiết

Các khối thông tin:

- Nghĩa tiếng Việt.
- Giải thích tiếng Anh.
- Ví dụ IELTS bằng tiếng Anh.
- Dịch ví dụ sang tiếng Việt.
- Từ đồng nghĩa.

Với từ đồng nghĩa:

- Nếu có dữ liệu, hiển thị dạng badge có thể bấm để tìm lại từ đó.
- Nếu chưa có, hiển thị thông báo chưa có từ đồng nghĩa.

### 6.3. Khu Vực Hành Động Tiếp Theo

Cuối trang có panel gợi ý học chủ động:

- Mô tả ngắn: không chỉ đọc nghĩa, cần đưa từ vào luyện tập.
- Nút `Flashcard`.
- Nút `Quiz nhanh`.

## 7. Trang Flashcard

Đường dẫn: `/vocabulary/flashcards`

Chức năng:

- Hiển thị từ vựng dạng thẻ.
- Người dùng bấm vào thẻ để mở/đóng đáp án.
- Mỗi thẻ có thể hiển thị từ, loại từ, nghĩa, định nghĩa và ví dụ.
- Có phân trang/lazy loading để tải thêm flashcard.

Yêu cầu giao diện:

- Thẻ đủ lớn để dễ đọc.
- Trên mobile, thẻ xếp 1 cột.
- Trạng thái mở đáp án phải rõ ràng.

## 8. Trang Quiz Nhanh

Đường dẫn: `/vocabulary/quiz`

Chức năng:

- Hệ thống chọn ngẫu nhiên 5 từ.
- Mỗi câu hỏi có 4 lựa chọn nghĩa.
- Một đáp án đúng là `meaning_vi` của từ.
- Có thời gian làm bài thông qua `data-timed-test`.
- Hết giờ tự động nộp theo logic timer chung của hệ thống.

Sau khi nộp:

- Chấm điểm theo số câu đúng.
- Hiển thị đáp án người dùng đã chọn.
- Hiển thị đáp án đúng.
- Hiển thị giải thích đúng/sai.
- Tách danh sách câu sai để người dùng ôn lại.

Với người dùng đã đăng nhập:

- Lưu lịch sử vào `test_attempts`.
- Loại bài: `Quiz từ vựng`.
- Level: `Ôn nhanh`.
- Lưu điểm, tổng số câu và chi tiết từng câu.

Với khách chưa đăng nhập:

- Vẫn làm được quiz.
- Không lưu lịch sử học tập.
- Cần có gợi ý đăng nhập để lưu kết quả.

## 9. Dữ Liệu Cần Có Cho Một Từ

Một bản ghi từ vựng nên có:

- `word`: từ tiếng Anh.
- `phonetic`: phiên âm.
- `part_of_speech`: loại từ.
- `level`: trình độ, ví dụ B1, B2, C1, IELTS.
- `topic`: chủ đề/lĩnh vực.
- `meaning_vi`: nghĩa tiếng Việt.
- `definition_en`: định nghĩa tiếng Anh.
- `example_en`: ví dụ tiếng Anh.
- `example_vi`: bản dịch ví dụ.
- `synonyms`: danh sách từ đồng nghĩa.

## 10. Trạng Thái Cần Thiết Kế

Cần chuẩn bị giao diện cho các trạng thái:

- Chưa nhập từ khóa.
- Đang tìm kiếm.
- Có kết quả.
- Không có kết quả.
- Đang tải thêm.
- Hết dữ liệu để tải thêm.
- Lỗi mạng hoặc lỗi server khi tìm live.
- Chủ đề không có từ.
- Người dùng làm quiz nhưng chưa đăng nhập.
- Người dùng đã đăng nhập và lưu lịch sử thành công.

## 11. Responsive

Desktop:

- Hero nằm trên cùng.
- Tab rõ ràng.
- Khu vực ôn theo lĩnh vực có thể chia sidebar chủ đề và vùng từ vựng.
- Danh sách kết quả có thể dùng nhiều cột hoặc card rộng.

Tablet:

- Giữ tab và form tìm kiếm rộng vừa phải.
- Các card từ vựng tự xuống dòng.
- Sidebar chủ đề có thể chuyển thành danh sách ngang hoặc grid.

Mobile:

- Hero gọn, không chiếm quá nhiều chiều cao.
- Input tìm kiếm full width.
- Các nút `Flashcard`, `Quiz nhanh`, `Tra từ` dễ bấm và không tràn.
- Tab đủ lớn để chạm.
- Danh sách kết quả xếp 1 cột.
- Thẻ ôn theo chủ đề xếp 1 cột.
- Footer và header không che nội dung khi cuộn.

## 12. Ghi Chú Cho Thiết Kế UI

- Ưu tiên cảm giác học tập nhanh, rõ, ít nhiễu.
- Màu chính dùng theo biến theme của dự án: `--primary`, `--surface`, `--muted`, `--line`.
- Không dùng layout quá chật vì nội dung từ vựng thường dài.
- Các nút hành động chính cần nổi bật nhưng không quá nhiều màu.
- Phần nghĩa tiếng Việt và ví dụ IELTS phải là trọng tâm thị giác.
