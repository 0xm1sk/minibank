<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $roles = [
            [
                "id" => Role::REGULAR_CLIENT,
                "name" => "Regular Client",
                "permissions" => [
                    "view_own_account",
                    "make_transactions",
                    "view_own_transactions",
                ],
                "level" => 1,
            ],
            [
                "id" => Role::VIP_CLIENT,
                "name" => "VIP Client",
                "permissions" => [
                    "view_own_account",
                    "make_transactions",
                    "view_own_transactions",
                    "higher_transaction_limit",
                    "priority_support",
                ],
                "level" => 2,
            ],
            [
                "id" => Role::ENTERPRISE_CLIENT,
                "name" => "Enterprise Client",
                "permissions" => [
                    "view_own_account",
                    "make_transactions",
                    "view_own_transactions",
                    "bulk_transactions",
                    "multiple_accounts",
                    "business_features",
                ],
                "level" => 3,
            ],
            [
                "id" => Role::EMPLOYEE,
                "name" => "Employee",
                "permissions" => [
                    "search_users",
                    "view_client_details",
                    "assist_customers",
                ],
                "level" => 4,
            ],
            [
                "id" => Role::MANAGER,
                "name" => "Manager",
                "permissions" => [
                    "search_users",
                    "view_client_details",
                    "assist_customers",
                    "approve_transactions",
                    "view_reports",
                    "manage_employees",
                ],
                "level" => 5,
            ],
            [
                "id" => Role::SUPERVISOR,
                "name" => "Supervisor",
                "permissions" => [
                    "search_users",
                    "view_client_details",
                    "assist_customers",
                    "approve_transactions",
                    "view_reports",
                    "manage_employees",
                    "view_all_transactions",
                    "system_monitoring",
                ],
                "level" => 6,
            ],
            [
                "id" => Role::CEO,
                "name" => "CEO",
                "permissions" => [
                    "search_users",
                    "view_client_details",
                    "assist_customers",
                    "approve_transactions",
                    "view_reports",
                    "manage_employees",
                    "view_all_transactions",
                    "system_monitoring",
                    "full_access",
                    "strategic_decisions",
                ],
                "level" => 7,
            ],
            [
                "id" => Role::ADMIN,
                "name" => "Admin",
                "permissions" => [
                    "search_users",
                    "view_client_details",
                    "assist_customers",
                    "approve_transactions",
                    "view_reports",
                    "manage_employees",
                    "view_all_transactions",
                    "system_monitoring",
                    "full_access",
                    "manage_users",
                    "system_configuration",
                    "security_management",
                ],
                "level" => 8,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(["id" => $roleData["id"]], $roleData);
        }
    }
}
