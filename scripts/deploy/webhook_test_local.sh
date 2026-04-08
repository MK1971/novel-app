#!/usr/bin/env bash
set -euo pipefail

if [[ $# -lt 2 ]]; then
  echo "Usage: $0 <base_url> <webhook_token> [user_id] [order_id]"
  echo "Example: $0 http://127.0.0.1:8000 wmnb-local-webhook-... 2 ORDER-LOCAL-123"
  exit 1
fi

BASE_URL="$1"
TOKEN="$2"
USER_ID="${3:-2}"
ORDER_ID="${4:-ORDER-LOCAL-123}"

curl -i -sS -X POST "${BASE_URL%/}/payment/donation/webhook" \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Token: ${TOKEN}" \
  -d "{\"event_type\":\"PAYMENT.CAPTURE.COMPLETED\",\"resource\":{\"id\":\"CAPTURE-LOCAL-1\",\"custom_id\":\"user:${USER_ID}\",\"amount\":{\"value\":\"12.00\"},\"supplementary_data\":{\"related_ids\":{\"order_id\":\"${ORDER_ID}\"}}}}"

echo
