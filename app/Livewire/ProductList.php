<?php

namespace App\Livewire;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\Detalleventa;
use Livewire\Component;
use Livewire\WithPagination;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Cliente;


class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 12;
    public $quantity = [];
    public $cartItems = [];
    public $quantitySelector = [];
    public $montoRecibido;
    public $metodoPago = 'efectivo';
    public $cambio = 0;

    public function mount()
    {
        Cart::instance('shopping')->destroy();
        $this->refreshCart();
    }

    protected function getCartContent()
    {
        return Cart::instance('shopping')->content()->map(function ($item) {
            return [
                'rowId'   => $item->rowId,
                'id'      => $item->id,
                'name'    => $item->name,
                'qty'     => $item->qty,
                'price'   => $item->price,
                'options' => $item->options->all()
            ];
        })->toArray();
    }

    public function render()
    {
        $query = Producto::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('producto', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_barras', 'like', '%' . $this->search . '%');
            });
        }

        $products = $query->orderBy('producto')->paginate($this->perPage);

        return view('livewire.product-list', [
            'products' => $products
        ]);
    }

    public function increaseQuantity($productId)
    {
        $this->quantitySelector[$productId] = ($this->quantitySelector[$productId] ?? 1) + 1;
    }

    public function decreaseQuantity($productId)
    {
        if (!isset($this->quantitySelector[$productId])) {
            $this->quantitySelector[$productId] = 1;
        }

        if ($this->quantitySelector[$productId] > 1) {
            $this->quantitySelector[$productId]--;
        }
    }

    public function addToCart($productId)
    {
        $product = Producto::with('promocion')->findOrFail($productId);
        $qty = $this->quantitySelector[$productId] ?? 1;
        $price = $product->precio_venta;
        $promoLabel = $product->promocion->tipo ?? null;

        $existingItem = Cart::instance('shopping')->search(fn($item) => $item->id == $productId)->first();

        if ($existingItem) {
            Cart::instance('shopping')->update($existingItem->rowId, $existingItem->qty + $qty);
        } else {
            Cart::instance('shopping')->add([
                'id' => $product->id,
                'name' => $product->producto,
                'price' => $price,
                'qty' => $qty,
                'options' => [
                    'codigo_barras' => $product->codigo_barras,
                    'foto' => $product->foto,
                    'promocion' => $promoLabel,
                ]
            ]);
        }

        $this->refreshCart();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "$product->producto agregado al carrito"
        ]);
    }

    public function updateQuantity($rowId)
    {
        if (!isset($this->quantity[$rowId])) {
            return;
        }

        if ($this->quantity[$rowId] < 1) {
            $this->removeFromCart($rowId);
            return;
        }

        Cart::instance('shopping')->update($rowId, $this->quantity[$rowId]);
        $this->refreshCart();
    }

    public function removeFromCart($rowId)
    {
        Cart::instance('shopping')->remove($rowId);
        $this->refreshCart();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Producto eliminado del carrito'
        ]);
    }

    public function clearCart()
    {
        Cart::instance('shopping')->destroy();
        $this->refreshCart();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Carrito vaciado correctamente'
        ]);
    }

    #[\Livewire\Attributes\On('cartUpdated')]
    public function refreshCart()
    {
        $items = $this->getCartContent();
        $this->cartItems = $items;

        foreach ($items as $item) {
            $this->quantity[$item['rowId']] = $item['qty'];
        }

        $this->dispatch('cartUpdated');
        $this->dispatch('cartItemsUpdated');
    }

public function procesarVenta($metodo = null, $recibido = null)
{
    if ($metodo !== null) {
        $this->metodoPago = is_array($metodo) ? $metodo['metodo'] : $metodo;
    }

    if ($recibido !== null && !is_array($recibido)) {
        $this->montoRecibido = floatval($recibido);
    }

    try {
        $cart = Cart::instance('shopping');

        if ($cart->count() === 0) {
            return ['success' => false, 'message' => 'El carrito está vacío'];
        }

        $total = $this->calcularTotal();

        // 🟡 SI ES ADEUDO, se crea cliente y detalle_deuda, no venta
        if ($this->metodoPago === 'adeudo' && is_array($recibido)) {
            $cliente = Cliente::create([
                'nombre' => $recibido['nombre'],
                'telefono' => $recibido['telefono'],
                'direccion' => '',
                'fecha_deuda' => now(),
                'deuda_inicial' => $total,
                'total_compra' => $total,
            ]);

            foreach ($cart->content() as $item) {
                \App\Models\DetalleDeuda::create([
                    'cliente_id' => $cliente->id,
                    'producto_id' => $item->id,
                    'precio' => $item->price,
                    'cantidad' => $item->qty,
                    'promocion_aplicada' => $item->options->promocion ?? null,
                ]);
            }

            $cart->destroy();
            $this->refreshCart();

            return [
                'success' => true,
                'cliente_id' => $cliente->id
            ];
        }

        // 🟢 SI ES PAGO NORMAL
        $pagoRecibido = $this->metodoPago === 'efectivo'
            ? ($this->montoRecibido > 0 ? $this->montoRecibido : $total)
            : $total;

        $venta = Venta::create([
            'total' => $total,
            'pago_recibido' => $pagoRecibido,
            'id_usuario' => auth()->id(),
            'metodo_pago' => $this->metodoPago
        ]);

        foreach ($cart->content() as $item) {
            Detalleventa::create([
                'precio' => $item->price,
                'cantidad' => $item->qty,
                'id_producto' => $item->id,
                'id_venta' => $venta->id,
                'promocion_aplicada' => $item->options->promocion ?? null
            ]);
        }

        $cart->destroy();
        $this->refreshCart();

        return [
            'success' => true,
            'ticket' => $venta->id,
            'total' => $total
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}






    private function calcularTotal()
    {
        $total = 0;
        foreach (Cart::instance('shopping')->content() as $item) {
            $total += $this->calcularSubtotalConPromocion($item);
        }
        return $total;
    }

    private function calcularSubtotalConPromocion($item)
    {
        $promo = strtolower($item->options->promocion ?? '');
        $original = $item->price;
        $qty = $item->qty;
        $subtotal = $original * $qty;

        switch ($promo) {
            case '2x1':
                return ($qty > 1) ? ceil($qty / 2) * $original : $subtotal;
            case '3x2':
                $sets = floor($qty / 3);
                $resto = $qty % 3;
                return ($sets * 2 + $resto) * $original;
            case '50%':
            case '50% de descuento':
                return $subtotal * 0.5;
            case 'precio especial':
                return $subtotal * 0.85;
            case 'segunda unidad al 30%':
                $pares = floor($qty / 2);
                $impares = $qty % 2;
                return ($pares * ($original + ($original * 0.3))) + ($impares * $original);
            default:
                return $subtotal;
        }
    }

    public function updatedMontoRecibido()
    {
        if ($this->metodoPago === 'efectivo') {
            $this->cambio = round($this->montoRecibido - $this->calcularTotal(), 2);
        } else {
            $this->cambio = 0;
        }
    }

    public function obtenerDatosCarrito()
    {
        $this->dispatch('datosCarritoActualizados', cartData: $this->cartItems);
    }

    #[\Livewire\Attributes\On('refreshCart')]
    public function syncCart()
    {
        $this->refreshCart();
    }

    public function procesarPago()
    {
        if (count($this->cartItems) === 0) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'El carrito está vacío'
            ]);
            return;
        }

        $this->dispatch('mostrarMetodosPago');
    }

    public function getTotalConPromociones()
    {
        return $this->calcularTotal();
    }
}
