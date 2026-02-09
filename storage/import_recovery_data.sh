#!/bin/bash
# Script to safely import recovered binlog data
# Date: February 2, 2026

echo "=== MySQL Binary Log Data Import Script ==="
echo ""

# Step 1: Create backup of current database state
echo "Step 1: Creating backup of current (empty) database state..."
mysqldump -u root taurus_crm > /var/www/taurus-crm/storage/backup_before_recovery_$(date +%Y%m%d_%H%M%S).sql
echo "✓ Backup created"
echo ""

# Step 2: Show current data count
echo "Step 2: Current database state:"
mysql -u root taurus_crm -e "SELECT 
    (SELECT COUNT(*) FROM leads) as leads_count,
    (SELECT COUNT(*) FROM attendances) as attendance_count,
    (SELECT COUNT(*) FROM users) as users_count;"
echo ""

# Step 3: Import the recovery file
echo "Step 3: Starting import of recovery_attempt.sql (71 MB)..."
echo "This may take several minutes. Please wait..."
mysql -u root taurus_crm < /var/www/taurus-crm/storage/recovery_attempt.sql 2>&1 | tee /var/www/taurus-crm/storage/import_log.txt
echo ""

# Step 4: Verify import
echo "Step 4: Verifying imported data..."
mysql -u root taurus_crm -e "SELECT 
    (SELECT COUNT(*) FROM leads) as leads_count,
    (SELECT COUNT(*) FROM attendances) as attendance_count,
    (SELECT COUNT(*) FROM users) as users_count;"
echo ""

echo "✓ Import complete!"
echo ""
echo "Import log saved to: /var/www/taurus-crm/storage/import_log.txt"
echo "Backup saved to: /var/www/taurus-crm/storage/backup_before_recovery_*.sql"
