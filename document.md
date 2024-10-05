# SmilePay Webhook 文件

## 目錄

1. [概述](#概述)
2. [Webhook Route](#webhook-Route)
3. [請求方法](#請求方法)
4. [請求標頭](#請求標頭)
5. [請求範例](#請求範例)
6. [Response](#Response)
7. [錯誤處理](#錯誤處理)
8. [安全性考量](#安全性考量)

---

## 概述

SmilePay Webhook 是一個 HTTP Route，用於接收來自 SmilePay 系統的付款通知。當使用者完成付款後，SmilePay 將向此Route發送通知，以便您的系統能夠即時處理對應的訂單。

## Webhook Route

- **URL**: `https://domain.to.your.paymenter/extensions/smilepay/webhook`

## 請求方法

- **方法**: `POST`
- **內容類型**: `application/json`（如果有傳送請求體）

## 請求標頭

為了確保請求的合法性和安全性，請求中必須包含以下標頭：

| 標頭名稱      | 說明                                      | 範例                |
|---------------|-------------------------------------------|---------------------|
| `x-api-key`   | 用於驗證請求的 API 金鑰。確保只有授權的來源能夠發送請求。 | `your-api-key-value` |
| `x-order-id`  | 相關訂單的唯一標識符。                     | `1`       |

### 標頭詳解

1. **`x-api-key`**:
   - **用途**: 驗證請求是否來自授權的來源。
   - **獲取方式**: 您應該從 SmilePay 獲取並安全存儲此 API 金鑰。
   
2. **`x-order-id`**:
   - **用途**: 指定需要處理的訂單編號。
   - **格式**: 字串，應與您系統中的訂單編號一致。

## 請求範例

```http
POST /extensions/smilepay/webhook HTTP/1.1
Host: store.mcloudtw.com
Content-Type: application/json
x-api-key: your-api-key-value
x-order-id: ORDER123456

{
    "event": "payment.completed",
    "amount": 1000,
    "currency": "TWD",
    "timestamp": "2024-04-27T12:34:56Z"
}
```

*注意*: 請求體 (`body`) 的內容取決於您的業務需求，如果不需要，可以省略。

## Response

### 成功Response

- **狀態碼**: `200 OK`
- **內容**:
  ```json
  {
      "status": "success",
      "message": "Webhook processed successfully."
  }
  ```

### 錯誤Response

根據不同的錯誤情況，系統將回傳對應的狀態碼和錯誤信息。

| 狀態碼 | 錯誤類型            | 說明                                      |
|--------|---------------------|-------------------------------------------|
| 400    | **錯誤的請求**      | 缺少必要的標頭或請求體無效。              |
| 401    | **未授權**          | 提供的 `x-api-key` 無效或缺失。            |
| 500    | **內部服務器錯誤**  | 系統在處理請求時發生異常。                |

#### 錯誤Response範例

```json
{
    "error": "Unauthorized",
    "message": "Invalid API Key."
}
```

```json
{
    "error": "Missing order ID",
    "message": "The x-order-id header is required."
}
```

```json
{
    "error": "Internal Server Error",
    "message": "An unexpected error occurred."
}
```

## 錯誤處理

當發生錯誤時，系統將根據具體情況回傳對應的 HTTP 狀態碼和錯誤信息。開發者應根據這些信息進行對應的處理，例如記錄日誌、重發request或通知相關人員。

### 常見錯誤情況

1. **缺少標頭**:
   - **狀況**: 請求缺少 `x-api-key` 或 `x-order-id`。
   - **Response**: `400 Bad Request`，回傳錯誤信息。

2. **無效的 API 金鑰**:
   - **狀況**: 提供的 `x-api-key` 不正確。
   - **Response**: `401 Unauthorized`，回傳錯誤信息。

3. **處理異常**:
   - **狀況**: 系統在處理請求時發生異常。
   - **Response**: `500 Internal Server Error`，回傳錯誤信息。

## 安全性考量

為了確保 Webhook 的安全性，請考慮以下幾點：

1. **保護 API 金鑰**:
   - 不要將 `x-api-key` 泄露給未經授權的人。
   - 使用環境變數或安全的配置管理工具存儲 API 金鑰。

2. **使用 HTTPS**:
   - 確保 Webhook Route 使用 HTTPS，防止數據在傳輸過程中被竊聽或篡改。

3. **日誌記錄**:
   - 記錄所有 Webhook 請求和 Response，便於後續的Review和問題排查。