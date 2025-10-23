<?php




// namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
// use Illuminate\Support\Facades\Gate; 
// use App\Models\User; // Usado para el Gate antiguo (lo mantendremos por compatibilidad)

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
//      *
//      * Este método se ejecuta después de que todos los Service Providers estén registrados.
//      */
//     public function boot(): void
//     {
//         // --------------------------------------------------------------------------
//         // 1. SOLUCIÓN AL ERROR 'Target class [role] does not exist.' (Middlewares Spatie)
//         // --------------------------------------------------------------------------
        
//         // Obtenemos la instancia del Router. Esto es NECESARIO si Kernel.php no funciona.
//         $router = $this->app->make(\Illuminate\Routing\Router::class);
        
//         // Registramos los aliases de los middlewares de Spatie.
//         $router->aliasMiddleware('role', \Spatie\Permission\Middleware\RoleMiddleware::class);
//         $router->aliasMiddleware('permission', \Spatie\Permission\Middleware\PermissionMiddleware::class);
//         $router->aliasMiddleware('role_or_permission', \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class);

//         // --------------------------------------------------------------------------
//         // 2. GATE ANTIGUO (Lo mantengo por si aún tienes rutas que lo usan)
//         // --------------------------------------------------------------------------
//         Gate::define('admin-access', function (User $user) {
//             // Verifica el campo 'role' en la tabla 'users'.
//             return $user->role === 'admin'; 
//         });
//     }
// }




namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use App\Models\User; // Asegúrate de que este modelo exista y esté correctamente configurado

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
        // 0. SOLUCIÓN AL ERROR 403: BYPASS PARA ADMINISTRADORES
        //    Esto anula cualquier restricción fallida (como un almacen_id nulo)
        // --------------------------------------------------------------------------
        Gate::before(function ($user, $ability) {
            // Si el usuario tiene el rol 'admin', se le concede acceso total incondicionalmente.
            if ($user->role === 'admin') {
                return true; 
            }
        });


        // --------------------------------------------------------------------------
        // 1. SOLUCIÓN AL ERROR 'Target class [role] does not exist.' (Middlewares Spatie)
        // --------------------------------------------------------------------------
        
        // Obtenemos la instancia del Router.
        $router = $this->app->make(\Illuminate\Routing\Router::class);
        
        // Registramos los aliases de los middlewares de Spatie.
        $router->aliasMiddleware('role', \Spatie\Permission\Middleware\RoleMiddleware::class);
        $router->aliasMiddleware('permission', \Spatie\Permission\Middleware\PermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class);

        // --------------------------------------------------------------------------
        // 2. GATE ANTIGUO (Se mantiene por compatibilidad)
        // --------------------------------------------------------------------------
        Gate::define('admin-access', function (User $user) {
            // Este Gate será ignorado si el Gate::before devuelve TRUE.
            return $user->role === 'admin'; 
        });
    }
}