# 02. Màn Hình Auth Và Tài Khoản Trên Header

## Trang Đăng Nhập

Mục đích: cho user đăng nhập vào hệ thống.

Chức năng cần có:

- Form email.
- Form mật khẩu.
- Nút đăng nhập.
- Link sang đăng ký.
- Hiển thị lỗi khi nhập sai.
- Sau đăng nhập:
  - User thường chuyển sang dashboard học viên.
  - Admin chuyển sang trang quản trị hoặc có thể mở trang quản trị từ menu tài khoản.

## Trang Đăng Ký

Mục đích: cho khách tạo tài khoản để lưu lịch sử học.

Chức năng cần có:

- Form tên.
- Form email.
- Form mật khẩu.
- Form xác nhận mật khẩu nếu cần.
- Nút tạo tài khoản.
- Link quay lại đăng nhập.
- Hiển thị lỗi validation.

## Menu Tài Khoản Trên Header

Mục đích: sau khi đăng nhập, thay nút đăng nhập/đăng ký bằng menu tài khoản.

Chức năng cần có:

- Hiển thị tên tài khoản.
- Bấm vào tên sẽ xổ xuống menu.
- Nếu là admin: có link Trang quản lý.
- Nếu là user: có link Quản lý tài khoản.
- Link Bài đã làm.
- Nút Đăng xuất.

Yêu cầu giao diện:

- Gọn trên desktop.
- Dễ bấm trên mobile.
- Không làm header quá cao.
