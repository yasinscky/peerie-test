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
                            {--email= : Admin email}
                            {--password= : Admin password}
                            {--name= : Admin name}
                            {--update : Promote existing user to admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create administrator or promote existing user to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email') ?: $this->ask('Admin email');
        $update = $this->option('update');

        if ($update) {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("User with email {$email} not found!");
                return 1;
            }

            $user->update(['is_admin' => true]);
            
            $this->info("User {$user->email} has been successfully promoted to admin!");
            $this->info("\nYou can now access the admin panel at: /admin");
            
            return 0;
        }

        $password = $this->option('password') ?: $this->secret('Admin password');
        $name = $this->option('name') ?: $this->ask('Admin name', 'Admin');

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
            $this->error('Validation errors:');
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

        $this->info("Admin user created successfully!");
        $this->info("Email: {$user->email}");
        $this->info("Name: {$user->name}");
        $this->info("\nYou can now access the admin panel at: /admin");

        return 0;
    }
}
