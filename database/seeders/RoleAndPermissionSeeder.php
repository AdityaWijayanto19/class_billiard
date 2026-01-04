<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Role-Based Access Control (RBAC) Seeder
     *
     * Defines 50 permissions across 11 domains and assigns them to 3 roles:
     * - super_admin: Full system access (50 permissions)
     * - admin: CMS, menus, categories, reports (22 permissions)
     * - kitchen: Kitchen operations only (12 permissions)
     *
     * Permission domains:
     * 1. ORDER (9) - Order creation, management, completion
     * 2. PAYMENT (6) - Payment confirmation, reports, refunds
     * 3. KITCHEN (5) - Kitchen display, status updates
     * 4. MENU (7) - Menu CRUD, pricing, categories
     * 5. CATEGORY (4) - Category management
     * 6. TABLE (5) - Table management & QR codes
     * 7. REPORT (5) - Sales, hourly, email reports
     * 8. USER (4) - User management
     * 9. ROLE (2) - Role and permission management
     * 10. INVENTORY (3) - Food inventory management
     * 11. NOTIFICATION (2) - Notification sounds management
     */
    public function run(): void
    {
        // Clear cache to avoid stale data
        app()['cache']->forget('spatie.permission.cache');

        $this->command->info('🔐 Starting RBAC setup...');
        $this->command->line('');

        // ================================================================
        // STEP 1: CREATE 47 PERMISSIONS ACROSS 8 DOMAINS
        // ================================================================

        $this->createOrderPermissions();
        $this->createPaymentPermissions();
        $this->createKitchenPermissions();
        $this->createMenuPermissions();
        $this->createCategoryPermissions();
        $this->createTablePermissions();
        $this->createReportPermissions();
        $this->createUserPermissions();
        $this->createRolePermissions();
        $this->createInventoryPermissions();
        $this->createNotificationPermissions();

        // ================================================================
        // STEP 2: CREATE 3 ROLES
        // ================================================================

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $kitchen = Role::firstOrCreate(['name' => 'kitchen', 'guard_name' => 'web']);

        // ================================================================
        // STEP 3: ASSIGN PERMISSIONS TO ROLES
        // ================================================================

        $this->assignSuperAdminPermissions($superAdmin);
        $this->assignAdminPermissions($admin);
        $this->assignKitchenPermissions($kitchen);

        // ================================================================
        // SUMMARY
        // ================================================================

        $this->displaySummary($superAdmin, $admin, $kitchen);
    }

    /**
     * Create ORDER permissions (9)
     * Controls: Order creation, viewing, updating, deletion, marking ready, completing
     */
    private function createOrderPermissions(): void
    {
        $permissions = [
            'order.view',          // List all orders
            'order.view_history',  // View order history/reports
            'order.create',        // Create new order
            'order.show',          // View single order detail
            'order.update',        // Update order data
            'order.delete',        // Delete order (only pending)
            'order.mark_ready',    // Mark order as ready
            'order.complete',      // Mark order as completed
            'order.cancel',        // Cancel order
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 9 ORDER permissions created');
    }

    /**
     * Create PAYMENT permissions (6)
     * Controls: Payment confirmation, viewing, reporting, refunds
     */
    private function createPaymentPermissions(): void
    {
        $permissions = [
            'payment.view',        // View payment data
            'payment.confirm',     // Confirm payment received
            'payment.view_reports', // View payment reports
            'payment.refund',      // Process refund
            'payment.export',      // Export payment data
            'payment.audit_log',   // View audit trail
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 6 PAYMENT permissions created');
    }

    /**
     * Create KITCHEN permissions (5)
     * Controls: Kitchen display, order status updates
     */
    private function createKitchenPermissions(): void
    {
        $permissions = [
            'kitchen.view_orders',    // View orders on kitchen display
            'kitchen.update_status',  // Update order status
            'kitchen.mark_ready',     // Mark order ready for pickup
            'kitchen.view_queue',     // View order queue/priority
            'kitchen.manage_sounds',  // Manage notification sounds
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 5 KITCHEN permissions created');
    }

    /**
     * Create MENU permissions (7)
     * Controls: Menu item CRUD, pricing, categories, availability
     */
    private function createMenuPermissions(): void
    {
        $permissions = [
            'menu.view',               // View menu items
            'menu.create',             // Create new menu item
            'menu.update',             // Update menu item
            'menu.delete',             // Delete menu item
            'menu.update_price',       // Update pricing
            'menu.toggle_availability', // Enable/disable items
            'menu.view_categories',    // View menu categories
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 7 MENU permissions created');
    }

    /**
     * Create CATEGORY permissions (4)
     * Controls: Menu category management
     */
    private function createCategoryPermissions(): void
    {
        $permissions = [
            'category.view',   // View categories
            'category.create', // Create category
            'category.update', // Update category
            'category.delete', // Delete category
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 4 CATEGORY permissions created');
    }

    /**
     * Create TABLE permissions (5)
     * Controls: Billiard table management and QR codes
     */
    private function createTablePermissions(): void
    {
        $permissions = [
            'table.view',   // View tables
            'table.manage', // Manage table status
            'table.create', // Create new table
            'table.update', // Update table info
            'table.delete', // Delete table
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 5 TABLE permissions created');
    }

    /**
     * Create REPORT permissions (5)
     * Controls: Sales reports, hourly analytics, exports, email
     */
    private function createReportPermissions(): void
    {
        $permissions = [
            'report.view',        // View reports page
            'report.view_sales',  // View sales reports
            'report.view_hourly', // View hourly breakdown
            'report.export',      // Export to Excel/PDF
            'report.send_email',  // Send reports via email
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 5 REPORT permissions created');
    }

    /**
     * Create USER permissions (4)
     * Controls: User management (super_admin only)
     */
    private function createUserPermissions(): void
    {
        $permissions = [
            'user.view',   // View users
            'user.create', // Create user
            'user.update', // Update user
            'user.delete', // Delete user
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 4 USER permissions created');
    }

    /**
     * Create ROLE permissions (2)
     * Controls: Role and permission assignment (super_admin only)
     */
    private function createRolePermissions(): void
    {
        $permissions = [
            'role.view',   // View roles and permissions
            'role.assign', // Assign roles to users
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 2 ROLE permissions created');
    }

    /**
     * Create INVENTORY permissions (3)
     * Controls: Food inventory management (super_admin only)
     */
    private function createInventoryPermissions(): void
    {
        $permissions = [
            'inventory.view',   // View inventory
            'inventory.update', // Update inventory quantity/reorder level
            'inventory.delete', // Delete inventory item
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 3 INVENTORY permissions created');
    }

    /**
     * Create NOTIFICATION permissions (2)
     * Controls: Notification sounds management
     */
    private function createNotificationPermissions(): void
    {
        $permissions = [
            'notification.view',   // View notification sounds
            'notification.manage', // Manage notification sounds
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->line('✅ 2 NOTIFICATION permissions created');
    }

    /**
     * Assign all permissions to super_admin role (47 permissions)
     * Super admin has complete access to all system features
     */
    private function assignSuperAdminPermissions($role): void
    {
        $role->syncPermissions(Permission::all());
    }

    /**
     * Assign permissions to admin role (3 permissions)
     *
     * Admin can:
     * - Handle incoming orders ONLY (view, show, cancel)
     * - Website CMS access (via role-based check)
     * - All other features (menus, categories, tables, payments, reports) require explicit permission grant by super_admin
     */
    private function assignAdminPermissions($role): void
    {
        $permissions = [
            // ORDER (3) - Handle incoming orders ONLY
            'order.view',
            'order.show',
            'order.cancel',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Assign permissions to kitchen role (12 permissions)
     *
     * Kitchen staff can:
     * - View and complete orders
     * - Manage kitchen operations (status, ready, queue)
     * - View menus and tables (read-only)
     */
    private function assignKitchenPermissions($role): void
    {
        $permissions = [
            // ORDER (3) - Kitchen operations
            'order.view',
            'order.show',
            'order.complete',

            // KITCHEN (5) - Full control
            'kitchen.view_orders',
            'kitchen.update_status',
            'kitchen.mark_ready',
            'kitchen.view_queue',
            'kitchen.manage_sounds',

            // MENU (2) - Read-only
            'menu.view',
            'menu.view_categories',

            // TABLE (1) - Read-only
            'table.view',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Display setup summary in console
     */
    private function displaySummary($superAdmin, $admin, $kitchen): void
    {
        $this->command->line('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✅ RBAC SETUP COMPLETE');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->line('');

        $this->command->line('📊 TOTAL PERMISSIONS: 50');
        $this->command->line('');

        $this->command->info('🔓 ROLE SUMMARY:');
        $this->command->line('');
        $this->command->line('  📍 super_admin');
        $this->command->line('     Permissions: '.$superAdmin->permissions->count().'/47');
        $this->command->line('     Access: FULL SYSTEM ACCESS');
        $this->command->line('');

        $this->command->line('  📍 admin');
        $this->command->line('     Permissions: '.$admin->permissions->count().'/47');
        $this->command->line('     Access: Menu, Category, Table, Order (read), Payment (read), Reports');
        $this->command->line('');

        $this->command->line('  📍 kitchen');
        $this->command->line('     Permissions: '.$kitchen->permissions->count().'/47');
        $this->command->line('     Access: Kitchen operations, Order completion, View menus/tables');
        $this->command->line('');

        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✅ Ready to use! Run: php artisan db:seed --class=SetupUsersWithRolesSeeder');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
