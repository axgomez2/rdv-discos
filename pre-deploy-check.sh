#!/bin/bash
# RDV Discos Pre-Deployment Check Script
# ======================================
# This script verifies that the codebase is ready for GitHub push and VPS deployment

echo "Running pre-deployment checks for RDV Discos..."
ERRORS=0

# Check if .env file exists but is not committed
if [ -f .env ]; then
    if git ls-files .env --error-unmatch 2>/dev/null; then
        echo "❌ ERROR: .env file is tracked by Git. Add it to .gitignore and remove from Git."
        ERRORS=$((ERRORS+1))
    else
        echo "✅ .env file is properly excluded from Git."
    fi
else
    echo "⚠️ WARNING: No .env file found. Make sure to create one on your VPS."
fi

# Check if .env.example exists
if [ -f .env.example ]; then
    echo "✅ .env.example file exists."
else
    echo "❌ ERROR: .env.example file not found. This is needed for deployment setup."
    ERRORS=$((ERRORS+1))
fi

# Check for storage symlink
if [ -L public/storage ] && [ -d "$(readlink public/storage)" ]; then
    echo "✅ Storage directory is properly linked."
else
    echo "⚠️ WARNING: Storage symlink not found or broken. Run 'php artisan storage:link' on deployment."
fi

# Check for compiled assets
if [ -d public/build ]; then
    echo "✅ Frontend assets are compiled."
else
    echo "⚠️ WARNING: Compiled assets not found. Run 'npm run build' before deployment."
fi

# Check composer.lock exists
if [ -f composer.lock ]; then
    echo "✅ composer.lock file exists."
else
    echo "❌ ERROR: composer.lock file not found. This is needed for consistent dependency installation."
    ERRORS=$((ERRORS+1))
fi

# Check for database migrations
if [ -d database/migrations ] && [ "$(ls -A database/migrations)" ]; then
    echo "✅ Database migrations exist."
else
    echo "❌ ERROR: No database migrations found."
    ERRORS=$((ERRORS+1))
fi

# Check for large files
echo "Checking for large files that shouldn't be committed..."
LARGE_FILES=$(find . -type f -size +10M -not -path "./vendor/*" -not -path "./node_modules/*" -not -path "./storage/*" -not -path "./public/storage/*")
if [ -n "$LARGE_FILES" ]; then
    echo "❌ ERROR: Large files found that may cause issues with Git:"
    echo "$LARGE_FILES"
    ERRORS=$((ERRORS+1))
else
    echo "✅ No problematic large files found."
fi

# Final assessment
if [ $ERRORS -eq 0 ]; then
    echo "✅ All checks passed! Your project is ready for GitHub push and VPS deployment."
else
    echo "❌ Found $ERRORS issues that should be resolved before deployment."
fi

exit $ERRORS
