# DSS API Reference

## Authentication
All API endpoints require authentication via `Authorization: Bearer {token}` header.

## Base URL
```
/api/dss
```

---

## Dead Stock Endpoints

### 1. List Dead Stocks
**Endpoint:** `GET /api/dss/dead-stocks`

**Parameters:**
```
- search (optional): Search by product name, SKU, or brand
- priority (optional): Filter by Critical|High|Medium|Low
- sort_by (optional): Sort by days_without_sale|stock_value|priority_level|detected_at
- sort_order (optional): asc|desc
- per_page (optional): Default 20
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "product_id": 123,
      "warehouse_id": 1,
      "days_without_sale": 95,
      "last_sold_date": "2026-05-17T10:30:00Z",
      "current_stock": 50,
      "stock_value": "15000.00",
      "priority_level": "Medium",
      "is_active": true,
      "detected_at": "2026-04-17T10:30:00Z",
      "product": {
        "id": 123,
        "name": "Engine Oil",
        "sku": "OIL-001",
        "brand": "Motul"
      }
    }
  ],
  "pagination": { "total": 42, "per_page": 20, "current_page": 1 }
}
```

### 2. Get Dead Stock Detail
**Endpoint:** `GET /api/dss/dead-stocks/{id}`

**Response:**
```json
{
  "id": 1,
  "product_id": 123,
  "days_without_sale": 95,
  "priority_level": "Medium",
  "stock_value": "15000.00",
  "recommendations": [
    {
      "id": 1,
      "recommendation_type": "promotion",
      "title": "Launch Promotional Campaign",
      "priority": "Medium"
    }
  ]
}
```

### 3. Get Dead Stocks by Priority
**Endpoint:** `GET /api/dss/dead-stocks/priority/{priority}`

**Parameters:**
```
priority: Critical|High|Medium|Low
```

**Response:** Array of dead stocks with specified priority

### 4. Mark Dead Stock as Resolved
**Endpoint:** `POST /api/dss/dead-stocks/{id}/resolve`

**Response:**
```json
{
  "success": true,
  "message": "Dead stock marked as resolved."
}
```

### 5. Recalculate All Dead Stocks
**Endpoint:** `POST /api/dss/dead-stocks/recalculate`

**Response:**
```json
{
  "success": true,
  "message": "Dead stock analysis recalculated successfully."
}
```

### 6. Export Dead Stocks as CSV
**Endpoint:** `GET /api/dss/dead-stocks/export/csv`

**Parameters:**
```
- priority (optional): Filter by priority level
```

**Response:** CSV file download with fields:
- SKU, Product Name, Brand, Current Stock, Stock Value, Days Without Sale, Last Sold Date, Priority, Recommendations

### 7. Get Dashboard Statistics
**Endpoint:** `GET /api/dss/dashboard-stats`

**Response:**
```json
{
  "total": 42,
  "countByPriority": {
    "Critical": 5,
    "High": 12,
    "Medium": 18,
    "Low": 7
  },
  "totalValue": 125000.50
}
```

### 8. Get Top Fast Moving Products
**Endpoint:** `GET /api/dss/top-fast-moving`

**Parameters:**
```
- limit (optional): Default 5, max 10
```

**Response:**
```json
[
  {
    "id": 456,
    "name": "Brake Pads",
    "sku": "BP-001",
    "velocity_score": 85.50
  }
]
```

---

## Recommendation Endpoints

### 1. Get Recommendations by Product
**Endpoint:** `GET /api/dss/recommendations/product/{productId}`

**Response:**
```json
[
  {
    "id": 1,
    "product_id": 123,
    "recommendation_type": "promotion",
    "title": "Launch Promotional Campaign",
    "description": "This product has not been sold for 95 days...",
    "priority": "Medium",
    "metadata": null,
    "is_active": true,
    "generated_at": "2026-07-16T10:30:00Z",
    "action_taken_at": null,
    "action_notes": null
  }
]
```

### 2. Get Pending Recommendations
**Endpoint:** `GET /api/dss/recommendations/pending`

**Parameters:**
```
- limit (optional): Default 50
```

**Response:** Array of recommendations with `action_taken_at` = null

### 3. Get Recommendations by Type
**Endpoint:** `GET /api/dss/recommendations/type/{type}`

**Parameters:**
```
type: promotion|discount|bundle|relocate|featured_display|social_media|supplier_return
per_page (optional): Default 20
```

**Response:** Paginated recommendations of specified type

### 4. Mark Recommendation as Actioned
**Endpoint:** `POST /api/dss/recommendations/{id}/action`

**Request Body:**
```json
{
  "action_notes": "Created promotional campaign on Facebook"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Recommendation marked as actioned."
}
```

### 5. Get Pending Recommendations Count
**Endpoint:** `GET /api/dss/recommendations/pending-count`

**Response:**
```json
{
  "count": 18
}
```

### 6. Get Recommendations Count by Type
**Endpoint:** `GET /api/dss/recommendations/count-by-type`

**Response:**
```json
{
  "promotion": 12,
  "discount": 8,
  "bundle": 10,
  "relocate": 5,
  "featured_display": 7,
  "social_media": 6,
  "supplier_return": 2
}
```

---

## Settings Endpoints

### 1. Get All Settings
**Endpoint:** `GET /api/dss/settings`

**Response:**
```json
{
  "dead_stock_threshold_days": 90,
  "slow_moving_threshold_days": 60,
  "fast_moving_threshold_units": 50,
  "bundle_recommendation_enabled": true,
  "promotion_recommendation_enabled": true,
  "discount_recommendation_enabled": true,
  "dss_analysis_enabled": true
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "error": "Invalid parameter value",
  "message": "Detailed error message"
}
```

### 404 Not Found
```json
{
  "error": "Resource not found",
  "message": "The requested resource does not exist"
}
```

### 422 Unprocessable Entity
```json
{
  "message": "The given data was invalid",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 500 Server Error
```json
{
  "message": "Server error occurred",
  "error": "Error details (dev only)"
}
```

---

## HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created |
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Permission denied |
| 404 | Not Found - Resource doesn't exist |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Server Error - Internal server error |

---

## Rate Limiting

API requests are rate-limited to:
- **60 requests per minute** per authenticated user
- **Headers returned:**
  - `X-RateLimit-Limit`
  - `X-RateLimit-Remaining`
  - `X-RateLimit-Reset`

---

## Pagination

List endpoints support pagination:

**Parameters:**
```
- page (optional): Page number (default: 1)
- per_page (optional): Items per page (default: 20, max: 100)
```

**Response Includes:**
```json
{
  "data": [...],
  "links": {
    "first": "url",
    "last": "url",
    "prev": "url",
    "next": "url"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 20,
    "to": 20,
    "total": 42
  }
}
```

---

## Filtering & Sorting

### Dead Stocks
**Sort Fields:**
- `days_without_sale` (default)
- `stock_value`
- `priority_level`
- `detected_at`

**Sort Order:**
- `asc` - Ascending (default)
- `desc` - Descending

### Recommendations
**Filter Fields:**
- `type` - Recommendation type
- `priority` - Priority level
- `status` - pending|actioned

---

## Example Requests

### Get Critical Dead Stocks
```bash
curl -X GET "http://localhost:8000/api/dss/dead-stocks?priority=Critical&sort_by=days_without_sale&sort_order=desc" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Get Pending Recommendations
```bash
curl -X GET "http://localhost:8000/api/dss/recommendations/pending?limit=10" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Mark Recommendation as Actioned
```bash
curl -X POST "http://localhost:8000/api/dss/recommendations/1/action" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "action_notes": "Created promotional campaign"
  }'
```

### Recalculate Analysis
```bash
curl -X POST "http://localhost:8000/api/dss/dead-stocks/recalculate" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Export Dead Stocks
```bash
curl -X GET "http://localhost:8000/api/dss/dead-stocks/export/csv?priority=Critical" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o dead_stocks.csv
```

---

## Response Timing

Typical response times:

| Endpoint | Time |
|----------|------|
| List dead stocks | 200-500ms |
| Get statistics | 100-300ms |
| Get recommendations | 150-400ms |
| Recalculate analysis | 2-10s |
| Export CSV | 500ms-2s |

---

## Versioning

Current API Version: **v1** (implicit)

Future versions may be available at:
- `/api/v2/dss/...` (to be added)

---

## Webhooks (Future)

Coming soon:
- Dead stock detected webhook
- Recommendation generated webhook
- Analysis completed webhook

---

## SDK/Client Libraries

For easier integration:

**JavaScript/TypeScript** (to be added):
```javascript
const dss = new DSSClient(apiToken);
const deadStocks = await dss.getDeadStocks({ priority: 'Critical' });
```

**PHP** (to be added):
```php
$dss = new DSSClient($apiToken);
$deadStocks = $dss->getDeadStocks(['priority' => 'Critical']);
```

---

## Changelog

### v1.0.0 (July 16, 2026)
- Initial release
- 8 dead stock endpoints
- 6 recommendation endpoints
- 1 settings endpoint
- CSV export support
- Full pagination & filtering

---

## Support

For API issues or questions:
1. Check this reference
2. Review `DSS_MODULE_DOCUMENTATION.md`
3. Check code comments in API controllers
4. Contact development team

---

**Last Updated:** July 16, 2026
**Maintained By:** Development Team
**License:** Proprietary
