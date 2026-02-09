#!/bin/bash

# Chat Notification System - Verification Script
# Verify all components are properly installed

echo "🔍 Chat Notification System Verification"
echo "=========================================="
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

PROJECT_ROOT="/var/www/taurus-crm"
PASS=0
FAIL=0

# Helper functions
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $2"
        ((PASS++))
    else
        echo -e "${RED}✗${NC} $2 (File not found: $1)"
        ((FAIL++))
    fi
}

check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} $2"
        ((PASS++))
    else
        echo -e "${RED}✗${NC} $2 (Directory not found: $1)"
        ((FAIL++))
    fi
}

check_table() {
    result=$(cd "$PROJECT_ROOT" && php artisan tinker <<EOF 2>/dev/null
Schema::hasTable('$1') ? 'yes' : 'no'
EOF
)
    if [[ "$result" == *"yes"* ]]; then
        echo -e "${GREEN}✓${NC} $2"
        ((PASS++))
    else
        echo -e "${RED}✗${NC} $2 (Table not found: $1)"
        ((FAIL++))
    fi
}

echo -e "${BLUE}PHP Files${NC}"
check_file "$PROJECT_ROOT/app/Http/Controllers/ChatNotificationController.php" "ChatNotificationController"
check_file "$PROJECT_ROOT/app/Models/ChatNotificationPreference.php" "ChatNotificationPreference Model"
check_file "$PROJECT_ROOT/database/migrations/2026_01_28_create_chat_notification_preferences_table.php" "Migration File"

echo ""
echo -e "${BLUE}JavaScript Files${NC}"
check_file "$PROJECT_ROOT/resources/js/chat-notification-manager.js" "ChatNotificationManager"
check_file "$PROJECT_ROOT/resources/js/chat-notifications-integration.js" "Integration Script"
check_file "$PROJECT_ROOT/public/js/service-worker.js" "Service Worker"

echo ""
echo -e "${BLUE}View Files${NC}"
check_file "$PROJECT_ROOT/resources/views/chat/notifications/settings.blade.php" "Settings View"
check_file "$PROJECT_ROOT/resources/views/chat/notifications/modal.blade.php" "Modal Component"

echo ""
echo -e "${BLUE}Documentation${NC}"
check_file "$PROJECT_ROOT/CHAT_NOTIFICATIONS.md" "Main Documentation"
check_file "$PROJECT_ROOT/CHAT_NOTIFICATIONS_QUICKSTART.md" "Quick Start Guide"
check_file "$PROJECT_ROOT/CHAT_NOTIFICATIONS_INTEGRATION.md" "Integration Guide"
check_file "$PROJECT_ROOT/CHAT_NOTIFICATIONS_SUMMARY.md" "Implementation Summary"

echo ""
echo -e "${BLUE}Database${NC}"
check_table "chat_notification_preferences" "chat_notification_preferences Table"

echo ""
echo -e "${BLUE}Configuration${NC}"
if grep -q "ChatNotificationController" "$PROJECT_ROOT/routes/web.php"; then
    echo -e "${GREEN}✓${NC} Routes configured"
    ((PASS++))
else
    echo -e "${RED}✗${NC} Routes not configured in web.php"
    ((FAIL++))
fi

echo ""
echo "=========================================="
echo -e "${GREEN}Passed: $PASS${NC}"
if [ $FAIL -gt 0 ]; then
    echo -e "${RED}Failed: $FAIL${NC}"
fi
echo ""

if [ $FAIL -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed! System is ready.${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Include in chat view: @include('chat.notifications.modal')"
    echo "2. Add JavaScript: @vite(['resources/js/chat-notification-manager.js', 'resources/js/chat-notifications-integration.js'])"
    echo "3. Set user context: window.currentUserId = {{ auth()->id() }}"
    echo "4. Build assets: npm run build"
    echo "5. Test: Open chat, click bell icon, test notification"
    echo ""
    exit 0
else
    echo -e "${RED}✗ Some checks failed. Please review the issues above.${NC}"
    echo ""
    echo "Common fixes:"
    echo "1. Re-run migrations: php artisan migrate --force"
    echo "2. Check file permissions: chmod -R 755 app/ resources/ public/js/"
    echo "3. Clear cache: php artisan cache:clear"
    echo "4. Rebuild assets: npm run build"
    echo ""
    exit 1
fi
