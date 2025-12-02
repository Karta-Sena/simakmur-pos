# 🚀 Cashier Dashboard Implementation Plan

**Project:** SiMakmur POS - Cashier Module Completion  
**Created:** 2 Desember 2025  
**Status:** Ready for Implementation

---

## 🎯 Implementation Goal

**Complete the Cashier POS module** agar fully functional untuk handle walk-in customers dengan fitur:
- Transaction processing
- Receipt printing
- Transaction history
- Daily sales summary
- Authentication system

---

## 📋 Prerequisites

Sebelum mulai, pastikan:
- ✅ Database `simakmur_db` sudah import
- ✅ `.env` sudah configured
- ✅ XAMPP running (Apache + MySQL)
- ✅ Customer module functioning (reference)

---

## 🔴 Phase 1: Critical Foundation (P0)

### Task 1.1: Create Transactions Table

**Priority:** **P0 - BLOCKER**  
**Estimated Time:** 30 minutes

#### 📁 File: `database/migrations/001_create_transactions_table.sql`

```sql
-- ============================================
-- MIGRATION: Create transactions table
-- For cashier POS walk-in sales
-- ============================================

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_number` varchar(50) NOT NULL UNIQUE,
  `cashier_id` int(11) DEFAULT NULL,
  `customer_type` enum('walk-in','online') DEFAULT 'walk-in',
  `payment_method` enum('cash','qris','debit','credit') NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `cash_received` decimal(10,2) DEFAULT NULL,
  `change_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('completed','cancelled','refunded') DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cashier_id` (`cashier_id`),
  KEY `transaction_number` (`transaction_number`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transaction Items (detail per item)
CREATE TABLE IF NOT EXISTS `transaction_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Action:**
1. Create file di `database/migrations/`
2. Run SQL via phpMyAdmin atau command line
3. Verify dengan `SHOW TABLES;`

---

### Task 1.2: Implement Transaction API

**Priority:** **P0 - BLOCKER**  
**Estimated Time:** 2 hours

#### 📁 File: `api/transactions/create.php`

**Purpose:** Process POS transaction and save to database

**Payload:**
```json
{
  "cashier_id": 1,
  "items": [
    {
      "id": 1,
      "name": "Nasi Telur",
      "price": 13000,
      "qty": 2
    }
  ],
  "total": 28600,
  "payment_method": "cash",
  "cash_received": 30000,
  "change_amount": 1400
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Transaksi berhasil",
  "data": {
    "transaction_id": 1,
    "transaction_number": "TRX-20251202-0001",
    "total": 28600,
    "change": 1400,
    "timestamp": "2025-12-02 12:00:00"
  }
}
```

**Implementation Steps:**
1. Validate payload
2. Generate transaction number (`TRX-YYYYMMDD-XXXX`)
3. Calculate tax (10%)
4. Insert to `transactions` table
5. Insert items to `transaction_items` table
6. Return transaction data for receipt

**Validation Rules:**
- ✅ Items array not empty
- ✅ Total >= 0
- ✅ Cash received >= total (if cash payment)
- ✅ Payment method valid enum
- ✅ Cashier ID exists (optional for now)

---

### Task 1.3: Update Cashier Frontend

**Priority:** **P0**  
**Estimated Time:** 1 hour

#### 📁 File: `cashier/js/pos.js`

**Changes Needed:**

1. **Add cashier_id to payload** (line 193):
```javascript
const payload = {
    cashier_id: null, // TODO: Get from session after auth
    items: Store.state.cart,
    total: this.state.grandTotal,
    payment_method: this.state.paymentMethod,
    cash_received: paid,
    change_amount: change
};
```

2. **Handle transaction response** (line 211):
```javascript
if (response && response.status === 'success') {
    this.closeModal('modalPayment');
    
    // Store receipt data
    this.lastReceipt = response.data;
    
    // Show success
    this.showToast(`Transaksi ${response.data.transaction_number} Berhasil!`);
    
    // Auto-print receipt (browser print)
    this.printReceipt(response.data);
    
    Store.clearCart();
    this.state.payInput = '0';
}
```

---

## 🟡 Phase 2: Transaction History (P1)

### Task 2.1: History API

**Priority:** **P1**  
**Estimated Time:** 1 hour

#### 📁 File: `api/transactions/history.php`

**Purpose:** Get transaction history with filters

**Query Params:**
- `date` - Filter by date (YYYY-MM-DD)
- `cashier_id` - Filter by cashier
- `limit` - Pagination limit
- `offset` - Pagination offset

**Response:**
```json
{
  "status": "success",
  "data": {
    "transactions": [
      {
        "id": 1,
        "transaction_number": "TRX-20251202-0001",
        "total": 28600,
        "payment_method": "cash",
        "created_at": "2025-12-02 12:00:00"
      }
    ],
    "total_count": 50,
    "total_sales": 1430000
  }
}
```

---

### Task 2.2: History UI Component

**Priority:** **P1**  
**Estimated Time:** 2 hours

#### 📁 File: `cashier/views/history.php`

**Features:**
- Date filter (today, this week, this month, custom)
- Transaction list with details
- Search by transaction number
- View receipt details
- Reprint receipt option

**UI Layout:**
```
┌─────────────────────────────────┐
│ [Filters]  [Search]  [Date]     │
├─────────────────────────────────┤
│ TRX-001 | Cash | 28.600 | 12:00│
│ TRX-002 | QRIS | 45.000 | 12:15│
│ ...                             │
├─────────────────────────────────┤
│ Total: 50 transaksi | Rp 1.5JT │
└─────────────────────────────────┘
```

**Update:**
- Add "Riwayat" button handler in sidebar
- Create modal or new view for history
- Implement pagination

---

## 🟢 Phase 3: Receipt Printing (P1)

### Task 3.1: Receipt HTML Template

**Priority:** **P1**  
**Estimated Time:** 1 hour

#### 📁 File: `cashier/views/receipt.html`

**Features:**
- Print-optimized CSS (`@media print`)
- Shop details (name, address dari .env)
- Transaction number
- Date & time
- Items list
- Subtotal, tax, total
- Payment method
- Change amount (if cash)
- Thank you message

**Template:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Struk - [transaction_number]</title>
    <style>
        @media print {
            body { width: 80mm; font-size: 12px; }
            /* Receipt styling */
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h2>KEDAI SEKAR MAKMUR</h2>
        <p>Jl. Kartini No 105, Burikan, Kudus</p>
        <hr>
        <p>TRX: [transaction_number]</p>
        <p>Waktu: [datetime]</p>
        <p>Kasir: [cashier_name]</p>
        <hr>
        <table>
            <!-- Items -->
        </table>
        <hr>
        <p>Subtotal: [subtotal]</p>
        <p>Pajak (10%): [tax]</p>
        <p><strong>TOTAL: [total]</strong></p>
        <p>Bayar: [cash_received]</p>
        <p>Kembali: [change]</p>
        <hr>
        <p class="center">Terima Kasih!</p>
    </div>
    <script>
        window.print();
    </script>
</body>
</html>
```

---

### Task 3.2: Print Function

**Priority:** **P1**  
**Estimated Time:** 30 minutes

#### 📁 File: `cashier/js/pos.js`

**Add method:**
```javascript
printReceipt(transactionData) {
    // Open print window with receipt HTML
    const printWindow = window.open('', '_blank', 'width=300,height=600');
    
    // Generate receipt HTML from template
    const receiptHTML = this.generateReceiptHTML(transactionData);
    
    printWindow.document.write(receiptHTML);
    printWindow.document.close();
    
    // Auto print
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

generateReceiptHTML(data) {
    // Populate template with data
    // Return completed HTML string
}
```

---

## 🔵 Phase 4: Authentication (P0)

### Task 4.1: Login System

**Priority:** **P0 - SECURITY**  
**Estimated Time:** 2 hours

#### 📁 File: `cashier/login.php`

**Features:**
- Simple login form (username + password)
- Session-based authentication
- Redirect to index.php if authenticated
- Store cashier info in session

#### 📁 File: `api/auth/login.php`

**Payload:**
```json
{
  "username": "kasir1",
  "password": "password123"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "user_id": 2,
    "username": "kasir1",
    "full_name": "Kasir 1",
    "role": "cashier"
  }
}
```

**Implementation:**
1. Validate credentials against `users` table
2. Verify `role = 'cashier'`
3. Create session with user data
4. Return user info

---

### Task 4.2: Protect Cashier Routes

**Priority:** **P0**  
**Estimated Time:** 30 minutes

#### 📁 File: `cashier/index.php`

**Add at top:**
```php
<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cashier') {
    header('Location: login.php');
    exit;
}

$cashier_name = $_SESSION['full_name'];
$cashier_id = $_SESSION['user_id'];
?>
```

**Update UI:**
- Display cashier name in sidebar avatar
- Add logout button
- Pass cashier_id to transaction API

---

## 🟣 Phase 5: Daily Summary (P2)

### Task 5.1: Daily Report API

**Priority:** **P2**  
**Estimated Time:** 1 hour

#### 📁 File: `api/reports/daily.php`

**Query Params:**
- `date` - Date to report (YYYY-MM-DD, default: today)
- `cashier_id` - Optional filter

**Response:**
```json
{
  "status": "success",
  "data": {
    "date": "2025-12-02",
    "total_transactions": 120,
    "total_sales": 3450000,
    "payment_methods": {
      "cash": 2100000,
      "qris": 1200000,
      "debit": 150000
    },
    "top_products": [
      {"name": "Nasi Ayam Sereh", "qty": 45, "revenue": 990000},
      {"name": "Es Teh", "qty": 89, "revenue": 356000}
    ]
  }
}
```

---

### Task 5.2: Dashboard Stats UI

**Priority:** **P2**  
**Estimated Time:** 1 hour

**Add to `cashier/index.php` or create `cashier/dashboard.php`**

**Features:**
- Today's sales summary cards
- Transaction count
- Payment method breakdown
- Top selling items
- Refresh button

**UI Layout:**
```
┌──────────────────────────────────┐
│  Today's Summary - 2 Des 2025    │
├──────────────────────────────────┤
│ [120 Transaksi] [Rp 3.45 JT]    │
│                                   │
│ Cash: Rp 2.1JT | QRIS: Rp 1.2JT │
│                                   │
│ Top Items:                        │
│ 1. Nasi Ayam Sereh (45x)         │
│ 2. Es Teh (89x)                  │
└──────────────────────────────────┘
```

---

## 📦 Phase 6: Database Seeder

### Task 6.1: Create Test Data

**Priority:** **P2**  
**Estimated Time:** 30 minutes

#### 📁 File: `database/seeders/001_users_seeder.sql`

```sql
-- Add cashier users for testing
INSERT INTO `users` (`username`, `password`, `role`, `full_name`, `is_active`) VALUES
('kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier', 'Kasir 1', 1),
('kasir2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier', 'Kasir 2', 1);

-- Password for both: admin123
```

#### 📁 File: `database/seeders/002_transactions_seeder.sql`

```sql
-- Add dummy transactions for testing history
INSERT INTO `transactions` (...) VALUES (...);
INSERT INTO `transaction_items` (...) VALUES (...);
```

---

## ✅ Testing Checklist

### Unit Testing (Manual):

**Transaction Creation:**
- [ ] Create transaction with cash payment
- [ ] Create transaction with QRIS payment
- [ ] Calculate tax correctly (10%)
- [ ] Calculate change correctly
- [ ] Generate unique transaction number
- [ ] Validate empty cart
- [ ] Validate insufficient payment

**History:**
- [ ] View today's transactions
- [ ] Filter by date range
- [ ] Search by transaction number
- [ ] Pagination works
- [ ] Total calculation correct

**Receipt:**
- [ ] Print receipt after transaction
- [ ] All data displayed correctly
- [ ] Format readable
- [ ] Browser print dialog opens

**Authentication:**
- [ ] Login with valid credentials
- [ ] Login fails with invalid credentials
- [ ] Session persists across pages
- [ ] Logout clears session
- [ ] Protected routes redirect to login

**Daily Report:**
- [ ] Shows today's summary
- [ ] Payment method breakdown correct
- [ ] Top products accurate
- [ ] Refresh updates data

---

## 🗂️ File Structure After Implementation

```
cashier/
├── index.php               # Main POS interface (protected)
├── login.php               # Login page (new)
├── css/
│   └── cashier.css        # Existing styles
├── js/
│   └── pos.js             # Updated with receipt printing
└── views/
    ├── history.php        # Transaction history (new)
    ├── receipt.html       # Print template (new)
    └── dashboard.php      # Daily summary (new)

api/
├── auth/
│   └── login.php          # Authentication (new)
├── transactions/
│   ├── create.php         # Create transaction (update)
│   └── history.php        # Get history (new)
└── reports/
    ├── daily.php          # Daily summary (new)
    └── stats.php          # Existing

database/
├── migrations/
│   └── 001_create_transactions_table.sql  # New
└── seeders/
    ├── 001_users_seeder.sql               # New
    └── 002_transactions_seeder.sql        # New
```

---

## 📊 Implementation Timeline

### Sprint 1: Foundation (2-3 days)
- ✅ Day 1: Database schema + Transaction API
- ✅ Day 2: Frontend integration + Testing
- ✅ Day 3: Authentication system

### Sprint 2: Features (2 days)
- ✅ Day 4: Transaction history + Receipt printing
- ✅ Day 5: Daily summary + Polish

### Sprint 3: Testing & Deployment (1 day)
- ✅ Day 6: Full system testing + Bug fixes

**Total Estimated Time:** 1 week (5-6 days)

---

## 🎯 Success Criteria

Cashier module dianggap **COMPLETE** jika:

- [x] Cashier bisa login dengan credentials
- [x] POS interface load semua products
- [x] Bisa add items ke cart
- [x] Bisa process payment (cash & QRIS)
- [x] Transaction tersimpan ke database
- [x] Generate unique transaction number
- [x] Receipt bisa di-print
- [x] Bisa lihat history transaksi
- [x] Bisa lihat daily summary
- [x] Tax calculation accurate (10%)
- [x] Change calculation accurate
- [x] Session management working
- [x] Protected from unauthorized access

---

## 🚀 Next Steps

### Immediate Actions:

1. **Review this plan** dengan team
2. **Setup development branch**
   ```bash
   git checkout -b feature/cashier-complete
   ```

3. **Start with Phase 1** (Critical Foundation)
   - Create transactions table
   - Implement transaction API
   - Test with Postman

4. **Incremental testing** setelah each phase

5. **Merge to main** setelah semua tests pass

---

## 📝 Notes

### Assumptions:
- Using existing `users` table for cashier accounts
- Tax rate fixed at 10% (configurable di .env nanti)
- Transaction number format: `TRX-YYYYMMDD-XXXX`
- Browser print untuk receipt (no thermal printer yet)

### Future Enhancements (Post-MVP):
- Bluetooth thermal printer integration
- Barcode scanner support
- Shift management
- Cash drawer tracking
- Void/refund transactions
- Split payment
- Customer loyalty points

---

**Ready to implement? Start with Phase 1! 🚀**

---

*Created by: Karta-Sena Team*  
*Last Updated: 2 Desember 2025*
