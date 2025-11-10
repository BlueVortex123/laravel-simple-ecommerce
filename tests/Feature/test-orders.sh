#!/bin/bash

echo "🚀 Laravel E-commerce Order Testing Suite"
echo "========================================="
echo ""

echo "1. Creating sample orders with Artisan command:"
echo "-----------------------------------------------"
php artisan test:order --count=3 --type=sample
echo ""

echo "2. Creating random order:"
echo "------------------------"
php artisan test:order --count=1 --type=random
echo ""

echo "3. Testing with specific user (if user ID 2 exists):"
echo "---------------------------------------------------"
php artisan test:order --user-id=2 --count=1 --type=config
echo ""

echo "Testing suite completed!"
echo ""
echo "💡 Tips:"
echo "- Use --user-id to test with specific users"
echo "- Use --count to create multiple orders"
echo "- Use --type=sample|random|config for different test scenarios"
echo "- Check storage/logs/laravel.log for detailed error information"