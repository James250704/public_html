# 歐印精品網站架構

## 檔案結構
```
/new_test/
├── css/
│   └── style.css
├── js/
│   └── main.js
├── images/
├──
│--- api/
│    ├── APImap.php
│    ├── member.php
│    ├── product.php
│    └── order.php
│---header.php
│---footer.php
│── index.php
│── profile.php
│── shopping.php
│── repair.php
│── about.php
│── productDetail.php
│── cart.php
│── checkout.php
│── myOrder.php
│── login.php
│── register.php
│── backend.php
├── .trae/
│   └── rules/
│       └── project_rules.md
├── feature.md
└── project_structure.md
```

## 資料庫結構
```sql
CREATE TABLE Member (
    MemberID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(50) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Phone VARCHAR(20),
    City VARCHAR(50),
    Address VARCHAR(255),
    IsAdmin BOOLEAN DEFAULT FALSE
);

CREATE TABLE Product (
    ProductID INT AUTO_INCREMENT PRIMARY KEY,
    Type VARCHAR(50) NOT NULL,
    ProductName VARCHAR(100) NOT NULL,
    Introdution TEXT,
    isActive BOOLEAN DEFAULT TRUE
);

CREATE TABLE Options (
    OptionID INT AUTO_INCREMENT PRIMARY KEY,
    ProductID INT NOT NULL,
    Color VARCHAR(50),
    Size VARCHAR(20),
    SizeDescription TEXT,
    Price DECIMAL(10,2) NOT NULL,
    Stock INT NOT NULL,
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

CREATE TABLE CartItem (
    MemberID INT NOT NULL,
    OptionID INT NOT NULL,
    Quantity INT NOT NULL,
    PRIMARY KEY (MemberID, OptionID),
    FOREIGN KEY (MemberID) REFERENCES Member(MemberID),
    FOREIGN KEY (OptionID) REFERENCES Options(OptionID)
);

CREATE TABLE Orders (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    MembersID INT NOT NULL,
    OrderDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status VARCHAR(20) NOT NULL,
    FOREIGN KEY (MembersID) REFERENCES Member(MemberID)
);

CREATE TABLE OrderItem (
    OrderID INT NOT NULL,
    OptionID INT NOT NULL,
    Quantity INT NOT NULL,
    PRIMARY KEY (OrderID, OptionID),
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID),
    FOREIGN KEY (OptionID) REFERENCES Options(OptionID)
);

CREATE TABLE Receipt (
    ReceiptID INT AUTO_INCREMENT PRIMARY KEY,
    MemberID INT NOT NULL,
    OrderID INT NOT NULL,
    PaymentMethod VARCHAR(50) NOT NULL,
    PaymentDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MemberID) REFERENCES Member(MemberID),
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID)
);

CREATE TABLE ReceiptItem (
    ReceiptID INT NOT NULL,
    OptionID INT NOT NULL,
    WarrantyID INT,
    PRIMARY KEY (ReceiptID, OptionID),
    FOREIGN KEY (ReceiptID) REFERENCES Receipt(ReceiptID),
    FOREIGN KEY (OptionID) REFERENCES Options(OptionID),
    FOREIGN KEY (WarrantyID) REFERENCES Warranty(WarrantyID)
);

CREATE TABLE Warranty (
    WarrantyID INT AUTO_INCREMENT PRIMARY KEY,
    WarrantyDate DATE NOT NULL,
    WarrantyStatus VARCHAR(20) NOT NULL
);

CREATE TABLE Repairs (
    RepairID INT AUTO_INCREMENT PRIMARY KEY,
    WarrantyID INT NOT NULL,
    RepairDate DATE NOT NULL,
    RepairStatus VARCHAR(20) NOT NULL,
    FOREIGN KEY (WarrantyID) REFERENCES Warranty(WarrantyID)
);
```

## API 端點規劃

APImap.php
```
GET    /api/member/login      - 會員登入
POST   /api/member/register   - 會員註冊
GET    /api/member/profile    - 取得會員資料
PUT    /api/member/profile    - 更新會員資料

GET    /api/product/list      - 商品列表
GET    /api/product/{id}      - 商品詳情
POST   /api/product           - 新增商品 (管理員)
PUT    /api/product/{id}      - 更新商品 (管理員)
DELETE /api/product/{id}      - 刪除商品 (管理員)

GET    /api/cart              - 購物車內容
POST   /api/cart              - 加入購物車
PUT    /api/cart/{optionId}   - 更新購物車數量
DELETE /api/cart/{optionId}   - 移除購物車項目

POST   /api/order             - 建立訂單
GET    /api/order             - 訂單列表
GET    /api/order/{id}        - 訂單詳情
```