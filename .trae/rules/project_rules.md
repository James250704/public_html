1. 請全部使用Bootstrap的樣式以及js
2. 請將各個顯示區塊分開，並使用Bootstrap的row與col
3. 請將所有的圖片放置在imgs資料夾中
4. 請將所有的js放置在js資料夾中
5. 請將所有的css放置在css資料夾中
8. 請將API類型分開存放並在API資料夾中的APImap中調用
9. DataBase請使用mySQL並使用下列定義的資料表：
    1. Member(MemberID, Name, Email, Password, Phone, City, Address, IsAdmin)
    2. Product(ProductID, Type, ProductName, Introdution, isActive)
    3. Options(OptionID, ProductID, Color, Size, SizeDescription, Price, Stock)
    4. CartItem(MemberID, OptionID, Quantity)
    5. Orders(OrderID, MembersID, OrderDate, Status, Note)
    6. OrderItem(OrderID, OptionID, Quantity)
    7. Receipt(ReceiptID, MemberID, OrderID, PaymentMethod, PaymentDate)
    8. ReceiptItem(ReceiptID, OptionID, WarrantyID)
    9. Warranty(WarrantyID, WarrantyDate, WarrantyStatus)
    10. Repairs(RepairID, WarrantyID, RepairDate, RepairStatus)
10. 請使用PHP的PDO來操作資料庫
11. 當要使用AJAX時，請使用await與async，禁止使用then
12. 請使用Bootstrap的modal與alert替代js的alert
13. php路徑請使用 __DIR__ 來取得，不要使用硬式路徑
14. 當有要求使用AJAX才使用AJAX，否則請使用PHP
15. error使用js的console.log來輸出
16. 在單獨顯示的畫面要使用 fixedFile中的 header.php與footer.php，並且使用正確的架構
17. 使用中文回答
18. 在訂單狀態時使用下面的樣式：
    'Completed' => 'bg-success',
    'Pending' => 'bg-info text-dark',
    'Shipping' => 'bg-primary',
    'Cancel' => 'bg-warning',
    'abnormal' => 'bg-danger',
    default => 'bg-secondary'   
    'Completed' => '已完成',
    'Pending' => '處理中',
    'Shipping' => '運送中',
    'Cancel' => '已取消',
    'abnormal' => '異常',