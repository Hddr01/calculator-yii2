<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%calculation_history}}`.
 */
class m241220_120000_create_calculation_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Enable foreign keys for SQLite
        if ($this->db->driverName === 'sqlite') {
            $this->execute('PRAGMA foreign_keys = ON');
        }

        // Create table with SQLite-compatible syntax
        if ($this->db->driverName === 'sqlite') {
            $this->execute('
                CREATE TABLE {{%calculation_history}} (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    num1 REAL NOT NULL,
                    num2 REAL NOT NULL,
                    operation VARCHAR(20) NOT NULL,
                    result REAL NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES {{%user}} (id) ON DELETE CASCADE
                )
            ');
        } else {
            // For other databases
            $this->createTable('{{%calculation_history}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'num1' => $this->decimal(15, 8)->notNull(),
                'num2' => $this->decimal(15, 8)->notNull(),
                'operation' => $this->string(20)->notNull(),
                'result' => $this->decimal(15, 8)->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
            
            $this->addForeignKey(
                'fk-calculation_history-user_id',
                'calculation_history',
                'user_id',
                'user',
                'id',
                'CASCADE'
            );
        }

        // Add index for better performance
        $this->createIndex(
            'idx-calculation_history-user_id-created_at',
            'calculation_history',
            ['user_id', 'created_at']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop index
        $this->dropIndex(
            'idx-calculation_history-user_id-created_at',
            'calculation_history'
        );

        // For non-SQLite databases, drop foreign key first
        if ($this->db->driverName !== 'sqlite') {
            $this->dropForeignKey(
                'fk-calculation_history-user_id',
                'calculation_history'
            );
        }

        // Drop table
        $this->dropTable('{{%calculation_history}}');
    }
}