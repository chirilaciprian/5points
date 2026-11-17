-- Product Management System: sample data (10 products).
-- Run after schema.sql: mysql -u <user> -p < database/seed/seed.sql
-- Running it again inserts the same 10 products a second time.

USE 5points;

INSERT INTO products (name, description, price, image, availability_date, in_stock) VALUES
    ('Wireless Mouse', 'Ergonomic 2.4 GHz wireless mouse with a silent click and 18-month battery life.', 24.99, NULL, '2026-01-15', 1),
    ('Mechanical Keyboard', 'Tenkeyless mechanical keyboard with hot-swappable brown switches and white backlight.', 89.50, NULL, '2026-02-01', 1),
    ('27" 4K Monitor', 'IPS panel, 3840x2160, 60 Hz, USB-C with 65 W power delivery.', 329.00, NULL, '2026-03-10', 1),
    ('USB-C Docking Station', '11-in-1 dock: dual HDMI, Ethernet, SD reader and 100 W pass-through charging.', 119.99, NULL, '2026-04-05', 0),
    ('Noise Cancelling Headphones', 'Over-ear Bluetooth headphones with active noise cancelling and 30 hours of playback.', 199.00, NULL, '2026-05-20', 1),
    ('Laptop Stand', 'Adjustable aluminium stand for laptops from 11 to 17 inches.', 34.90, NULL, '2026-06-01', 1),
    ('1080p Webcam', 'Full HD webcam with autofocus, privacy shutter and dual microphones.', 59.99, NULL, '2026-07-12', 0),
    ('Portable SSD 1TB', 'Pocket-sized NVMe SSD with read speeds up to 1050 MB/s over USB 3.2.', 109.00, NULL, '2026-09-01', 1),
    ('Smart LED Desk Lamp', 'Dimmable desk lamp with five colour temperatures and a wireless charging base.', 45.00, NULL, '2026-12-01', 0),
    ('Bluetooth Speaker', NULL, 39.95, NULL, NULL, 1);
