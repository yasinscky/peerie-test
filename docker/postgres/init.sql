-- Initialization script for PostgreSQL
-- This script runs when the PostgreSQL container starts for the first time

-- Create extensions if needed
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";

-- Set default timezone
SET timezone = 'Europe/Moscow';

-- Create indexes for better performance (will be created after migrations)
-- These are just examples, actual indexes should be in Laravel migrations

-- Grant permissions
GRANT ALL PRIVILEGES ON DATABASE marketing_planner TO postgres;
