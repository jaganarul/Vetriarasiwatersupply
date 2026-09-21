<?php

/**
 * Migration: Add invoices table
 *
 * PostgreSQL/Supabase version.
 * Safe to run if the invoices table already exists.
 */

require_once __DIR__ . '/../init.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS invoices (
        id BIGSERIAL PRIMARY KEY,
        order_id BIGINT NOT NULL UNIQUE,
        invoice_number VARCHAR(100) NOT NULL UNIQUE,
        invoice_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        due_date DATE,
        subtotal NUMERIC(10,2) DEFAULT 0,
        tax NUMERIC(10,2) DEFAULT 0,
        total NUMERIC(10,2) NOT NULL,
        status VARCHAR(20) DEFAULT 'Draft',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_invoices_order
            FOREIGN KEY (order_id)
            REFERENCES orders(id)
            ON DELETE CASCADE
    );
    ";

    $pdo->exec($sql);

    echo "✅ Migration successful! Invoices table is ready.\n";

} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
