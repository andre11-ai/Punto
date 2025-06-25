        <?php

        use App\Http\Controllers\Auth\AuthenticatedSessionController;
        use App\Http\Controllers\CategoriaController;
        use App\Http\Controllers\ClienteController;
        use App\Http\Controllers\CompaniaController;
        use App\Http\Controllers\DashboardController;
        use App\Http\Controllers\DatatableController;
        use App\Http\Controllers\ProductoController;
        use App\Http\Controllers\ProfileController;
        use App\Http\Controllers\UsuarioController;
        use App\Http\Controllers\VentaController;
        use App\Http\Controllers\ProveedoresController;
        use App\Http\Controllers\PromocionController;




        use Illuminate\Support\Facades\Route;

            Route::get('/', [AuthenticatedSessionController::class, 'create'])
                        ->name('login');


            Route::middleware('auth')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            Route::resource('productos', ProductoController::class);
          //  Route::resource('clientes', ClienteController::class);
            Route::resource('categorias', CategoriaController::class);
            Route::resource('proveedores', ProveedoresController::class);

            Route::resource('usuarios', UsuarioController::class);

            Route::put('/proveedores/{id}', [ProveedoresController::class, 'update'])->name('proveedores.update');
            Route::get('/api/categoria-info/{id}', [CategoriaController::class, 'getCategoriaInfo'])->name('api.categoria.info');
            Route::get('/proveedores-por-categoria/{id}', [CategoriaController::class, 'proveedoresPorCategoria']);

            Route::get('/listarProductos', [DatatableController::class, 'products'])->name('products.list');
           // Route::get('/listarClientes', [DatatableController::class, 'clients'])->name('clients.list');
            Route::get('/listarUsuarios', [DatatableController::class, 'users'])->name('users.list');
            Route::get('/listarCategorias', [DatatableController::class, 'categories'])->name('categories.list');
Route::get('/listarVentas', [VentaController::class, 'listVentas'])->name('sales.list');
            Route::get('usuarios/list', [UsuarioController::class, 'list'])->name('usuarios.list');
            Route::get('/compania', [CompaniaController::class, 'index'])->name('compania.index');
            Route::put('/compania/{compania}', [CompaniaController::class, 'update'])->name('compania.update');
            Route::get('/categories/list', [CategoriaController::class, 'list'])->name('categories.list');
            Route::get('/productos/list', [ProductoController::class, 'list'])->name('products.list');
            Route::get('/products/list', [ProductoController::class, 'list'])->name('products.list');
            Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
            Route::post('/promociones/guardar', [PromocionController::class, 'guardar'])->name('promociones.guardar');
            Route::get('/productos/{id}/edit-promocion', [ProductoController::class, 'editPromocion'])->name('productos.edit-promocion');
            Route::put('/productos/{id}/update-promocion', [ProductoController::class, 'updatePromocion'])->name('productos.update-promocion');
            Route::delete('/productos/{id}/remove-promocion', [ProductoController::class, 'removePromocion'])->name('productos.remove-promocion');
            Route::get('/promociones/categorias/{proveedor}',[PromocionController::class, 'getCategorias'])->name('promociones.categorias');
            Route::post('/promociones/guardar', [PromocionController::class, 'guardar'])->name('promociones.guardar');
            Route::get('/promociones/verificar', [PromocionController::class, 'verificarExpiradas'])->name('promociones.verificar');
            Route::get('/productos/{id}/edit-promocion', [ProductoController::class, 'editPromocion'])->name('productos.edit-promocion');

            Route::post('/productos/{id}/update-promocion', [ProductoController::class, 'updatePromocion'])->name('productos.update-promocion');
            Route::get('/promociones', [ProductoController::class, 'index'])->name('promociones.index');
            Route::get('/promociones/proveedores', [PromocionController::class, 'getProveedores'])->name('promociones.proveedores');
            Route::get('/promociones/productos/{categoria}', [PromocionController::class, 'getProductos']);
            Route::get('/promociones/categorias/{proveedorId}', [PromocionController::class, 'getCategorias'])->name('promociones.categorias');
            Route::get('/proveedores/list', [ProveedoresController::class, 'list'])->name('proveedores.list');
            Route::get('/venta', [VentaController::class, 'index'])->name('venta.index');
            Route::get('/venta/show', [VentaController::class, 'show'])->name('venta.show');
            Route::get('/venta/cliente', [VentaController::class, 'cliente'])->name('venta.cliente');
            Route::post('/venta', [VentaController::class, 'store'])->name('venta.store');
            Route::get('/venta/{id}/ticket', [VentaController::class, 'ticket'])->name('venta.ticket');


    Route::post('/venta', [VentaController::class, 'procesarVenta'])->name('venta.store');
// CLIENTES
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
Route::get('/clientes/list', [ClienteController::class, 'listarClientes'])->name('clients.list');
Route::post('/guardar-deudor', [ClienteController::class, 'guardarDeudor'])->name('guardar.deudor');
Route::post('/clientes/{id}/abonar', [ClienteController::class, 'registrarAbono']);
Route::get('/clientes/{id}/abono-ticket', [ClienteController::class, 'ticketAbono'])->name('generar.ticket.abono');
Route::get('/venta/{id}/ticket-abono', [VentaController::class, 'ticketAbono'])->name('ventas.ticket.abono');

Route::get('/venta/{cliente}/registrar-abono-final/{monto}', [VentaController::class, 'registrarAbonoFinal']);
Route::get('/ventas/ticket/{id}', [VentaController::class, 'mostrarTicket'])->name('ventas.ticket');



        });

        require __DIR__ . '/auth.php';
