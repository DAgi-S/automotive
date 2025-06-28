-- Sample Products for Checkout Testing
USE automotive;

INSERT IGNORE INTO tbl_products (id, name, description, price, image_url, stock, status) VALUES
(1, 'Premium Brake Pads', 'High-quality brake pads for superior stopping power', 89.99, 'https://partsouq.com/assets/tesseract/assets/global/TOYOTA00/source/11/110642.gif', 50, 'active'),
(2, 'Engine Oil Filter', 'Premium oil filter for extended engine protection', 12.99, 'https://www.ebaymotorsblog.com/motors/blog/wp-content/uploads/2023/08/evo_crankshaft-592x400.jpg', 100, 'active'),
(3, 'Performance Boost Kit', 'Complete performance enhancement kit', 199.99, 'https://sp-ao.shortpixel.ai/client/to_auto,q_glossy,ret_img,w_495,h_450/https://dizz.com/wp-content/uploads/2023/04/car-turbocharger-isolated-on-white-background-tur-2021-08-27-08-38-29-utc-PhotoRoom.png-PhotoRoom.webp', 25, 'active'),
(4, 'Premium Suspension System', 'Advanced suspension system for improved ride quality', 299.99, 'https://w7.pngwing.com/pngs/260/445/png-transparent-automotive-engine-parts-automotive-engine-parts-car-parts-auto-parts.png', 15, 'active'); 