<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user 
                            {--email= : Email админа}
                            {--password= : Пароль админа}
                            {--name= : Имя админа}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать администратора для доступа в Filament админ-панель';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email') ?: $this->ask('Email админа');
        $password = $this->option('password') ?: $this->secret('Пароль админа');
        $name = $this->option('name') ?: $this->ask('Имя админа', 'Admin');

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
            'name' => $name,
        ], [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $this->error('Ошибки валидации:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - ' . $error);
            }
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info("Админ-пользователь успешно создан!");
        $this->info("Email: {$user->email}");
        $this->info("Имя: {$user->name}");
        $this->info("\nТеперь вы можете зайти в админку по адресу: /admin");

        return 0;
    }
}
