#!/usr/bin/env bash

# Read .env values
ENV_FILE="$(dirname "$0")/../.env"

# Parse .env file for database settings
eval "$(grep -E '^database\.(default\.)?(hostname|database|username|password|port)=' "$ENV_FILE" | sed 's/^/export /')"

DB_HOST="${database_default_hostname:-localhost}"
DB_NAME="${database_default_database:-kanban_db}"
DB_USER="${database_default_username:-kanban_user}"
DB_PASS="${database_default_password:-changeme}"
DB_PORT="${database_default_port:-5432}"

echo "======================================"
echo "Database Setup"
echo "======================================"
echo "Host: $DB_HOST"
echo "Port: $DB_PORT"
echo "Database: $DB_NAME"
echo "User: $DB_USER"
echo "Password: ${DB_PASS:0:4}***"
echo "======================================"

# Ask for action
echo ""
echo "Select action:"
echo "  1) Create new (will fail if exists)"
echo "  2) Drop only (will delete existing data)"
echo "  3) Drop and recreate (will delete existing data)"
echo "  4) Check if exists only"
read -p "Enter choice [1-4]: " choice

case "$choice" in
    1)
        echo ""
        echo "Creating database and user..."
        sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';" &&
        sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;" &&
        sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"
        ;;
    2)
        echo ""
        echo "Dropping database and user..."
        sudo -u postgres psql -c "DROP DATABASE IF EXISTS $DB_NAME;"
        sudo -u postgres psql -c "DROP USER IF EXISTS $DB_USER;"
        ;;
    3)
        echo ""
        echo "Dropping existing database and user..."
        sudo -u postgres psql -c "DROP DATABASE IF EXISTS $DB_NAME;"
        sudo -u postgres psql -c "DROP USER IF EXISTS $DB_USER;"
        echo ""
        echo "Creating database and user..."
        sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';" &&
        sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;" &&
        sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"
        ;;
    4)
        echo ""
        echo "Checking database existence..."
        sudo -u postgres psql -lqt | cut -d \| -f 1 | grep -qw "$DB_NAME" && echo "✓ Database '$DB_NAME' exists" || echo "✗ Database '$DB_NAME' does not exist"
        sudo -u postgres psql -tAc "SELECT 1 FROM pg_roles WHERE rolname='$DB_USER'" | grep -q 1 && echo "✓ User '$DB_USER' exists" || echo "✗ User '$DB_USER' does not exist"
        ;;
    *)
        echo "Invalid choice. Exiting."
        exit 1
        ;;
esac

echo ""
echo "Done."