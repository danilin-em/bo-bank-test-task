<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $users = [
                [
                    'name' => 'Иван Петров',
                    'email' => 'ivan.petrov@example.com',
                    'age' => 30,
                    'balance' => 100000,  // 1000.00 в копейках
                ],
                [
                    'name' => 'Мария Сидорова',
                    'email' => 'maria.sidorova@example.com',
                    'age' => 25,
                    'balance' => 50000,   // 500.00 в копейках
                ],
                [
                    'name' => 'Алексей Козлов',
                    'email' => 'alexey.kozlov@example.com',
                    'age' => 35,
                    'balance' => 0,       // 0.00 в копейках
                ],
            ];

            $accounts = [];

            // Создаем пользователей и их аккаунты
            foreach ($users as $userData) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'age' => $userData['age'],
                ]);

                $account = Account::create([
                    'user_id' => $user->id,
                    'balance' => 0, // Начальный баланс 0, пополним транзакциями
                ]);

                $accounts[] = [
                    'account' => $account,
                    'initial_balance' => $userData['balance'],
                ];
            }

            // Создаем транзакции пополнения для каждого аккаунта с балансом > 0
            foreach ($accounts as $accountData) {
                if ($accountData['initial_balance'] > 0) {
                    // Создаем транзакцию пополнения
                    Transaction::create([
                        'from_account_id' => null, // Пополнение извне (нет источника)
                        'to_account_id' => $accountData['account']->id,
                        'amount' => $accountData['initial_balance'],
                        'type' => 'deposit',
                        'status' => 'completed',
                        'reference_id' => 'SEED_' . Str::upper(Str::random(8)),
                    ]);

                    $accountData['account']->update([
                        'balance' => $accountData['initial_balance'],
                    ]);
                }
            }
        });
    }
}
