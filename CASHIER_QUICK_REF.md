# 📌 Quick Reference - Cashier Implementation

**TL;DR:** Roadmap cepat untuk mengerjakan cashier dashboard

---

## 🔥 Priority Task List

### ⚠️ **BLOCKER** - Harus dikerjakan dulu:

1. **Database:** Create `transactions` table
   - File: `database/migrations/001_create_transactions_table.sql`
   - Action: Run SQL di phpMyAdmin
   - Verify: `SHOW TABLES;` → harus ada `transactions` dan `transaction_items`

2. **API:** Implement `/api/transactions/create.php`
   - Input: cart items, payment method, cash received
   - Output: transaction number, change
   - Test: Postman/curl

3. **Frontend:** Update `cashier/js/pos.js`
   - Add cashier_id to payload
   - Handle response with receipt data
   - Test: Complete a transaction via UI

4. **Auth:** Create login system
   - `cashier/login.php` - Login form
   - `api/auth/login.php` - Authentication
   - Protect `cashier/index.php`
   - Test: Login → access POS

---

## 📝 Recommended Work Order

```
Day 1: Foundation
├── 1. Create transactions table (30 min)
├── 2. Implement transaction API (2 hours)
├── 3. Test API with Postman (30 min)
└── 4. Update frontend integration (1 hour)

Day 2: Authentication
├── 1. Create login page (1 hour)
├── 2. Implement auth API (1 hour)
├── 3. Protect cashier routes (30 min)
├── 4. Add user seeder (30 min)
└── 5. Test login flow (30 min)

Day 3: Receipt & History
├── 1. Create receipt template (1 hour)
├── 2. Implement print function (30 min)
├── 3. Create history API (1 hour)
└── 4. Build history UI (2 hours)

Day 4-5: Polish & Testing
├── 1. Daily summary dashboard (2 hours)
├── 2. Error handling (1 hour)
├── 3. Full system testing (2 hours)
└── 4. Bug fixes & optimization (varies)
```

---

## 🔧 Technical Snippets

### Create Transaction API Structure:

```php
<?php
// api/transactions/create.php
require_once '../../config.php';
require_once '../../includes/db.php';
require_once '../../includes/response.php';

// 1. Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// 2. Validate
if (empty($input['items'])) {
    Response::error('Cart kosong', 400);
}

// 3. Generate transaction number
$date = date('Ymd');
$count = // ... get today's count + 1
$trx_number = "TRX-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

// 4. Calculate totals
$subtotal = array_sum(array_map(function($item) {
    return $item['price'] * $item['qty'];
}, $input['items']));
$tax = $subtotal * 0.1;
$total = $subtotal + $tax;

// 5. Insert transaction
$sql = "INSERT INTO transactions (...) VALUES (...)";
// ... execute

// 6. Insert items
foreach ($input['items'] as $item) {
    $sql = "INSERT INTO transaction_items (...) VALUES (...)";
    // ... execute
}

// 7. Return
Response::success([
    'transaction_id' => $transaction_id,
    'transaction_number' => $trx_number,
    'total' => $total,
    'change' => $input['change_amount']
]);
```

### Simple Login Check:

```php
<?php
// cashier/index.php (top of file)
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cashier_name = $_SESSION['full_name'];
```

---

## 🧪 Quick Test Commands

### Test Transaction API:
```bash
curl -X POST http://localhost/simakmur-pos/api/transactions/create.php \
  -H "Content-Type: application/json" \
  -d '{
    "cashier_id": 1,
    "items": [{"id":1,"name":"Nasi Telur","price":13000,"qty":2}],
    "total": 28600,
    "payment_method": "cash",
    "cash_received": 30000,
    "change_amount": 1400
  }'
```

Expected:
```json
{
  "status": "success",
  "data": {
    "transaction_number": "TRX-20251202-0001",
    ...
  }
}
```

### Verify Database:
```sql
-- Check tables exist
SHOW TABLES LIKE 'transactions%';

-- Check transaction created
SELECT * FROM transactions ORDER BY id DESC LIMIT 1;

-- Check items
SELECT * FROM transaction_items WHERE transaction_id = 1;
```

---

## 📚 Documentation Links

- **PROJECT_ANALYSIS.md** - Full project analysis
- **CASHIER_IMPLEMENTATION_PLAN.md** - Detailed implementation plan
- **INSTALL.md** - Setup guide
- **ANALISIS_KEAMANAN.md** - Security analysis

---

## 🐛 Common Issues & Solutions

### Issue: "Table 'transactions' doesn't exist"
**Solution:** Run migration SQL di phpMyAdmin

### Issue: "API returns 404"
**Solution:** Check `.htaccess` routing, verify file exists

### Issue: "Transaction number not unique"
**Solution:** Add transaction on number generation query

### Issue: "Session not working"
**Solution:** Check `session_start()` at top of file before any output

### Issue: "JSON parse error"
**Solution:** Check `Content-Type: application/json` header

---

## 💡 Pro Tips

1. **Test API first** sebelum integrate ke frontend
   - Use Postman atau curl
   - Verify database entries

2. **Use transaction** untuk database operations
   ```php
   $conn->begin_transaction();
   try {
       // ... insert transaction
       // ... insert items
       $conn->commit();
   } catch (Exception $e) {
       $conn->rollback();
       throw $e;
   }
   ```

3. **Add console.log** everywhere saat debug
   ```javascript
   console.log('Transaction payload:', payload);
   console.log('API response:', response);
   ```

4. **Keep browser DevTools open**
   - Network tab untuk API calls
   - Console untuk errors

5. **Git commit frequently**
   ```bash
   git add .
   git commit -m "feat: implement transaction API"
   ```

---

## ✅ Definition of Done

Task dianggap **DONE** jika:
- [ ] Code ditulis & tested
- [ ] No console errors
- [ ] Database entries correct
- [ ] UI responds correctly
- [ ] Happy path works end-to-end
- [ ] Basic error handling ada
- [ ] Git committed

---

**Questions? Check implementation plan atau konsul team! 🚀**

---

*Quick ref untuk: CASHIER_IMPLEMENTATION_PLAN.md*
