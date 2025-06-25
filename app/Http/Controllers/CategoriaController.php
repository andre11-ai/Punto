<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedores;
use Illuminate\Http\Request;


/**
 * Class CategoriaController
 * @package App\Http\Controllers
 */
class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    $categorias = Categoria::with('proveedor')->get();
    return view('categoria.index', compact('categorias'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categoria = new Categoria();
        $proveedores = \App\Models\Proveedores::all();
        return view('categoria.create', compact('categoria', 'proveedores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'proveedor_id' => 'nullable|exists:proveedores,id',
        ]);

        $proveedor = Proveedores::find($request->input('proveedor_id'));
        $upc = $proveedor ? $proveedor->upc : null;

        Categoria::create([
            'nombre' => $request->input('nombre'),
            'proveedor_id' => $request->input('proveedor_id'),
            'upc' => $upc,
        ]);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categoria = Categoria::find($id);

        return view('categoria.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Categoria $categoria)
    {
        $proveedores = Proveedores::all();
        return view('categoria.edit', compact('categoria', 'proveedores'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Categoria $categoria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required',
            'proveedor_id' => 'nullable|exists:proveedores,id',
        ]);

        $categoria->update($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }


    public function list()
    {
        $categorias = Categoria::with('proveedor')->get();

        return response()->json([
            'data' => $categorias
        ]);
    }

    public function getCategoriaInfo($id)
    {
        try {
            $categoria = Categoria::with('proveedor')->findOrFail($id);

            if (empty($categoria->upc)) {
                throw new \Exception("La categoría no tiene UPC asignado");
            }

            $upc = str_pad($categoria->upc, 10, '0', STR_PAD_LEFT);
            if (strlen($upc) !== 10) {
                throw new \Exception("El UPC debe tener exactamente 10 dígitos");
            }

            $totalProductos = Producto::where('id_categoria', $id)->count();
            $correlativo = str_pad($totalProductos + 1, 2, '0', STR_PAD_LEFT);

            $codigo13digitos = $upc . $correlativo;

            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int)$codigo13digitos[$i] * ($i % 2 === 0 ? 1 : 3);
            }
            $checkDigit = (10 - ($sum % 10)) % 10;
            $ean13 = $codigo13digitos . $checkDigit;

            if (!$categoria->proveedor) {
                throw new \Exception("La categoría no tiene proveedor asignado");
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'proveedor' => [
                        'id' => $categoria->proveedor->id,
                        'nombre' => $categoria->proveedor->nombre
                    ],
                    'codigo' => $ean13,
                    'codigo_barras' => $ean13,
                    'total_productos' => $totalProductos
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Error en getCategoriaInfo: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    private function generateEAN13($upc)
    {
        $upc = str_pad($upc, 13, '0', STR_PAD_LEFT);

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$upc[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;

        return substr($upc, 0, 12) . $checkDigit;
    }

    public function getFullNameAttribute()
    {
        return "{$this->nombre} [UPC: {$this->upc}]";

    }

}
