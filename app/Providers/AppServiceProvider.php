<?php

// namespace App\Providers;

// use Illuminate\Support\ServiceProvider;

// use App\Models\User; // Importa el modelo User
// use Illuminate\Support\Facades\Gate; // Importa la fachada de Gates

// class AppServiceProvider extends ServiceProvider
// {
//     /**
//      * Register any application services.
//      */
//     public function register(): void
//     {
//         //
//     }

//     /**
//      * Bootstrap any application services.
//      */
//     public function boot(): void
//     {
//         //
//         // *** AÑADE ESTE CÓDIGO AQUÍ ***
//     // Define el Gate 'admin-access' que requiere tu middleware en las rutas
//     Gate::define('admin-access', function (User $user) {
        
//         // Verifica si el rol del usuario logueado es 'admin'
//         // Sabemos que tu usuario 'juan' tiene 'role' = 'admin'
//         return $user->role === 'admin'; 
//     });
//     }
// }



namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use App\Models\User; // Usado para el Gate antiguo (lo mantendremos por compatibilidad)

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Este método se ejecuta después de que todos los Service Providers estén registrados.
     */
    public function boot(): void
    {
        // --------------------------------------------------------------------------
        // 1. SOLUCIÓN AL ERROR 'Target class [role] does not exist.' (Middlewares Spatie)
        // --------------------------------------------------------------------------
        
        // Obtenemos la instancia del Router. Esto es NECESARIO si Kernel.php no funciona.
        $router = $this->app->make(\Illuminate\Routing\Router::class);
        
        // Registramos los aliases de los middlewares de Spatie.
        $router->aliasMiddleware('role', \Spatie\Permission\Middleware\RoleMiddleware::class);
        $router->aliasMiddleware('permission', \Spatie\Permission\Middleware\PermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class);

        // --------------------------------------------------------------------------
        // 2. GATE ANTIGUO (Lo mantengo por si aún tienes rutas que lo usan)
        // --------------------------------------------------------------------------
        Gate::define('admin-access', function (User $user) {
            // Verifica el campo 'role' en la tabla 'users'.
            return $user->role === 'admin'; 
        });
    }
}
